<?php
namespace App\Policies;
use App\Models\{Tool, User};
class ToolPolicy { public function viewAny(User $u): bool { return in_array($u->role, ['admin','petugas','peminjam'], true); } public function view(User $u, Tool $m): bool { return $this->viewAny($u); } public function create(User $u): bool { return $u->isAdmin(); } public function update(User $u, Tool $m): bool { return $u->isAdmin(); } public function delete(User $u, Tool $m): bool { return $u->isAdmin(); } }
