<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_pending_alumni_with_alumni_role(): void
    {
        $admin = $this->adminUser();
        $region = Wilayah::create(['name' => 'Wilayah Alumni']);

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.index'))
            ->post(route('alumni.store'), [
                'name' => 'Alumni Baru',
                'email' => 'alumni.baru@example.test',
                'password' => 'Password123',
                'password_confirmation' => 'Password123',
                'wilayah_id' => $region->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.index', absolute: false));

        $this->assertAuthenticatedAs($admin);

        $alumni = User::where('email', 'alumni.baru@example.test')->firstOrFail();

        $this->assertSame('Alumni Baru', $alumni->name);
        $this->assertSame(User::STATUS_PENDING, $alumni->status);
        $this->assertTrue($alumni->hasRole('alumni'));
        $this->assertSame($region->id, $alumni->wilayah_id);
    }

    public function test_koorda_creates_alumni_in_their_region_even_if_other_region_is_submitted(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Sendiri']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $koorda = $this->adminUser();
        $koorda->update(['wilayah_id' => $region->id]);
        $koorda->roles()->sync([Role::firstOrCreate(['name' => 'koorda'])->id]);

        $this->actingAs($koorda)->post(route('alumni.store'), [
            'name' => 'Alumni Koorda', 'email' => 'new@example.test',
            'password' => 'Password123', 'password_confirmation' => 'Password123', 'wilayah_id' => $other->id,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertAuthenticatedAs($koorda);
        $alumni = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertSame($region->id, $alumni->wilayah_id);
        $this->get(route('alumni.index'))->assertOk()->assertSee('Alumni Koorda');
        $this->get(route('verification.index'))->assertOk()->assertSee('Alumni Koorda');
    }

    public function test_admin_must_select_a_valid_region_when_creating_alumni(): void
    {
        $admin = $this->adminUser();
        foreach ([null, 999999] as $regionId) {
            $this->actingAs($admin)->post(route('alumni.store'), [
                'name' => 'Alumni Baru', 'email' => 'new@example.test',
                'password' => 'Password123', 'password_confirmation' => 'Password123', 'wilayah_id' => $regionId,
            ])->assertSessionHasErrors('wilayah_id');
        }
        $this->assertDatabaseMissing('users', ['email' => 'new@example.test']);
    }

    public function test_koorda_without_region_cannot_create_alumni(): void
    {
        $koorda = $this->adminUser();
        $koorda->roles()->sync([Role::firstOrCreate(['name' => 'koorda'])->id]);

        $this->actingAs($koorda)->post(route('alumni.store'), [
            'name' => 'Alumni Baru', 'email' => 'new@example.test',
            'password' => 'Password123', 'password_confirmation' => 'Password123',
        ])->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'new@example.test']);
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
