# Setup MySQL — E-Complain

Panduan sekali jalan untuk memindahkan aplikasi dari SQLite (dev)
ke MySQL/MariaDB (database lama `e_complain`).

## 1. Prasyarat

- Terminal **Administrator** (install/start service DB butuh elevasi).
- PHP 8.3+, Composer, Node 20+ (sudah ada di mesin dev).

## 2. Install server database (pilih salah satu)

```powershell
# Opsi A — MySQL resmi (≈500 MB)
winget install Oracle.MySQL.8.4

# Opsi B — Docker (butuh Docker Desktop berjalan)
docker run -d --name ecomplain-mysql `
  -e MYSQL_ROOT_PASSWORD=root `
  -e MYSQL_DATABASE=e_complain `
  -e MYSQL_CHARACTER_SET_SERVER=utf8mb4 `
  -e MYSQL_COLLATION_SERVER=utf8mb4_unicode_ci `
  -p 3306:3306 mysql:8.4

# Opsi C — Laragon / XAMPP (GUI, termudah di Windows)
# Buat database via phpMyAdmin / HeidiSQL setelah install.
```

## 3. Buat database (lewati jika pakai Opsi B)

```powershell
mysql -u root -p -e "CREATE DATABASE e_complain CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 4. Konfigurasi `.env`

Salin dari contoh bila belum ada, lalu aktifkan blok MySQL:

```powershell
copy .env.example .env
php artisan key:generate
```

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_complain
DB_USERNAME=root
DB_PASSWORD=isi-password-root
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

> Kembali ke SQLite kapan saja: `DB_CONNECTION=sqlite` (hapus baris `DB_*` MySQL).

## 5. Migrasi + impor data legacy

Jalankan dari root proyek (`e_complain/`):

```powershell
php artisan migrate --force
php artisan legacy:import
php artisan db:seed --force
php artisan storage:link
```

Artinya tiap perintah:

| Perintah | Fungsi |
|---|---|
| `migrate --force` | Buat ulang skema Laravel (`users`, `complains`, `responses`, …) |
| `legacy:import` | Impor `database/legacy/e_complain.sql` — idempoten, aman diulang. Password plaintext lama otomatis di-hash. Opsi: `--dry-run`, `--sql=...`, `--img=...` |
| `db:seed --force` | Akun bawaan + master `tujuan`/`jenis`/`faq` contoh |
| `storage:link` | Lampiran `storage/app/public/lampiran` bisa diakses via `/storage` |

## 6. Verifikasi

```powershell
php artisan test            # tetap pakai SQLite :memory:, tidak terpengaruh .env
php artisan legacy:import   # users: 0 = tidak ada duplikat (idempoten)
```

Login di `http://localhost:8000/login`:

| Username | Password | Role |
|---|---|---|
| `rakadaffa` | `raka123` | user |
| `admin1` | `admin1` | admin |
| `admin123` | `admin123` | superadmin |

Lalu: `php artisan serve` dan `npm run dev` (atau `npm run build` untuk produksi).

## 7. Troubleshooting

| Gejala | Solusi |
|---|---|
| `Access denied for user 'root'` | Cek `DB_PASSWORD`; reset: `mysqladmin -u root password BARU` atau via MySQL Installer |
| `Connection refused` / `port 3306` | Service belum jalan: `services.msc` → start `MySQL84`. Cek bentrok: `netstat -ano \| findstr 3306` |
| `Unknown database 'e_complain'` | Ulangi langkah 3 (nama harus persis `e_complain`) |
| `Table '...' already exists` | Database sudah terisi skema lama: backup dulu, atau `php artisan migrate:fresh --force` (**hapus semua data**) |
| `SQLSTATE[HY000] [2002]` | Host/port salah, atau server belum start |
| `Incorrect string value` / `???` | Pastikan DB `utf8mb4` (langkah 3) dan `DB_CHARSET/COLLATION` di `.env` |
| Lampiran 404 | Jalankan `php artisan storage:link`; pastikan folder `storage/app/public/lampiran` ada |
| `legacy:import` lapor `files_missing` | File tidak ada di `legacy_native_backup/img/` — warning saja, data tetap masuk dengan nama file tercatat |
| Kembali ke SQLite | Set `DB_CONNECTION=sqlite`, hapus/beri `#` pada baris `DB_HOST`…`DB_COLLATION` |
