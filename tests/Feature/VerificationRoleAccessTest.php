<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_koorda_cannot_see_or_review_manager_targets_even_in_own_region(): void
    {
        $own = Wilayah::create(['name' => 'Sendiri', 'kode' => 'OWN']);
        $other = Wilayah::create(['name' => 'Lain', 'kode' => 'OTHER']);
        $actor = $this->account('koorda', $own, User::STATUS_ACTIVE);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        foreach ([
            ['koorda', $own],
            ['admin', $own],
            ['super_admin', $own],
            ['admin', $other],
            ['super_admin', null],
        ] as [$role, $region]) {
            $target = $this->account($role, $region, User::STATUS_PENDING);
            $this->actingAs($actor)->post(route('verification.approve', $target), ['level_id' => $level->id])->assertForbidden();
            $this->post(route('verification.reject', $target), ['reason' => 'Tidak sesuai'])->assertForbidden();
            $this->get(route('verification.index'))
                ->assertOk()->assertViewHas('pending', fn ($items) => !$items->getCollection()->contains('id', $target->id));
            $this->assertSame(User::STATUS_PENDING, $target->fresh()->status);
            $this->assertSame(0, $target->notifications()->count());
            $this->assertSame(0, AuditLog::where('entity_id', $target->id)->count());
        }

        $mixed = $this->account('alumni', $own, User::STATUS_PENDING);
        $mixed->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $this->get(route('verification.index'))
            ->assertViewHas('pending', fn ($items) => !$items->getCollection()->contains('id', $mixed->id));
        $this->post(route('verification.approve', $mixed), ['level_id' => $level->id])->assertForbidden();
        $this->post(route('verification.reject', $mixed), ['reason' => 'Tidak sesuai'])->assertForbidden();
    }

    public function test_admin_can_review_manager_targets_including_super_admin(): void
    {
        $actor = $this->account('admin', null, User::STATUS_ACTIVE);
        $superAdmin = $this->account('super_admin', null, User::STATUS_PENDING);
        $mixed = $this->account('admin', null, User::STATUS_PENDING);
        $mixed->roles()->attach(Role::firstOrCreate(['name' => 'super_admin']));
        $peer = $this->account('admin', null, User::STATUS_PENDING);
        $koorda = $this->account('koorda', null, User::STATUS_PENDING);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $this->actingAs($actor)->get(route('verification.index'))->assertOk()
            ->assertViewHas('pending', fn ($items) => $items->total() === 4
                && $items->getCollection()->contains('id', $superAdmin->id)
                && $items->getCollection()->contains('id', $mixed->id)
                && $items->getCollection()->contains('id', $peer->id)
                && $items->getCollection()->contains('id', $koorda->id));

        $this->post(route('verification.approve', $superAdmin), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->post(route('verification.reject', $mixed), ['reason' => 'Data belum lengkap'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->post(route('verification.approve', $peer), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->post(route('verification.reject', $koorda), ['reason' => 'Data belum lengkap'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $peer->fresh()->status);
        $this->assertSame(User::STATUS_ACTIVE, $superAdmin->fresh()->status);
        $this->assertSame(User::STATUS_PENDING, $mixed->fresh()->status);
        $this->assertSame(User::STATUS_PENDING, $koorda->fresh()->status);
        $this->assertSame(1, $koorda->notifications()->count());
    }

    public function test_admin_can_reactivate_lone_super_admin_after_profile_change(): void
    {
        $superAdmin = $this->account('super_admin', null, User::STATUS_ACTIVE);
        $superAdmin->angkatan = 2020;
        $superAdmin->save();
        $admin = $this->account('admin', null, User::STATUS_ACTIVE);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $this->actingAs($superAdmin)->patch(route('profile.update'), [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'angkatan' => 2021,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_PENDING, $superAdmin->fresh()->status);

        $this->actingAs($admin)->post(route('verification.approve', $superAdmin), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $superAdmin->fresh()->status);
    }

    public function test_super_admin_can_review_other_super_admin_without_region(): void
    {
        $actor = $this->account('super_admin', null, User::STATUS_ACTIVE);
        $target = $this->account('super_admin', null, User::STATUS_PENDING);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $this->actingAs($actor)->get(route('verification.index'))
            ->assertViewHas('pending', fn ($items) => $items->getCollection()->contains('id', $target->id));
        $this->post(route('verification.approve', $target), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $target->fresh()->status);
    }

    public function test_koorda_can_still_review_alumni_and_legacy_roleless_account(): void
    {
        $own = Wilayah::create(['name' => 'Sendiri', 'kode' => 'OWN']);
        $actor = $this->account('koorda', $own, User::STATUS_ACTIVE);
        $alumni = $this->account('alumni', $own, User::STATUS_PENDING);
        $legacy = User::factory()->create(['wilayah_id' => $own->id, 'status' => User::STATUS_PENDING]);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $this->actingAs($actor)->get(route('verification.index'))
            ->assertViewHas('pending', fn ($items) => $items->getCollection()->contains('id', $alumni->id)
                && $items->getCollection()->contains('id', $legacy->id));
        $this->post(route('verification.approve', $alumni), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->post(route('verification.approve', $legacy), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $alumni->fresh()->status);
        $this->assertTrue($legacy->fresh()->hasRole('alumni'));
    }

    public function test_mixed_actor_roles_use_highest_privilege(): void
    {
        $admin = $this->account('koorda', null, User::STATUS_ACTIVE);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $koorda = $this->account('koorda', null, User::STATUS_PENDING);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $this->actingAs($admin)->post(route('verification.approve', $koorda), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $koorda->fresh()->status);

        $superAdmin = $this->account('admin', null, User::STATUS_ACTIVE);
        $superAdmin->roles()->attach(Role::firstOrCreate(['name' => 'super_admin']));
        $target = $this->account('super_admin', null, User::STATUS_PENDING);
        $this->actingAs($superAdmin)->post(route('verification.approve', $target), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $target->fresh()->status);
    }

    public function test_self_and_non_pending_targets_cannot_be_reviewed(): void
    {
        $admin = $this->account('admin', null, User::STATUS_ACTIVE);
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $this->actingAs($admin)->post(route('verification.approve', $admin), ['level_id' => $level->id])
            ->assertSessionHasErrors('msg');
        $this->post(route('verification.reject', $admin), ['reason' => 'Data belum lengkap'])
            ->assertSessionHasErrors('msg');

        foreach ([User::STATUS_ACTIVE, User::STATUS_SUSPENDED] as $status) {
            $target = $this->account('alumni', null, $status);
            $this->post(route('verification.approve', $target), ['level_id' => $level->id])
                ->assertSessionHasErrors('msg');
            $this->post(route('verification.reject', $target), ['reason' => 'Data belum lengkap'])
                ->assertSessionHasErrors('msg');
            $this->assertSame($status, $target->fresh()->status);
            $this->assertSame(0, $target->notifications()->count());
            $this->assertSame(0, AuditLog::where('entity_id', $target->id)->count());
        }
    }

    private function account(string $role, ?Wilayah $region, string $status): User
    {
        $user = User::factory()->create(['status' => $status, 'wilayah_id' => $region?->id]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }
}
