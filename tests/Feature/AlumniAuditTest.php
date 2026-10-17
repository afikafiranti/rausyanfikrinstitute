<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Level;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class AlumniAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_and_level_changes_have_structured_audit_records(): void
    {
        [$admin, $alumni] = $this->accounts();
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);

        $this->actingAs($admin)->patch(route('alumni.update', $alumni), [
            'name' => 'Nama Diperbarui', 'email' => 'updated@example.test',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('Nama Lama', AuditLog::where('action', 'alumni.update')->firstOrFail()->payload_json['name']['before']);
        $this->assertNull($alumni->fresh()->email_verified_at);

        $this->patch(route('alumni.level.update', $alumni), ['level_id' => $level->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame($level->id, AuditLog::where('action', 'alumni.level.update')->firstOrFail()->payload_json['after']['level_id']);
    }

    public function test_delete_preserves_an_audit_record_of_the_deleted_account(): void
    {
        [$admin, $alumni] = $this->accounts();

        $this->actingAs($admin)->delete(route('alumni.destroy', $alumni))->assertSessionHasNoErrors()->assertRedirect();

        $audit = AuditLog::where('action', 'alumni.delete')->firstOrFail();
        $this->assertSame($alumni->id, $audit->entity_id);
        $this->assertSame('Nama Lama', $audit->payload_json['before']['name']);
        $this->assertNull($alumni->fresh());
    }

    public function test_admin_region_change_requires_koorda_reapproval_and_records_status_change(): void
    {
        [$admin, $koorda] = $this->accounts();
        $old = Wilayah::create(['name' => 'Wilayah Lama']);
        $new = Wilayah::create(['name' => 'Wilayah Baru']);
        $koorda->update(['wilayah_id' => $old->id]);
        $koorda->roles()->sync([Role::firstOrCreate(['name' => 'koorda'])->id]);

        $this->actingAs($admin)->patch(route('alumni.update', $koorda), [
            'name' => $koorda->name, 'email' => $koorda->email, 'wilayah_id' => $new->id,
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertSame(User::STATUS_PENDING, $koorda->fresh()->status);
        $statusChange = AuditLog::where('action', 'alumni.update')->sole()->payload_json['status'];
        ksort($statusChange);
        $this->assertSame(['after' => User::STATUS_PENDING, 'before' => User::STATUS_ACTIVE], $statusChange);
        $this->assertSame('pending', $koorda->notifications()->sole()->data['kind']);
        $this->actingAs($koorda->fresh())->get(route('verification.index'))->assertForbidden();
    }

    #[DataProvider('changes')]
    public function test_audit_failure_rolls_back_account_changes(string $change): void
    {
        [$admin, $alumni] = $this->accounts();
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);
        AuditLog::creating(fn () => throw new RuntimeException('Simulated audit failure'));

        try {
            $this->actingAs($admin);
            $response = match ($change) {
                'profile' => $this->patch(route('alumni.update', $alumni), ['name' => 'Nama Baru', 'email' => $alumni->email]),
                'level' => $this->patch(route('alumni.level.update', $alumni), ['level_id' => $level->id]),
                'delete' => $this->delete(route('alumni.destroy', $alumni)),
            };
            $this->assertSame(500, $response->status());
            $this->assertSame('Nama Lama', $alumni->fresh()->name);
            $this->assertNull($alumni->fresh()->level_id);
            $this->assertTrue($alumni->fresh()->hasRole('alumni'));
        } finally {
            AuditLog::flushEventListeners();
        }
    }

    public function test_invalid_angkatan_and_inactive_level_are_rejected_before_saving(): void
    {
        [$admin, $alumni] = $this->accounts();
        $level = Level::create(['name' => 'Level Nonaktif', 'is_active' => false]);
        $this->actingAs($admin)->patch(route('alumni.update', $alumni), [
            'name' => $alumni->name, 'email' => $alumni->email, 'angkatan' => 'bukan-angka',
        ])->assertSessionHasErrors('angkatan');
        $this->patch(route('alumni.level.update', $alumni), ['level_id' => $level->id])->assertSessionHasErrors('level_id');
        $this->assertNull($alumni->fresh()->angkatan);
        $this->assertNull($alumni->fresh()->level_id);
    }

    public static function changes(): array
    {
        return [['profile'], ['level'], ['delete']];
    }

    private function accounts(): array
    {
        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));
        $alumni = User::factory()->create(['name' => 'Nama Lama', 'status' => User::STATUS_ACTIVE]);
        $alumni->roles()->attach(Role::firstOrCreate(['name' => 'alumni']));

        return [$admin, $alumni];
    }
}
