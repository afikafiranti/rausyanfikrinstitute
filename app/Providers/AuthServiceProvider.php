<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('view-alumni', fn($user) => $user->status === User::STATUS_ACTIVE && $user->isAdminLike());
        Gate::define('manage-content', fn($user) => $user->status === User::STATUS_ACTIVE && $user->hasAnyRole(['super_admin', 'admin']));
        Gate::define('review-user', fn($user) => $user->status === User::STATUS_ACTIVE && $user->isAdminLike());
        Gate::define('review-report', fn($user) => $user->status === User::STATUS_ACTIVE && $user->isAdminLike());
    }
}
