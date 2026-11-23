# E-Complain

Sistem pengaduan/keluhan berbasis web untuk lingkungan sekolah — **Project KIK**. Aplikasi ini adalah hasil migrasi dari PHP native ke **Laravel 13**, dengan tampilan dan alur yang dipertahankan dari aplikasi lama.

## Fitur

### User
- Landing page dan FAQ publik
- Registrasi dan login
- Dashboard pengajuan keluhan (pilih unit tujuan, jenis keluhan, tulis keluhan, unggah lampiran)
- Halaman riwayat dan detail keluhan
- Badge notifikasi untuk tanggapan baru
- Hapus keluhan sendiri

### Admin
- Dashboard ringkasan keluhan
- Daftar keluhan dan detail
- Memberi tanggapan (respons) atas keluhan
- Mengelola/menghapus tanggapan

### Superadmin
- Semua fitur admin
- Manajemen user, admin, unit tujuan, jenis keluhan, dan FAQ
- Laporan keluhan dengan filter dan grafik bulanan
- Cetak laporan
- Command palette untuk navigasi cepat

## Teknologi

- PHP 8.3+ / Laravel 13
- Blade + Tailwind CSS 4 + Vite 8
- SQLite (default) atau MySQL/MariaDB
- PHPUnit 12

## Persyaratan

- PHP 8.3+ dengan ekstensi standar Laravel
- Composer
- Node.js 20+ dan npm

## Instalasi

```bash
composer install
cp .env.example .env          # PowerShell: copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Buka `http://localhost:8000`.

Untuk SQLite, pastikan file `database/database.sqlite` ada:

```bash
# PowerShell
New-Item database/database.sqlite -ItemType File -Force
```

Panduan pindah ke MySQL/MariaDB tersedia di [SETUP_MYSQL.md](SETUP_MYSQL.md).

## Akun Demo

| Username | Password | Role |
|---|---|---|
| `rakadaffa` | `raka123` | user |
| `admin1` | `admin1` | admin |
| `admin123` | `admin123` | superadmin |

Akun di atas berasal dari dump database native (`database/legacy/e_complain.sql`) dan otomatis di-hash saat diimpor.

## Impor Data Legacy (opsional)

```bash
php artisan legacy:import
```

Perintah ini idempoten dan aman dijalankan berulang. Opsi yang tersedia:

```bash
php artisan legacy:import --dry-run
php artisan legacy:import --sql=path/ke/dump.sql --img=path/ke/gambar
```

Lampiran lama disalin ke `storage/app/public/lampiran` (pastikan `php artisan storage:link` sudah dijalankan). Password plaintext dari database lama otomatis di-hash.

## Testing

```bash
php artisan test
```

Test menggunakan SQLite in-memory (lihat `phpunit.xml`), sehingga tidak mengubah database development.

## Struktur Singkat

```
app/
  Console/Commands/      # legacy:import
  Http/Controllers/      # Auth, Landing, User, Admin, Superadmin
  Http/Middleware/       # RoleMiddleware
  Models/                # User, Complain, Response, Tujuan, Jenis, Faq
  Support/               # LegacyImporter
database/
  legacy/                # dump SQL aplikasi native
  migrations/            # skema Laravel
  seeders/               # akun demo + master data
resources/views/         # Blade per role
routes/web.php           # routing berbasis role
tests/Feature/           # test alur aplikasi
```
