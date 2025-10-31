<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('view-alumni', fn($user) => $user->isAdminLike());
        Gate::define('manage-content', fn($user) => $user->isAdminLike());
        Gate::define('review-user', fn($user) => $user->isAdminLike());
        Gate::define('review-report', fn($user) => $user->isAdminLike());
    }
}
