<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_account_cannot_be_rejected_or_notified(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['status' => User::STATUS_ACTIVE]);

        $this->actingAs($admin)
            ->post(route('verification.reject', $target), ['reason' => 'Data tidak cocok'])
            ->assertSessionHasErrors('msg');

        $this->assertSame(0, $target->notifications()->count());
        $this->assertSame(0, AuditLog::where('entity_id', $target->id)->count());
    }

    public function test_rejection_keeps_account_pending_and_records_readable_reason(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['status' => User::STATUS_PENDING]);

        $this->actingAs($admin)
            ->post(route('verification.reject', $target), ['reason' => 'Data belum lengkap'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertSame(User::STATUS_PENDING, $target->fresh()->status);
        $this->assertSame(['reason' => 'Data belum lengkap'], AuditLog::where('entity_id', $target->id)->sole()->payload_json);
        $this->assertSame('Data belum lengkap', $target->notifications()->sole()->data['meta']['reason']);
    }

    public function test_approval_sets_level_role_and_status_with_readable_audit_and_notification(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['status' => User::STATUS_PENDING]);
        $level = Level::create(['name' => 'Level Dasar', 'is_active' => true]);

        $this->actingAs($admin)
            ->post(route('verification.approve', $target), ['level_id' => $level->id, 'note' => 'Disetujui'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $target->refresh();
        $this->assertSame(User::STATUS_ACTIVE, $target->status);
        $this->assertSame($level->id, $target->level_id);
        $this->assertTrue($target->hasRole('alumni'));
        $this->assertSame('approved', $target->notifications()->sole()->data['kind']);
        $this->assertSame([
            'before' => ['level_id' => null, 'status' => User::STATUS_PENDING],
            'after' => ['level_id' => $level->id, 'status' => User::STATUS_ACTIVE],
            'note' => 'Disetujui',
        ], AuditLog::where('entity_id', $target->id)->sole()->payload_json);
    }

    public function test_inactive_level_cannot_be_assigned_during_approval(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['status' => User::STATUS_PENDING]);
        $inactive = Level::create(['name' => 'Level Lama', 'is_active' => false]);

        $this->actingAs($admin)
            ->post(route('verification.approve', $target), ['level_id' => $inactive->id])
            ->assertSessionHasErrors('level_id');

        $this->assertSame(User::STATUS_PENDING, $target->fresh()->status);
        $this->assertSame(0, $target->notifications()->count());
        $this->assertSame(0, AuditLog::where('entity_id', $target->id)->count());
    }

    private function admin(): User
    {
        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));

        return $admin;
    }
}
