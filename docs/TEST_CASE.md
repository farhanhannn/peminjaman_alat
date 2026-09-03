# Test Case Minimal

| No | Skenario | Input/Kondisi | Hasil yang Diharapkan |
|---:|---|---|---|
| 1 | Login user | Email/password salah | Gagal login dan muncul notifikasi |
| 2 | Tambah alat | Data alat valid | Alat tersimpan dan muncul di daftar |
| 3 | Pinjam alat | Peminjam memilih alat aktif dan qty valid | Pengajuan tersimpan dengan status Menunggu |
| 4 | Kembalikan alat | Peminjaman berstatus Dipinjam | Stok bertambah, status Dikembalikan, denda dihitung |
| 5 | Cek privilege user | Peminjam membuka menu User/Kategori | Akses ditolak/menu admin tidak tersedia |
| 6 | Approval stok tidak cukup | Qty lebih besar dari available_stock | Approval gagal, stok tidak berubah |
| 7 | Tolak pengajuan | Petugas memilih Tolak pada status Menunggu | Status menjadi Ditolak dan log tersimpan |
| 8 | Cetak laporan | Admin/Petugas membuka Cetak | Laporan tampil dan siap print/PDF |
| 9 | Akun nonaktif | `is_active=false` | User tidak dapat masuk panel |
| 10 | Validasi tanggal | Due date sebelum loan date | Validasi gagal |

## Bukti Pengujian

Tambahkan screenshot hasil pengujian pada bagian laporan pengumpulan. Skenario nomor 1–5 adalah minimal yang diwajibkan soal.
