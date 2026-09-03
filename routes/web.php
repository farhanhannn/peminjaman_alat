<?php

use App\Models\Loan;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::middleware('auth')->group(function () {
    Route::get('/reports/loans', function () {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isPetugas(), 403);
        $loans = Loan::with(['borrower', 'tool', 'approver', 'returner'])->latest()->get();
        return view('reports.loans', compact('loans'));
    })->name('reports.loans');
});
