<?php

namespace App\Filament\Widgets;

use App\Models\{Loan, Tool, User};
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        if ($user?->isPeminjam()) {
            return [
                Stat::make('Alat Tersedia', Tool::where('is_active', true)->sum('available_stock')),
                Stat::make('Pengajuan Saya', Loan::where('borrower_id', $user->id)->where('status', 'pending')->count()),
                Stat::make('Sedang Dipinjam', Loan::where('borrower_id', $user->id)->whereIn('status', ['borrowed','overdue'])->count()),
            ];
        }

        return [
            Stat::make('Total Alat', Tool::count()),
            Stat::make('Sedang Dipinjam', Loan::whereIn('status', ['borrowed','overdue'])->count()),
            Stat::make('Menunggu Approval', Loan::where('status', 'pending')->count()),
            Stat::make('Total Peminjam', User::where('role', 'peminjam')->count()),
        ];
    }
}
