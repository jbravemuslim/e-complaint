<?php

namespace Tests\Feature;

use App\Models\Complain;
use App\Models\Jenis;
use App\Models\Response;
use App\Models\Tujuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $admin;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        $tujuan = Tujuan::create(['nama_unit' => 'Kesiswaan']);
        $jenis = Jenis::create(['nama_jenis' => 'Fasilitas']);

        foreach (['2026-08-01' => 'Lama', '2026-09-15' => 'Tengah', '2026-10-01' => 'Baru'] as $tgl => $label) {
            $c = Complain::create([
                'user_id' => $this->user->id, 'keluhan' => "Keluhan {$label}",
                'lampiran' => null, 'tgl_keluhan' => $tgl,
                'tujuan_id' => $tujuan->id, 'jenis_id' => $jenis->id,
                'status' => 'selesai',
            ]);
            Response::create([
                'complain_id' => $c->id, 'admin_id' => $this->admin->id,
                'tanggapan' => 'OK', 'tgl_tanggapan' => $tgl,
            ]);
        }
    }

    public function test_laporan_filters_by_date_range(): void
    {
        $this->actingAs($this->superadmin)->get('/superadmin/laporan')->assertOk()
            ->assertSee('Keluhan Lama', false)
            ->assertSee('Keluhan Baru', false);

        $this->actingAs($this->superadmin)
            ->get('/superadmin/laporan?dari=2026-09-01&sampai=2026-09-30')->assertOk()
            ->assertSee('Keluhan Tengah', false)
            ->assertDontSee('Keluhan Lama', false)
            ->assertDontSee('Keluhan Baru', false);

        // Cetak mengikuti filter yang sama (cetak hanya tampilkan tanggal, bukan isi keluhan).
        $this->actingAs($this->superadmin)
            ->get('/superadmin/laporan/cetak?dari=2026-09-01&sampai=2026-09-30')->assertOk()
            ->assertSee('2026-09-15', false)
            ->assertSee('Periode: 2026-09-01 s/d 2026-09-30', false)
            ->assertDontSee('2026-08-01', false)
            ->assertDontSee('2026-10-01', false);
    }

    public function test_laporan_rejects_inverted_range_and_empty_state(): void
    {
        $this->actingAs($this->superadmin)
            ->get('/superadmin/laporan?dari=2026-10-01&sampai=2026-09-01')
            ->assertSessionHasErrors('sampai');

        $this->actingAs($this->superadmin)
            ->get('/superadmin/laporan?dari=2027-01-01&sampai=2027-01-31')->assertOk()
            ->assertSee('Tidak ada laporan pada periode ini.', false);
    }
}
