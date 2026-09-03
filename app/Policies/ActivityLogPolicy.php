<?php
namespace App\Policies;
use App\Models\{ActivityLog, User};
class ActivityLogPolicy { public function viewAny(User $u): bool { return $u->isAdmin(); } public function view(User $u, ActivityLog $m): bool { return $u->isAdmin(); } }
