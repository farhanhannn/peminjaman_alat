<?php
namespace App\Policies;
use App\Models\{Loan, User};
class LoanPolicy {
    public function viewAny(User $u): bool { return in_array($u->role, ['admin','petugas','peminjam'], true); }
    public function view(User $u, Loan $m): bool { return $u->isAdmin() || $u->isPetugas() || $m->borrower_id === $u->id; }
    public function create(User $u): bool { return in_array($u->role, ['admin','peminjam'], true); }
    public function update(User $u, Loan $m): bool { return $u->isAdmin() || ($u->isPeminjam() && $m->borrower_id === $u->id && $m->status === 'pending'); }
    public function delete(User $u, Loan $m): bool { return $u->isAdmin() && $m->status === 'pending'; }
}
