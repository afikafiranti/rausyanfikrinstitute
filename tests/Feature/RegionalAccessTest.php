<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegionalAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_koorda_only_sees_alumni_and_filter_data_from_their_region(): void
    {
        $ownRegion = Wilayah::create(['name' => 'Wilayah Sendiri', 'kode' => 'OWN']);
        $otherRegion = Wilayah::create(['name' => 'Wilayah Lain', 'kode' => 'OTHER']);
        $koorda = $this->actor('koorda', $ownRegion);
        User::factory()->create(['name' => 'Alumni Sendiri', 'wilayah_id' => $ownRegion->id, 'angkatan' => 2021]);
        User::factory()->create(['name' => 'Alumni Rahasia', 'wilayah_id' => $otherRegion->id, 'angkatan' => 1999]);

        $this->actingAs($koorda)->get(route('alumni.index'))
            ->assertOk()
            ->assertSee('Alumni Sendiri')
            ->assertDontSee('Alumni Rahasia')
            ->assertViewHas('totalAlumni', 2)
            ->assertViewHas('wilayah', fn ($items) => $items->modelKeys() === [$ownRegion->id])
            ->assertViewHas('angkatanList', fn ($items) => !$items->contains(1999));

        $this->get(route('alumni.index', ['wilayah_id' => $otherRegion->id]))
            ->assertOk()
            ->assertDontSee('Alumni Rahasia')
            ->assertViewHas('alumni', fn ($items) => $items->total() === 0);
    }

    public function test_koorda_cannot_open_alumni_from_another_region(): void
    {
        $ownRegion = Wilayah::create(['name' => 'Sendiri', 'kode' => 'OWN']);
        $otherRegion = Wilayah::create(['name' => 'Lain', 'kode' => 'OTHER']);
        $koorda = $this->actor('koorda', $ownRegion);
        $target = User::factory()->create(['wilayah_id' => $otherRegion->id]);

        $this->actingAs($koorda)->get(route('alumni.show', $target))->assertNotFound();
    }

    public function test_koorda_can_only_list_and_review_pending_accounts_in_their_region(): void
    {
        $ownRegion = Wilayah::create(['name' => 'Sendiri', 'kode' => 'OWN']);
        $otherRegion = Wilayah::create(['name' => 'Lain', 'kode' => 'OTHER']);
        $koorda = $this->actor('koorda', $ownRegion);
        $ownTarget = User::factory()->create(['wilayah_id' => $ownRegion->id]);
        $otherTarget = User::factory()->create(['wilayah_id' => $otherRegion->id]);
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);

        $this->actingAs($koorda)->get(route('verification.index'))
            ->assertOk()
            ->assertViewHas('pending', fn ($items) => $items->getCollection()->modelKeys() === [$ownTarget->id]);
        $this->post(route('verification.approve', $otherTarget), ['level_id' => $level->id])->assertForbidden();
        $this->post(route('verification.reject', $otherTarget), ['reason' => 'Bukan wilayah sendiri'])->assertForbidden();
        $this->assertSame(User::STATUS_PENDING, $otherTarget->fresh()->status);
        $this->assertSame(0, $otherTarget->notifications()->count());

        $this->get(route('alumni.show', $ownTarget))->assertOk();
        $this->post(route('verification.approve', $ownTarget), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $ownTarget->fresh()->status);
    }

    public function test_koorda_without_region_cannot_access_alumni_or_verification(): void
    {
        $koorda = $this->actor('koorda', null);
        $target = User::factory()->create(['wilayah_id' => null]);
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar']);

        $this->actingAs($koorda)->get(route('alumni.index'))->assertForbidden();
        $this->get(route('alumni.show', $target))->assertForbidden();
        $this->get(route('verification.index'))->assertForbidden();
        $this->post(route('verification.approve', $target), ['level_id' => $level->id])->assertForbidden();
        $this->post(route('verification.reject', $target), ['reason' => 'Belum memiliki wilayah'])->assertForbidden();
        $this->assertSame(User::STATUS_PENDING, $target->fresh()->status);
        $this->assertSame(0, $target->notifications()->count());
    }

    public function test_region_change_requires_reapproval_before_koorda_can_review_new_region(): void
    {
        $ownRegion = Wilayah::create(['name' => 'Sendiri', 'kode' => 'OWN']);
        $otherRegion = Wilayah::create(['name' => 'Lain', 'kode' => 'OTHER']);
        $koorda = $this->actor('koorda', $ownRegion);
        $target = User::factory()->create(['wilayah_id' => $otherRegion->id]);
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar']);

        $this->actingAs($koorda)->patch(route('profile.update'), [
            'name' => $koorda->name, 'email' => $koorda->email, 'wilayah_id' => $otherRegion->id,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_PENDING, $koorda->fresh()->status);
        $this->get(route('alumni.index'))->assertForbidden();
        $this->get(route('alumni.show', $target))->assertForbidden();
        $this->get(route('verification.index'))->assertForbidden();
        $this->post(route('verification.approve', $target), ['level_id' => $level->id])->assertForbidden();
        $this->post(route('verification.reject', $target), ['reason' => 'Pindah wilayah sendiri'])->assertForbidden();
        $this->assertSame(User::STATUS_PENDING, $target->fresh()->status);
    }

    #[DataProvider('adminRoles')]
    public function test_admin_roles_can_view_and_review_across_regions(string $role): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Target', 'kode' => 'TARGET']);
        $admin = $this->actor($role, null);
        $target = User::factory()->create(['wilayah_id' => $region->id]);
        $rejected = User::factory()->create(['wilayah_id' => $region->id]);
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar']);

        $this->actingAs($admin)->get(route('alumni.index'))->assertOk()->assertViewHas('totalAlumni', 3);
        $this->get(route('alumni.show', $target))->assertOk();
        $this->get(route('verification.index'))->assertOk()
            ->assertViewHas('pending', fn ($items) => $items->total() === 2);
        $this->post(route('verification.approve', $target), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->post(route('verification.reject', $rejected), ['reason' => 'Data belum lengkap'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(User::STATUS_ACTIVE, $target->fresh()->status);
        $this->assertSame(1, $rejected->notifications()->count());
    }

    #[DataProvider('unauthorizedRoles')]
    public function test_guests_and_alumni_cannot_access_alumni_or_verification(?string $role, int $status): void
    {
        if ($role !== null) {
            $this->actingAs($this->actor($role, null));
        }
        $target = User::factory()->create();

        $this->get(route('alumni.index'))->assertStatus($status);
        $this->get(route('alumni.show', $target))->assertStatus($status);
        $this->get(route('verification.index'))->assertStatus($status);
        $this->post(route('verification.approve', $target))->assertStatus($status);
        $this->post(route('verification.reject', $target))->assertStatus($status);
    }

    public static function adminRoles(): array
    {
        return [['admin'], ['super_admin']];
    }

    public static function unauthorizedRoles(): array
    {
        return [[null, 302], ['alumni', 403]];
    }

    private function actor(string $role, ?Wilayah $region): User
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'wilayah_id' => $region?->id]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }
}
