<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{ActivityLog, Category, Loan, Tool, User};
use App\Policies\{ActivityLogPolicy, CategoryPolicy, LoanPolicy, ToolPolicy, UserPolicy};

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Tool::class, ToolPolicy::class);
        Gate::policy(Loan::class, LoanPolicy::class);
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
    }
}
