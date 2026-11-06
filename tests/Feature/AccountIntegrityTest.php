<?php

namespace Tests\Feature;

use App\Models\KajianReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_author_cannot_delete_own_account_at_any_report_status(): void
    {
        foreach (['draft', 'submitted', 'approved', 'rejected'] as $status) {
            $author = $this->member('alumni');
            $report = $this->report($author, $status);

            $this->actingAs($author)->from('/profile')->delete('/profile', ['password' => 'password'])
                ->assertRedirect('/profile')->assertSessionHasErrorsIn('userDeletion', 'delete');

            $this->assertNotNull($author->fresh());
            $this->assertSame($status, $report->fresh()->status);
        }

        $this->assertDatabaseCount('kajian_reports', 4);
        $this->assertDatabaseMissing('audit_logs', ['action' => 'profile.delete']);
    }

    public function test_alumni_deletion_rejects_report_author_and_reviewer_without_losing_history(): void
    {
        $admin = $this->member('admin');
        $author = $this->member('alumni');
        $reviewer = $this->member('koorda');
        $report = $this->report($author, 'approved', $reviewer);

        foreach ([$author, $reviewer] as $target) {
            $this->actingAs($admin)->from(route('alumni.index'))
                ->delete(route('alumni.destroy', $target))
                ->assertRedirect(route('alumni.index', absolute: false))->assertSessionHasErrors('delete');
            $this->assertNotNull($target->fresh());
        }

        $this->assertSame($author->id, $report->fresh()->user_id);
        $this->assertSame($reviewer->id, $report->fresh()->reviewed_by);
        $this->assertDatabaseMissing('audit_logs', ['action' => 'alumni.delete']);
    }

    public function test_foreign_keys_preserve_report_and_reviewer_when_users_are_deleted_directly(): void
    {
        $author = $this->member('alumni');
        $reviewer = $this->member('koorda');
        $report = $this->report($author, 'approved', $reviewer);

        foreach ([$author, $reviewer] as $target) {
            try {
                DB::table('users')->where('id', $target->id)->delete();
                $this->fail('Foreign key must reject deletion of a report participant.');
            } catch (QueryException) {
                $this->assertNotNull($target->fresh());
            }
        }

        $this->assertSame($author->id, $report->fresh()->user_id);
        $this->assertSame($reviewer->id, $report->fresh()->reviewed_by);
    }

    public function test_only_active_global_manager_cannot_delete_own_account(): void
    {
        foreach (['admin', 'super_admin'] as $role) {
            $manager = $this->member($role);
            $this->actingAs($manager)->from('/profile')->delete('/profile', ['password' => 'password'])
                ->assertRedirect('/profile')->assertSessionHasErrorsIn('userDeletion', 'delete');
            $this->assertNotNull($manager->fresh());
            $this->assertAuthenticatedAs($manager);
        }
    }

    public function test_second_admin_can_delete_own_account_while_another_remains(): void
    {
        $first = $this->member('admin');
        $second = $this->member('admin');

        $this->actingAs($first)->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/')->assertSessionHasNoErrors();

        $this->assertNull($first->fresh());
        $this->assertSame(User::STATUS_ACTIVE, $second->fresh()->status);
    }

    public function test_sole_super_admin_cannot_become_pending_even_if_another_admin_exists(): void
    {
        $super = $this->member('super_admin', ['angkatan' => 2020]);
        $admin = $this->member('admin');

        $this->actingAs($super)->from('/profile')->patch('/profile', [
            'name' => $super->name,
            'email' => $super->email,
            'angkatan' => 2021,
        ])->assertRedirect('/profile')->assertSessionHasErrors('status');

        $this->assertSame(User::STATUS_ACTIVE, $super->fresh()->status);
        $this->assertSame(2020, $super->fresh()->angkatan);
        $this->assertSame(User::STATUS_ACTIVE, $admin->fresh()->status);
        $this->assertDatabaseMissing('audit_logs', ['action' => 'profile.update']);
    }

    public function test_multi_role_user_counts_once_and_can_deactivate_with_replacements(): void
    {
        $multi = $this->member('admin', ['angkatan' => 2020]);
        $multi->roles()->attach(Role::firstOrCreate(['name' => 'super_admin'])->id);

        $this->actingAs($multi)->from('/profile')->patch('/profile', [
            'name' => $multi->name, 'email' => $multi->email, 'angkatan' => 2021,
        ])->assertSessionHasErrors('status');
        $this->assertSame(User::STATUS_ACTIVE, $multi->fresh()->status);

        $this->member('admin');
        $this->actingAs($multi)->from('/profile')->patch('/profile', [
            'name' => $multi->name, 'email' => $multi->email, 'angkatan' => 2021,
        ])->assertSessionHasErrors('status');

        $this->member('super_admin');
        $this->actingAs($multi)->patch('/profile', [
            'name' => $multi->name, 'email' => $multi->email, 'angkatan' => 2021,
        ])->assertSessionHasNoErrors();
        $this->assertSame(User::STATUS_PENDING, $multi->fresh()->status);
    }

    public function test_deleting_unrelated_account_cleans_owned_avatar_only(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/owned.jpg', 'owned');
        Storage::disk('public')->put('avatars/keep.jpg', 'keep');
        $owner = $this->member('alumni', ['photo_url' => '/storage/avatars/owned.jpg']);
        $external = $this->member('alumni', ['photo_url' => 'https://example.test/avatar.jpg']);

        $this->actingAs($owner)->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/')->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing('avatars/owned.jpg');
        Storage::disk('public')->assertExists('avatars/keep.jpg');

        $this->actingAs($external)->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/')->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists('avatars/keep.jpg');
    }

    public function test_admin_deleting_reportless_alumni_cleans_local_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/alumni.jpg', 'owned');
        $admin = $this->member('admin');
        $alumni = $this->member('alumni', ['photo_url' => '/storage/avatars/alumni.jpg']);

        $this->actingAs($admin)->delete(route('alumni.destroy', $alumni))
            ->assertSessionHasNoErrors();

        $this->assertNull($alumni->fresh());
        Storage::disk('public')->assertMissing('avatars/alumni.jpg');
    }

    private function member(string $role, array $attributes = []): User
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE] + $attributes);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role])->id);

        return $user;
    }

    private function report(User $author, string $status, ?User $reviewer = null): KajianReport
    {
        return KajianReport::create([
            'user_id' => $author->id,
            'tanggal' => '2026-11-06',
            'judul' => 'Laporan '.$status,
            'status' => $status,
            'reviewed_by' => $reviewer?->id,
        ]);
    }
}
