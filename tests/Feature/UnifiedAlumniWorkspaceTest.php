<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnifiedAlumniWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_matches_names_only_and_default_page_size_is_bounded(): void
    {
        $admin = $this->account('Pengelola', 'admin');
        for ($i = 1; $i <= 17; $i++) {
            $this->account(sprintf('Target %02d', $i), 'alumni');
        }
        $this->account('Nadia', 'alumni')->update(['email' => 'email-only@example.test']);

        $this->actingAs($admin)->get(route('alumni.index', ['q' => 'email-only']))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->total() === 0);
        $this->get(route('alumni.index', ['q' => 'Target', 'sort' => 'name', 'dir' => 'desc']))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->total() === 17 && $rows->count() === 15)
            ->assertSee('rel="next"', false)->assertSee('aria-sort="descending"', false)
            ->assertDontSee('name="ab"', false);
        $this->get(route('alumni.index', ['q' => 'Target', 'sort' => 'name', 'dir' => 'desc', 'page' => 2]))
            ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->count() === 2
                && $rows->first()->name === 'Target 02' && $rows->last()->name === 'Target 01')
            ->assertSee('rel="prev"', false)->assertDontSee('rel="next"', false);
    }

    public function test_every_visible_data_column_sorts_both_ways_without_duplicating_multi_role_accounts(): void
    {
        $admin = $this->account('Pengelola', 'admin');
        $first = $this->account('Target Alpha', 'admin');
        $first->roles()->attach(Role::firstOrCreate(['name' => 'alumni']));
        $second = $this->account('Target Zulu', 'koorda');
        foreach ([[$first, 'A', 2020, 'active'], [$second, 'Z', 2025, 'suspended']] as [$user, $label, $year, $status]) {
            $user->update([
                'wilayah_id' => Wilayah::create(['name' => $label])->id,
                'level_id' => Level::create(['name' => $label, 'description' => $label, 'is_active' => true])->id,
                'angkatan' => $year, 'status' => $status,
            ]);
        }
        foreach (['name', 'angkatan', 'wilayah', 'role', 'level', 'status'] as $sort) {
            foreach (['asc' => [$first->id, $second->id], 'desc' => [$second->id, $first->id]] as $dir => $ids) {
                $this->actingAs($admin)->get(route('alumni.index', ['q' => 'Target', 'sort' => $sort, 'dir' => $dir]))
                    ->assertOk()->assertViewHas('alumni', fn ($rows) => $rows->total() === 2 && $rows->modelKeys() === $ids);
            }
        }
        $this->getJson(route('alumni.index', ['sort' => 'users.password']))->assertUnprocessable();
    }

    public function test_verification_json_keeps_validation_audit_and_pending_rejection_contracts(): void
    {
        $admin = $this->account('Pengelola', 'admin');
        $target = $this->account("O'Rahman", 'alumni', 'pending');
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $this->actingAs($admin)->get(route('verification.index'))
            ->assertRedirect(route('alumni.index', ['sort' => 'status', 'dir' => 'asc']));
        $this->get(route('alumni.show', $target))->assertOk()
            ->assertSee('id="verifikasi"', false)->assertSee('action="'.route('verification.approve', $target).'"', false);
        $this->postJson(route('verification.approve', $target), [])->assertUnprocessable()->assertJsonValidationErrors('level_id');
        $this->postJson(route('verification.reject', $target), ['reason' => 'no'])->assertUnprocessable()->assertJsonValidationErrors('reason');
        $this->postJson(route('verification.reject', $target), ['reason' => 'Lengkapi profil terlebih dahulu.'])->assertOk();
        $this->assertSame('pending', $target->fresh()->status);
        $this->assertSame(1, $target->notifications()->count());
        $this->assertSame(1, AuditLog::where('entity_id', $target->id)->where('action', 'verification.reject')->count());
        $this->postJson(route('verification.approve', $target), ['level_id' => $level->id])->assertOk();
        $this->assertSame('active', $target->fresh()->status);
        $this->postJson(route('verification.approve', $target), ['level_id' => $level->id])->assertConflict();
        $this->postJson(route('verification.approve', $admin), ['level_id' => $level->id])->assertForbidden();
        $this->assertSame(1, AuditLog::where('entity_id', $target->id)->where('action', 'verification.approve')->count());
    }

    public function test_verification_export_matches_eligible_named_rows_across_pages(): void
    {
        $region = Wilayah::create(['name' => 'Sendiri']);
        $other = Wilayah::create(['name' => 'Lain']);
        $actor = $this->account('Pengelola', 'koorda');
        $actor->update(['wilayah_id' => $region->id]);
        $pendingId = null;
        foreach ([['Target Pending', 'alumni', 'pending', $region], ['Target Active', 'alumni', 'active', $region],
            ['Target Foreign', 'alumni', 'pending', $other], ['Target Manager', 'koorda', 'pending', $region]] as [$name, $role, $status, $area]) {
            $user = $this->account($name, $role, $status);
            $user->update(['wilayah_id' => $area->id]);
            if ($name === 'Target Pending') {
                $pendingId = $user->id;
            }
        }
        $filters = ['view' => 'verification', 'q' => 'Target'];
        $this->actingAs($actor)->get(route('alumni.index', $filters))->assertOk()
            ->assertViewHas('alumni', fn ($rows) => $rows->total() === 3
                && $rows->getCollection()->contains('name', 'Target Pending')
                && $rows->getCollection()->contains('name', 'Target Active')
                && $rows->getCollection()->contains('name', 'Target Manager'))
            ->assertViewHas('reviewableIds', fn ($ids) => $ids === [$pendingId]);
        $csv = $this->get(route('alumni.csv.export', $filters + ['page' => 2]))->assertOk()->streamedContent();
        $this->assertStringContainsString('Target Pending', $csv);
        foreach (['Target Active', 'Target Foreign', 'Target Manager'] as $excluded) {
            $this->assertStringNotContainsString($excluded, $csv);
        }
    }

    public function test_html_verification_returns_to_list_context_and_rejects_external_return_urls(): void
    {
        $admin = $this->account('Pengelola', 'admin');
        $target = $this->account('Target', 'alumni', 'pending');
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $context = ['view' => 'verification', 'q' => 'Target', 'sort' => 'angkatan', 'dir' => 'desc', 'page' => 2];
        $returnTo = route('alumni.index', array_diff_key($context, ['view' => true]), false);
        $this->actingAs($admin)->get(route('alumni.show', ['id' => $target->id] + $context))
            ->assertOk()->assertViewHas('returnTo', function ($url) use ($context) {
                parse_str(parse_url($url, PHP_URL_QUERY), $actual);
                return $actual == array_diff_key($context, ['view' => true]);
            });
        $this->postJson(route('verification.approve', $target), ['level_id' => $level->id, '_return' => 'https://example.test/alumni?view=verification'])
            ->assertUnprocessable()->assertJsonValidationErrors('_return');
        $this->assertSame('pending', $target->fresh()->status);
        $this->post(route('verification.approve', $target), ['level_id' => $level->id, '_return' => $returnTo])
            ->assertRedirect($returnTo)->assertSessionHasNoErrors();
    }

    private function account(string $name, string $role, string $status = 'active'): User
    {
        $user = User::factory()->create(compact('name', 'status'));
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }
}
