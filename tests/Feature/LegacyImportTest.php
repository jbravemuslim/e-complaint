<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\User;
use App\Support\LegacyImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LegacyImportTest extends TestCase
{
    use RefreshDatabase;

    private string $sqlPath;

    private string $imgDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->imgDir = sys_get_temp_dir().'/legacy_img_'.uniqid();
        mkdir($this->imgDir);
        // PNG 1x1 asli agar lolos validasi image bila dipakai.
        file_put_contents(
            $this->imgDir.'/bukti.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==')
        );

        $this->sqlPath = sys_get_temp_dir().'/legacy_'.uniqid().'.sql';
        file_put_contents($this->sqlPath, <<<'SQL'
            CREATE TABLE `akun_admin` (`id_admin` int(11) NOT NULL);
            INSERT INTO `akun_admin` (`id_admin`, `nama_admin`, `username`, `password`, `level`) VALUES
            (3, 'admin1', 'admin123', 'admin123', 'superadmin'),
            (4, 'admin', 'admin1', 'admin1', 'admin');
            INSERT INTO `akun_user` (`id_user`, `nama_user`, `username`, `password`) VALUES
            (1, 'Raka Daffa', 'rakadaffa', 'raka123');
            INSERT INTO `tjn_keluhan` (`id_tujuan`, `nama_unit`) VALUES
            (1, 'Kesiswaan'), (2, 'TU');
            INSERT INTO `jns_keluhan` (`id_jenis`, `nama_jenis`) VALUES
            (1, 'Fasilitas');
            INSERT INTO `faq` (`id_faq`, `pertanyaan`, `jawaban`) VALUES
            (1, 'Cara lapor?', 'Login lalu isi form.');
            INSERT INTO `complain` (`no_antrian`, `id_user`, `keluhan`, `lampiran`, `tgl_keluhan`, `tjn_kategori`, `jns_kategori`, `status`) VALUES
            (10, 1, 'AC rusak, tolong diperbaiki', 'bukti.png', '2026-09-01', 1, 1, 'proses'),
            (11, 1, 'Tanpa gambar', 'noImage.png', '2026-09-02', 2, 1, 'proses'),
            (12, 99, 'User tidak ada', '', '2026-09-03', 1, 1, 'proses');
            INSERT INTO `tindak_lanjut` (`id_lanjut`, `no_antrian`, `id_admin`, `tanggapan`, `tgl_tanggapan`) VALUES
            (1, 10, 4, 'Sudah diperbaiki', '2026-09-02'),
            (2, 99, 4, 'Orphan', '2026-09-03');
            SQL);
    }

    protected function tearDown(): void
    {
        @unlink($this->sqlPath);
        array_map('unlink', glob($this->imgDir.'/*') ?: []);
        @rmdir($this->imgDir);

        parent::tearDown();
    }

    public function test_parse_dump_reads_all_tables(): void
    {
        $parsed = LegacyImporter::parseDump($this->sqlPath);

        $this->assertCount(2, $parsed['akun_admin']);
        $this->assertCount(1, $parsed['akun_user']);
        $this->assertCount(3, $parsed['complain']);
        $this->assertSame('AC rusak, tolong diperbaiki', $parsed['complain'][0][2]);
    }

    public function test_import_is_idempotent_and_hashes_passwords(): void
    {
        Storage::fake('public');

        $first = (new LegacyImporter($this->imgDir))->run($this->sqlPath);

        $this->assertSame(3, $first['imported']['users']);
        $this->assertSame(2, $first['imported']['complains']);
        $this->assertSame(1, $first['imported']['complains_skipped']);
        $this->assertSame(1, $first['imported']['responses']);
        $this->assertSame(1, $first['imported']['responses_skipped']);
        $this->assertSame(1, $first['files_copied']);

        $raka = User::where('username', 'rakadaffa')->first();
        $this->assertTrue(Hash::check('raka123', $raka->password));
        $this->assertSame('user', $raka->role);

        $complain = Complain::find(10); // no_antrian dipertahankan
        $this->assertSame('bukti.png', $complain->lampiran);
        $this->assertSame('Kesiswaan', $complain->tujuan->nama_unit);
        $this->assertSame('Sudah diperbaiki', $complain->response->tanggapan);
        $this->assertNull(Complain::find(11)->lampiran); // noImage.png -> null
        Storage::disk('public')->assertExists('lampiran/bukti.png');

        // Dijalankan ulang: tidak ada duplikat.
        $second = (new LegacyImporter($this->imgDir))->run($this->sqlPath);
        $this->assertSame(3, User::count());
        $this->assertSame(2, Complain::count());
        $this->assertSame(0, $second['imported']['users']);
        $this->assertTrue(Hash::check('raka123', $raka->fresh()->password));
    }

    public function test_import_real_dump_seeds_three_accounts(): void
    {
        $result = (new LegacyImporter(null))->run(base_path('database/legacy/e_complain.sql'));

        $this->assertSame(3, $result['imported']['users']);
        $this->assertSame(0, $result['imported']['complains']); // tabel legacy kosong
        $this->assertTrue(Hash::check('admin123', User::where('username', 'admin123')->first()->password));
        $this->assertSame('superadmin', User::where('username', 'admin123')->first()->role);
    }
}
