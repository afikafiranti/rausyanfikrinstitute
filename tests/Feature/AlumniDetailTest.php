<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\LevelSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_level_seeder_does_not_use_system_roles_as_learning_levels(): void
    {
        $this->seed(LevelSeeder::class);

        $descriptions = Level::query()
            ->orderBy('id')
            ->pluck('description')
            ->map(fn ($description) => strtolower((string) $description));

        $this->assertSame(['dasar', 'menengah', 'lanjutan', 'akhir'], $descriptions->all());
        $this->assertFalse($descriptions->contains('superadmin'));
        $this->assertFalse($descriptions->contains('super_admin'));
        $this->assertFalse($descriptions->contains('admin'));
        $this->assertFalse($descriptions->contains('koordinator'));
        $this->assertFalse($descriptions->contains('koorda'));
        $this->assertFalse($descriptions->contains('alumni'));
    }

    public function test_user_seeder_sets_admin_and_super_admin_to_final_learning_level(): void
    {
        $this->seed(WilayahSeeder::class);
        $this->seed(LevelSeeder::class);
        $this->seed(UserSeeder::class);

        $finalLevel = Level::where('description', 'akhir')->firstOrFail();

        $superAdmin = User::where('email', 'afika@rausyanfikr.com')->firstOrFail();
        $admin = User::where('email', 'arif@rausyanfikr.com')->firstOrFail();

        $this->assertSame($finalLevel->id, $superAdmin->level_id);
        $this->assertSame($finalLevel->id, $admin->level_id);
    }

    public function test_alumni_detail_separates_account_role_from_learning_level(): void
    {
        $adminRole = Role::create(['name' => 'super_admin']);
        $alumniRole = Role::create(['name' => 'alumni']);
        $level = Level::create([
            'name' => 'Level 1',
            'description' => 'dasar',
            'is_active' => true,
        ]);

        $admin = User::factory()->create();
        $admin->roles()->attach($adminRole->id);

        $alumni = User::factory()->create([
            'name' => 'Andi Puji Lestari',
            'level_id' => $level->id,
            'status' => User::STATUS_ACTIVE,
        ]);
        $alumni->roles()->attach($alumniRole->id);

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.show', $alumni));

        $response
            ->assertOk()
            ->assertSee('Role Akun')
            ->assertSee('alumni')
            ->assertSee('Level Materi')
            ->assertSee('dasar')
            ->assertDontSee('superadmin');
    }

    public function test_admin_can_update_alumni_learning_level(): void
    {
        $admin = $this->userWithRole('admin');
        $alumni = $this->userWithRole('alumni', ['level_id' => $this->level('Level 1', 'Dasar')->id]);
        $newLevel = $this->level('Level 2', 'Menengah');

        $response = $this
            ->actingAs($admin)
            ->from(route('alumni.show', $alumni))
            ->patch(route('alumni.level.update', $alumni), [
                'level_id' => $newLevel->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.show', $alumni, absolute: false))
            ->assertSessionHas('success');

        $this->assertSame($newLevel->id, $alumni->refresh()->level_id);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'alumni.level.update',
            'entity_type' => 'user',
            'entity_id' => $alumni->id,
        ]);
    }

    public function test_super_admin_can_update_koorda_learning_level(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $koorda = $this->userWithRole('koorda', ['level_id' => $this->level('Level 1', 'Dasar')->id]);
        $newLevel = $this->level('Level 3', 'Lanjutan');

        $response = $this
            ->actingAs($superAdmin)
            ->from(route('alumni.show', $koorda))
            ->patch(route('alumni.level.update', $koorda), [
                'level_id' => $newLevel->id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('alumni.show', $koorda, absolute: false));

        $this->assertSame($newLevel->id, $koorda->refresh()->level_id);
    }

    public function test_koorda_cannot_update_learning_level(): void
    {
        $koordaActor = $this->userWithRole('koorda');
        $alumni = $this->userWithRole('alumni', ['level_id' => $this->level('Level 1', 'Dasar')->id]);
        $newLevel = $this->level('Level 2', 'Menengah');

        $response = $this
            ->actingAs($koordaActor)
            ->from(route('alumni.show', $alumni))
            ->patch(route('alumni.level.update', $alumni), [
                'level_id' => $newLevel->id,
            ]);

        $response
            ->assertSessionHasErrors('level')
            ->assertRedirect(route('alumni.show', $alumni, absolute: false));

        $this->assertNotSame($newLevel->id, $alumni->refresh()->level_id);
    }

    public function test_admin_cannot_update_admin_or_super_admin_learning_level_from_alumni_detail(): void
    {
        $admin = $this->userWithRole('admin');
        $otherAdmin = $this->userWithRole('admin', ['level_id' => $this->level('Level 1', 'Dasar')->id]);
        $superAdmin = $this->userWithRole('super_admin', ['level_id' => $this->level('Level 1', 'Dasar')->id]);
        $newLevel = $this->level('Level 2', 'Menengah');

        $this
            ->actingAs($admin)
            ->from(route('alumni.show', $otherAdmin))
            ->patch(route('alumni.level.update', $otherAdmin), ['level_id' => $newLevel->id])
            ->assertSessionHasErrors('level')
            ->assertRedirect(route('alumni.show', $otherAdmin, absolute: false));

        $this
            ->actingAs($admin)
            ->from(route('alumni.show', $superAdmin))
            ->patch(route('alumni.level.update', $superAdmin), ['level_id' => $newLevel->id])
            ->assertSessionHasErrors('level')
            ->assertRedirect(route('alumni.show', $superAdmin, absolute: false));

        $this->assertNotSame($newLevel->id, $otherAdmin->refresh()->level_id);
        $this->assertNotSame($newLevel->id, $superAdmin->refresh()->level_id);
    }

    public function test_admin_sees_level_update_form_for_alumni_and_koorda_targets(): void
    {
        $admin = $this->userWithRole('admin');
        $koorda = $this->userWithRole('koorda', ['level_id' => $this->level('Level 1', 'Dasar')->id]);
        $this->level('Level 2', 'Menengah');

        $response = $this
            ->actingAs($admin)
            ->get(route('alumni.show', $koorda));

        $response
            ->assertOk()
            ->assertSee('Ubah Level Materi')
            ->assertSee('name="level_id"', false)
            ->assertSee(route('alumni.level.update', $koorda), false);
    }

    public function test_koorda_does_not_see_level_update_form(): void
    {
        $koordaActor = $this->userWithRole('koorda');
        $alumni = $this->userWithRole('alumni', ['level_id' => $this->level('Level 1', 'Dasar')->id]);

        $response = $this
            ->actingAs($koordaActor)
            ->get(route('alumni.show', $alumni));

        $response
            ->assertOk()
            ->assertDontSee('Ubah Level Materi')
            ->assertDontSee('name="level_id"', false);
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

    private function level(string $name, string $description): Level
    {
        return Level::firstOrCreate(
            ['name' => $name],
            ['description' => $description, 'is_active' => true]
        );
    }
}
