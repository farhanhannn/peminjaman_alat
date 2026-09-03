# Dokumentasi Aplikasi Peminjaman Alat

## 1. Analisis Kebutuhan

Aplikasi memiliki 3 level pengguna sesuai soal:

| Fitur | Admin | Petugas | Peminjam |
|---|:---:|:---:|:---:|
| Login / Logout | ✓ | ✓ | ✓ |
| CRUD User | ✓ | - | - |
| CRUD Alat | ✓ | - | - |
| CRUD Kategori | ✓ | - | - |
| CRUD Data Peminjaman | ✓ | - | - |
| CRUD Data Pengembalian | ✓ | - | - |
| Log Aktifitas | ✓ | - | - |
| Menyetujui Peminjaman | - | ✓ | - |
| Memantau Pengembalian | - | ✓ | - |
| Mencetak Laporan | - | ✓ | - |
| Melihat daftar alat | - | ✓ | ✓ |
| Mengajukan peminjaman | - | - | ✓ |
| Mengembalikan alat | - | - | ✓ |

**Catatan implementasi:** Admin mempunyai hak CRUD penuh. Petugas mempunyai akses monitoring dan approval. Peminjam hanya melihat alat, membuat pengajuan sendiri, dan mengembalikan peminjaman miliknya. Pengaturan stok dilakukan secara transaksional ketika pengajuan disetujui dan alat dikembalikan.

## 2. Teknologi

- PHP 8.3+
- Laravel 13
- Filament 4
- SQLite untuk pengembangan lokal
- MySQL 8+ sebagai alternatif database deployment
- Blade untuk halaman laporan cetak

## 3. Struktur Data

### users
`id, name, email, role, is_active, password, email_verified_at, remember_token, timestamps`

### categories
`id, name, description, timestamps`

### tools
`id, category_id, code, name, description, stock, available_stock, condition, daily_fine, is_active, timestamps`

### loans
`id, loan_code, borrower_id, tool_id, quantity, loan_date, due_date, return_date, approved_by, returned_by, status, fine, notes, timestamps`

### activity_logs
`id, user_id, module, action, description, ip_address, user_agent, timestamps`

## 4. ERD

Lihat `ERD.md`. Relasi utama:

- `categories 1:N tools`
- `users 1:N loans` melalui `borrower_id`
- `users 1:N loans` melalui `approved_by`
- `users 1:N loans` melalui `returned_by`
- `tools 1:N loans`
- `users 1:N activity_logs`

## 5. Alur Program

### Login
1. User membuka `/admin`.
2. Sistem memvalidasi email dan password melalui autentikasi Filament.
3. Sistem memastikan akun aktif.
4. Setelah login, menu dan operasi dibatasi berdasarkan role.
5. Aktivitas penting dicatat pada `activity_logs`.

### Peminjaman
1. Peminjam membuka menu Peminjaman.
2. Memilih alat aktif dan jumlah.
3. Mengisi tanggal pinjam dan batas pengembalian.
4. Sistem membuat status `pending`.
5. Petugas/admin memeriksa pengajuan.
6. Saat disetujui, transaksi database mengecek `available_stock`.
7. Jika cukup, stok dikurangi dan status menjadi `borrowed`.
8. Jika tidak cukup, transaksi dibatalkan dan pengguna menerima notifikasi.

### Pengembalian dan denda
1. Peminjam/petugas memilih pengembalian.
2. Sistem menghitung keterlambatan dari `due_date` terhadap tanggal pengembalian.
3. Denda = jumlah hari terlambat × denda harian alat × jumlah alat.
4. Stok tersedia ditambah kembali.
5. Status menjadi `returned`, tanggal kembali dan denda disimpan.

## 6. Fungsi, Prosedur, dan Method

- `LoanService::approve()` — approval + pengurangan stok menggunakan transaction.
- `LoanService::reject()` — menolak pengajuan.
- `LoanService::return()` — pengembalian + penghitungan denda + penambahan stok menggunakan transaction.
- `LoanService::log()` — membuat audit log.
- `User::isAdmin()`, `isPetugas()`, `isPeminjam()` — helper role.
- SQL MySQL pada `database/sql/peminjaman_alat_mysql.sql` berisi function `hitung_denda`, procedure `setujui_peminjaman`, trigger validasi, contoh operasi JOIN dan COMMIT/ROLLBACK.

## 7. Coding Guidelines & Best Practices

- Query menggunakan relationship/Eloquent dan `with()` untuk mencegah N+1 pada daftar peminjaman.
- Tabel Filament memiliki search, sort, filter, dan pagination.
- Perubahan stok dan transaksi pengembalian memakai `DB::transaction()`.
- Password di-hash otomatis oleh cast model.
- Hak akses dipisahkan menggunakan Policy dan pembatasan Resource/Action.
- Input diberi validasi required, unique, numeric, min value, dan format email.
- Log aktivitas menyimpan user, modul, aksi, deskripsi, IP, dan user-agent.

## 8. Cara Menjalankan

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka `http://127.0.0.1:8000/admin`.

Akun demo:

| Role | Email | Password |
|---|---|---|
| Admin | admin@peminjaman.test | password |
| Petugas | petugas@peminjaman.test | password |
| Peminjam | peminjam@peminjaman.test | password |

## 9. Laporan

Menu Cetak pada data peminjaman membuka halaman laporan siap print. Gunakan tombol **Cetak / Simpan PDF** pada browser.

## 10. Debugging

Masalah yang umum:

- `APP_KEY` kosong → jalankan `php artisan key:generate`.
- Tabel belum ada → jalankan `php artisan migrate --seed`.
- Cache konfigurasi lama → jalankan `php artisan optimize:clear`.
- Login gagal → pastikan akun seed dan password sesuai tabel akun demo.
- Stok tidak berubah → approval harus melalui aksi **Setujui**, bukan sekadar mengubah status manual.
