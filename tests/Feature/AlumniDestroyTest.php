<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_alumni_user(): void
    {
        $admin = $this->userWithRole('admin', ['email' => 'admin-delete@example.test']);
        $alumni = $this->userWithRole('alumni', ['email' => 'hapus.alumni@example.test']);

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $alumni));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.index', absolute: false))
            ->assertSessionHas('success');

        $this->assertNull($alumni->fresh());
        $this->assertDatabaseMissing('role_user', ['user_id' => $alumni->id]);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_delete_koorda_user(): void
    {
        $admin = $this->userWithRole('admin', ['email' => 'admin-delete-koorda@example.test']);
        $koorda = $this->userWithRole('koorda', ['email' => 'hapus.koorda@example.test']);

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $koorda));

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.index', absolute: false))
            ->assertSessionHas('success');

        $this->assertNull($koorda->fresh());
        $this->assertDatabaseMissing('role_user', ['user_id' => $koorda->id]);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_super_admin_can_delete_alumni_or_koorda_user(): void
    {
        $superAdmin = $this->userWithRole('super_admin', ['email' => 'super-delete@example.test']);
        $alumni = $this->userWithRole('alumni', ['email' => 'hapus.alumni.super@example.test']);
        $koorda = $this->userWithRole('koorda', ['email' => 'hapus.koorda.super@example.test']);

        $this
            ->actingAs($superAdmin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $alumni))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.index', absolute: false));

        $this
            ->actingAs($superAdmin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $koorda))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertNull($alumni->fresh());
        $this->assertNull($koorda->fresh());
        $this->assertAuthenticatedAs($superAdmin);
    }

    public function test_admin_cannot_delete_their_own_account_from_alumni_list(): void
    {
        $admin = $this->userWithRole('admin', ['email' => 'self-delete@example.test']);

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $admin));

        $response
            ->assertSessionHasErrors('delete')
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertNotNull($admin->fresh());
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_cannot_delete_admin_or_super_admin_user_from_alumni_list(): void
    {
        $admin = $this->userWithRole('admin', ['email' => 'admin-actor@example.test']);
        $otherAdmin = $this->userWithRole('admin', ['email' => 'other-admin-delete@example.test']);
        $superAdmin = $this->userWithRole('super_admin', ['email' => 'super-protected@example.test']);

        $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $otherAdmin))
            ->assertSessionHasErrors('delete')
            ->assertRedirect(route('alumni.index', absolute: false));

        $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $superAdmin))
            ->assertSessionHasErrors('delete')
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertNotNull($otherAdmin->fresh());
        $this->assertNotNull($superAdmin->fresh());
    }

    public function test_koorda_cannot_delete_alumni_or_koorda_user(): void
    {
        $koordaActor = $this->userWithRole('koorda', ['email' => 'koorda-actor@example.test']);
        $alumni = $this->userWithRole('alumni', ['email' => 'protected-alumni@example.test']);

        $response = $this
            ->actingAs($koordaActor)
            ->from(route('alumni.index'))
            ->delete(route('alumni.destroy', $alumni));

        $response
            ->assertSessionHasErrors('delete')
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertNotNull($alumni->fresh());
        $this->assertAuthenticatedAs($koordaActor);
    }

    public function test_admin_sees_delete_button_for_alumni_and_koorda_only(): void
    {
        $admin = $this->userWithRole('admin', ['email' => 'admin-buttons@example.test']);
        $alumni = $this->userWithRole('alumni', ['name' => 'Alumni Bisa Hapus', 'email' => 'button-alumni@example.test']);
        $koorda = $this->userWithRole('koorda', ['name' => 'Koorda Bisa Hapus', 'email' => 'button-koorda@example.test']);
        $otherAdmin = $this->userWithRole('admin', ['name' => 'Admin Aman', 'email' => 'button-admin@example.test']);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.index'));

        $response
            ->assertOk()
            ->assertSee('action="' . route('alumni.destroy', $alumni) . '"', false)
            ->assertSee('action="' . route('alumni.destroy', $koorda) . '"', false)
            ->assertDontSee('action="' . route('alumni.destroy', $otherAdmin) . '"', false);
    }

    private function userWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        $user = User::factory()->create(array_merge([
            'status' => User::STATUS_ACTIVE,
        ], $attributes));

        $user->roles()->attach($role->id);

        return $user;
    }
}
