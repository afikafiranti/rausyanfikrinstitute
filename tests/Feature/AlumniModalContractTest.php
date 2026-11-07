<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlumniModalContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_ignores_legacy_verification_view_and_sorts_pending_first(): void
    {
        $admin = $this->account('Admin', 'admin');
        $pending = $this->account('Target Pending', 'alumni', 'pending');
        $active = $this->account('Target Active', 'alumni', 'active');
        $suspended = $this->account('Target Suspended', 'alumni', 'suspended');

        $this->actingAs($admin)->get(route('alumni.index', ['view' => 'verification', 'q' => 'Target', 'sort' => 'status', 'dir' => 'asc']))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->modelKeys() === [$pending->id, $active->id, $suspended->id]);
        $this->get(route('alumni.index', ['q' => 'Target', 'sort' => 'status', 'dir' => 'desc']))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->modelKeys() === [$suspended->id, $active->id, $pending->id]);
        $this->get(route('verification.index', ['q' => 'Target']))
            ->assertRedirect(route('alumni.index', ['q' => 'Target', 'sort' => 'status', 'dir' => 'asc']));
    }

    public function test_fragments_are_private_and_enforce_review_and_delete_permissions(): void
    {
        $admin = $this->account('Admin', 'admin');
        $pending = $this->account('Target Pending', 'alumni', 'pending');
        $active = $this->account('Target Active', 'alumni', 'active');

        $this->actingAs($admin)->get(route('alumni.show', ['id' => $pending->id, 'modal' => 'detail']))
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')->assertHeader('X-Alumni-Modal', '1')
            ->assertViewIs('alumni.partials.detail');
        $this->get(route('alumni.show', ['id' => $pending->id, 'modal' => 'delete']))
            ->assertOk()->assertHeader('X-Alumni-Modal', '1')->assertViewIs('alumni.partials.delete');
        $this->get(route('alumni.show', ['id' => $pending->id, 'modal' => 'verification']))
            ->assertOk()->assertHeader('X-Alumni-Modal', '1')->assertViewIs('verification.forms');
        $this->get(route('alumni.show', ['id' => $active->id, 'modal' => 'verification']))->assertForbidden();
        $this->get(route('alumni.show', ['id' => $admin->id, 'modal' => 'delete']))->assertForbidden();
        $this->get(route('alumni.edit', ['user' => $pending->id, 'modal' => '1']))
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')->assertHeader('X-Alumni-Modal', '1')
            ->assertViewIs('alumni.partials.edit');
    }

    public function test_json_mutations_return_messages_and_preserve_validation_and_protection(): void
    {
        $admin = $this->account('Admin', 'admin');
        $alumni = $this->account('Target', 'alumni');
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $region = Wilayah::create(['name' => 'Palu']);

        $this->actingAs($admin)->postJson(route('alumni.store'), [])->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->postJson(route('alumni.store'), [
            'name' => 'New Target', 'email' => 'new@example.test', 'password' => 'Password123!',
            'password_confirmation' => 'Password123!', 'wilayah_id' => $region->id,
        ])->assertOk()->assertJsonStructure(['message']);
        $this->patchJson(route('alumni.update', $alumni), ['name' => 'Target Updated', 'email' => $alumni->email])
            ->assertOk()->assertJsonStructure(['message']);
        $this->patchJson(route('alumni.level.update', $alumni), ['level_id' => $level->id])
            ->assertOk()->assertJsonStructure(['message']);
        $this->deleteJson(route('alumni.destroy', $admin))->assertForbidden()->assertJsonStructure(['message'])
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->deleteJson(route('alumni.destroy', $alumni))->assertOk()->assertJsonStructure(['message'])
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->assertModelMissing($alumni);
    }

    public function test_unfiltered_verification_fragment_supplies_valid_return_url(): void
    {
        $admin = $this->account('Admin', 'admin');
        $pending = $this->account('Target Pending', 'alumni', 'pending');
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);

        $returnTo = route('alumni.index', ['sort' => 'name', 'dir' => 'asc'], false);
        $this->actingAs($admin)->get(route('alumni.show', ['id' => $pending->id, 'modal' => 'verification']))
            ->assertOk()->assertViewHas('returnTo', $returnTo);
        $this->postJson(route('verification.approve', $pending), [
            'level_id' => $level->id, '_return' => $returnTo,
        ])->assertOk();
    }

    private function account(string $name, string $role, string $status = 'active'): User
    {
        $user = User::factory()->create(compact('name', 'status'));
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
        return $user;
    }
}
