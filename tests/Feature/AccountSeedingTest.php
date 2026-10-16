<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSeedingTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seed_in_production_creates_reference_data_without_demo_accounts(): void
    {
        app()->detectEnvironment(fn () => 'production');

        try {
            $this->artisan('db:seed', ['--force' => true])->assertExitCode(0);
            $this->artisan('db:seed', ['--class' => UserSeeder::class, '--force' => true])->assertExitCode(0);

            $this->assertSame(4, Role::count());
            $this->assertSame(4, Level::count());
            $this->assertSame(0, User::count());
            $this->assertDatabaseCount('posts', 0);
            $this->assertDatabaseCount('galleries', 0);
        } finally {
            app()->detectEnvironment(fn () => 'testing');
        }
    }

    public function test_reseeding_preserves_existing_account_role_and_learning_level(): void
    {
        $this->artisan('db:seed')->assertExitCode(0);

        $admin = User::where('email', 'arif@rausyanfikr.com')->firstOrFail();
        $admin->update(['name' => 'Updated Administrator', 'password' => Hash::make('private-password')]);
        $admin->roles()->detach();
        $level = Level::findOrFail($admin->level_id);
        $level->update(['description' => 'custom akhir']);

        $this->artisan('db:seed')->assertExitCode(0);

        $admin->refresh();
        $this->assertSame('Updated Administrator', $admin->name);
        $this->assertTrue(Hash::check('private-password', $admin->password));
        $this->assertCount(0, $admin->roles);
        $this->assertSame('custom akhir', $level->refresh()->description);
        $this->assertSame(1, User::where('email', 'arif@rausyanfikr.com')->count());
    }

    public function test_user_seed_works_when_existing_role_has_a_different_id(): void
    {
        $adminRole = Role::create(['name' => 'admin']);

        $this->artisan('db:seed', ['--class' => UserSeeder::class])->assertExitCode(0);

        $this->assertSame(4, Role::whereIn('name', ['alumni', 'koorda', 'admin', 'super_admin'])->count());
        $this->assertSame($adminRole->id, User::where('email', 'arif@rausyanfikr.com')->firstOrFail()->roles()->firstOrFail()->id);
    }
}
