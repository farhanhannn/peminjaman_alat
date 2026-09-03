<?php
namespace App\Policies;
use App\Models\{Category, User};
class CategoryPolicy { public function viewAny(User $u): bool { return $u->isAdmin(); } public function view(User $u, Category $m): bool { return $u->isAdmin(); } public function create(User $u): bool { return $u->isAdmin(); } public function update(User $u, Category $m): bool { return $u->isAdmin(); } public function delete(User $u, Category $m): bool { return $u->isAdmin(); } }
