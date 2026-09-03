# Aplikasi Peminjaman Alat

Project tugas pengembangan aplikasi peminjaman alat dengan Laravel 13 + Filament 4.

Dokumentasi lengkap berada di folder `docs/`:

- `DOKUMENTASI.md`
- `ERD.md`
- `FLOWCHART.md`
- `TEST_CASE.md`
- `EVALUASI.md`
- `../database/sql/peminjaman_alat_mysql.sql`

## Quick Start

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Login di `/admin` menggunakan:

- Admin: `admin@peminjaman.test` / `password`
- Petugas: `petugas@peminjaman.test` / `password`
- Peminjam: `peminjam@peminjaman.test` / `password`
