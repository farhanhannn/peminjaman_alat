<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public static function approve(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loan->load('tool');
            if ($loan->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Hanya pengajuan berstatus menunggu yang dapat disetujui.']);
            }
            if (! $loan->tool->is_active || $loan->tool->available_stock < $loan->quantity) {
                throw ValidationException::withMessages(['tool_id' => 'Stok alat tidak mencukupi.']);
            }

            $loan->tool->decrement('available_stock', $loan->quantity);
            $loan->update([
                'status' => 'borrowed',
                'approved_by' => auth()->id(),
            ]);
            self::log('peminjaman', 'approve', "Pengajuan {$loan->loan_code} disetujui.");
            return $loan->refresh();
        });
    }

    public static function reject(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            if ($loan->status !== 'pending') {
                throw ValidationException::withMessages(['status' => 'Hanya pengajuan berstatus menunggu yang dapat ditolak.']);
            }
            $loan->update(['status' => 'rejected', 'approved_by' => auth()->id()]);
            self::log('peminjaman', 'reject', "Pengajuan {$loan->loan_code} ditolak.");
            return $loan->refresh();
        });
    }

    public static function return(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loan->load('tool');
            if (! in_array($loan->status, ['borrowed', 'overdue'], true)) {
                throw ValidationException::withMessages(['status' => 'Peminjaman ini tidak sedang dipinjam.']);
            }

            $returnDate = now()->startOfDay();
            $lateDays = max(0, $returnDate->diffInDays($loan->due_date, false) * -1);
            $fine = $lateDays * (float) $loan->tool->daily_fine * $loan->quantity;

            $loan->tool->increment('available_stock', $loan->quantity);
            $loan->update([
                'status' => 'returned',
                'return_date' => $returnDate,
                'returned_by' => auth()->id(),
                'fine' => $fine,
            ]);
            self::log('pengembalian', 'return', "Alat pada {$loan->loan_code} dikembalikan. Denda: Rp " . number_format($fine, 0, ',', '.') . '.');
            return $loan->refresh();
        });
    }

    public static function log(string $module, string $action, string $description): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
