<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Jenis;
use App\Models\Tujuan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun asli dari database native (lihat LegacyImportSeeder).
        $this->call(LegacyImportSeeder::class);

        // Master data contoh — di database lama tabelnya kosong,
        // tapi form pengajuan butuh data ini agar bisa dipakai.
        foreach (['Kurikulum', 'Kesiswaan', 'Sarana Prasarana', 'TU'] as $unit) {
            Tujuan::firstOrCreate(['nama_unit' => $unit]);
        }

        foreach (['Akademik', 'Fasilitas', 'Administrasi', 'Lainnya'] as $nama) {
            Jenis::firstOrCreate(['nama_jenis' => $nama]);
        }

        Faq::firstOrCreate(
            ['pertanyaan' => 'Bagaimana cara mengajukan keluhan?'],
            ['jawaban' => 'Login sebagai user, pilih tujuan dan jenis keluhan, tulis keluhan, lalu klik Kirim.']
        );
        Faq::firstOrCreate(
            ['pertanyaan' => 'Berapa lama keluhan diproses?'],
            ['jawaban' => 'Keluhan berstatus proses akan ditanggapi oleh admin, lalu status berubah menjadi selesai.']
        );
    }
}
