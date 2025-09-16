<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [ /* ... */ ];

    public function boot(): void
    {
        // Koorda/Admin/Super boleh review user
        Gate::define('review-user', function (User $user) {
            return $user->roles()->whereIn('name', ['koorda','admin','super_admin'])->exists();
        });

        // Admin/Super akses semua wilayah
        Gate::define('manage-all', function (User $user) {
            return $user->roles()->whereIn('name', ['admin','super_admin'])->exists();
        });
    }
}
