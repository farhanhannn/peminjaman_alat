# Laporan Evaluasi Singkat

## Fitur yang berjalan

1. Login/logout dan pembatasan role.
2. CRUD user, kategori, dan alat untuk Admin.
3. Pengajuan peminjaman oleh Peminjam.
4. Approval/reject oleh Admin/Petugas.
5. Pengembalian dan perhitungan denda.
6. Stok tersedia otomatis berkurang/bertambah.
7. Log aktivitas.
8. Laporan peminjaman siap dicetak.
9. Database migration dan SQL MySQL dengan function, procedure, trigger, transaction.

## Bug/risiko yang perlu diperhatikan

- Instalasi lokal membutuhkan extension PHP yang sesuai dengan driver database yang dipilih.
- SQL procedure/function pada folder `database/sql` ditujukan untuk MySQL 8+, sedangkan migration default dapat berjalan dengan SQLite.
- Untuk deployment produksi, tambahkan backup database, HTTPS, rate limiting login, dan pengaturan password policy yang lebih ketat.

## Rencana pengembangan berikutnya

- Notifikasi email/WhatsApp saat pengajuan disetujui atau mendekati jatuh tempo.
- Dashboard grafik statistik peminjaman.
- Upload foto alat dan bukti pengembalian.
- Multi-item dalam satu nomor transaksi peminjaman.
- Export laporan Excel/PDF otomatis.
- Pengelolaan denda dan pembayaran denda.
