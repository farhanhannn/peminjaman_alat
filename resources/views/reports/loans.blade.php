<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        body{font-family:Arial,sans-serif;font-size:12px;margin:30px;color:#111}.header{text-align:center;margin-bottom:20px}.header h1{margin:0;font-size:20px}.header p{margin:4px}.meta{margin-bottom:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #333;padding:6px;text-align:left}th{text-align:center;background:#eee}.right{text-align:right}@media print{.no-print{display:none}}
    </style>
</head>
<body>
<div class="no-print" style="text-align:right;margin-bottom:15px"><button onclick="window.print()">Cetak / Simpan PDF</button></div>
<div class="header"><h1>LAPORAN PEMINJAMAN ALAT</h1><p>Aplikasi Pengelolaan Peminjaman Alat</p></div>
<div class="meta">Dicetak: {{ now()->format('d/m/Y H:i') }} | Petugas: {{ auth()->user()->name }}</div>
<table>
<thead><tr><th>No</th><th>Kode</th><th>Peminjam</th><th>Alat</th><th>Qty</th><th>Pinjam</th><th>Jatuh Tempo</th><th>Kembali</th><th>Status</th><th>Denda</th></tr></thead>
<tbody>
@forelse($loans as $i=>$loan)
<tr><td>{{ $i+1 }}</td><td>{{ $loan->loan_code }}</td><td>{{ $loan->borrower->name }}</td><td>{{ $loan->tool->name }}</td><td>{{ $loan->quantity }}</td><td>{{ $loan->loan_date?->format('d/m/Y') }}</td><td>{{ $loan->due_date?->format('d/m/Y') }}</td><td>{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td><td>{{ ucfirst($loan->status) }}</td><td class="right">Rp {{ number_format((float)$loan->fine,0,',','.') }}</td></tr>
@empty
<tr><td colspan="10" style="text-align:center">Belum ada data.</td></tr>
@endforelse
</tbody></table>
</body></html>
