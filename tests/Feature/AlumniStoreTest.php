<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_pending_alumni_with_alumni_role(): void
    {
        $admin = $this->adminUser();

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->post(route('alumni.store'), [
                'name' => 'Alumni Baru',
                'email' => 'alumni.baru@example.test',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertAuthenticatedAs($admin);

        $alumni = User::where('email', 'alumni.baru@example.test')->firstOrFail();

        $this->assertSame('Alumni Baru', $alumni->name);
        $this->assertSame(User::STATUS_PENDING, $alumni->status);
        $this->assertTrue($alumni->hasRole('alumni'));
    }

    public function test_alumni_email_must_be_unique(): void
    {
        $admin = $this->adminUser();
        User::factory()->create(['email' => 'existing@example.test']);

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->post(route('alumni.store'), [
                'name' => 'Alumni Duplikat',
                'email' => 'existing@example.test',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
            ]);

        $response
            ->assertSessionHasErrors('email')
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertSame(1, User::where('email', 'existing@example.test')->count());
    }

    public function test_alumni_password_confirmation_must_match(): void
    {
        $admin = $this->adminUser();

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->post(route('alumni.store'), [
                'name' => 'Alumni Password Salah',
                'email' => 'password.salah@example.test',
                'password' => 'Password123',
                'password_confirmation' => 'Password456',
            ]);

        $response
            ->assertSessionHasErrors('password')
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertNull(User::where('email', 'password.salah@example.test')->first());
    }

    private function adminUser(): User
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'status' => User::STATUS_ACTIVE,
        ]);

        $admin->roles()->attach($adminRole->id);

        return $admin;
    }
}
