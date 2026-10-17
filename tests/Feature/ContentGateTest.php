<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentGateTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('roles')]
    public function test_content_management_requires_admin_role(string $role, bool $allowed): void
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        $this->assertSame($allowed, Gate::forUser($user)->allows('manage-content'));
        $this->assertSame($role !== 'alumni', Gate::forUser($user)->allows('view-alumni'));
        $this->assertSame($role !== 'alumni', Gate::forUser($user)->allows('review-user'));
    }

    public static function roles(): array
    {
        return [
            ['super_admin', true],
            ['admin', true],
            ['koorda', false],
            ['alumni', false],
        ];
    }

    #[DataProvider('inactiveRoles')]
    public function test_inactive_accounts_cannot_use_management_permissions(string $role, string $status): void
    {
        $user = User::factory()->create(['status' => $status]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        foreach (['view-alumni', 'review-user', 'manage-content', 'review-report'] as $ability) {
            $this->assertFalse(Gate::forUser($user)->allows($ability), $ability);
        }
    }

    public static function inactiveRoles(): array
    {
        return [
            ['koorda', User::STATUS_PENDING], ['koorda', User::STATUS_SUSPENDED],
            ['admin', User::STATUS_PENDING], ['admin', User::STATUS_SUSPENDED],
            ['super_admin', User::STATUS_PENDING], ['super_admin', User::STATUS_SUSPENDED],
        ];
    }
}
