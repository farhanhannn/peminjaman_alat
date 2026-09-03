# Flowchart / Pseudocode

## Login

```text
START
  ↓
Input email + password
  ↓
Validasi akun
  ├─ Salah → tampilkan notifikasi gagal → END
  └─ Benar
       ↓
   Cek is_active
       ├─ Tidak aktif → tolak akses → END
       └─ Aktif → login
                    ↓
              Ambil role user
                    ↓
              Tampilkan dashboard
                    ↓
                   END
```

## Peminjaman

```text
START
  ↓
Peminjam memilih alat + jumlah + tanggal
  ↓
Validasi data
  ↓
Simpan pengajuan status PENDING
  ↓
Petugas memeriksa pengajuan
  ↓
Stok tersedia?
  ├─ Tidak → TOLAK / notifikasi
  └─ Ya → TRANSACTION
            ↓
        Kurangi available_stock
            ↓
        Status BORROWED
            ↓
           COMMIT
            ↓
           END
```

## Pengembalian + denda

```text
START
  ↓
Pilih peminjaman aktif
  ↓
Tanggal kembali = hari ini
  ↓
Hari terlambat = max(0, tanggal kembali - due date)
  ↓
Denda = hari terlambat × denda/hari × jumlah
  ↓
TRANSACTION
  ├─ Tambah available_stock
  ├─ Status RETURNED
  ├─ Simpan return_date
  └─ Simpan fine
  ↓
COMMIT
  ↓
Tampilkan hasil + denda
  ↓
END
```
