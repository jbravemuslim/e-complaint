<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Impor data akun dari database native (e_complain.sql, dump 21 Sep 2026).
 *
 * Password di database lama tersimpan plaintext, di sini langsung di-hash
 * oleh cast 'hashed' di model User sehingga pemilik akun tetap login
 * dengan password yang sama seperti dulu.
 *
 * Tabel complain / tindak_lanjut / tjn_keluhan / jns_keluhan / faq di dump
 * dalam keadaan kosong sehingga tidak ada yang perlu diimpor.
 */
class LegacyImportSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // akun_admin: (3, 'admin1', 'admin123', 'admin123', 'superadmin')
        User::updateOrCreate(
            ['username' => 'admin123'],
            ['name' => 'admin1', 'password' => 'admin123', 'role' => 'superadmin']
        );

        // akun_admin: (4, 'admin', 'admin1', 'admin1', 'admin')
        User::updateOrCreate(
            ['username' => 'admin1'],
            ['name' => 'admin', 'password' => 'admin1', 'role' => 'admin']
        );

        // akun_user: (1, 'Raka Daffa', 'rakadaffa', 'raka123')
        User::updateOrCreate(
            ['username' => 'rakadaffa'],
            ['name' => 'Raka Daffa', 'password' => 'raka123', 'role' => 'user']
        );
    }
}
