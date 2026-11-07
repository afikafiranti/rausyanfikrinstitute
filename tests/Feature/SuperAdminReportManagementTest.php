<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\KajianReport;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class SuperAdminReportManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_super_admin_can_edit_and_delete_every_status_across_owners_and_regions_with_audit(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Asal']);
        $otherRegion = Wilayah::create(['name' => 'Wilayah Lain']);
        $admin = $this->member(['super_admin', 'koorda'], $region->id);
        $author = $this->member(['koorda'], $otherRegion->id);
        $reviewer = $this->member(['admin'], $region->id);

        foreach (['draft', 'submitted', 'approved', 'rejected'] as $status) {
            $report = $this->report($author, $otherRegion->id, $status, $reviewer);
            $protected = ['user_id', 'wilayah_id', 'status', 'reviewed_by', 'reviewed_at', 'reject_reason'];
            $before = array_intersect_key($report->getRawOriginal(), array_flip($protected));
            $this->actingAs($admin)->get("/laporan/{$report->id}/edit")->assertOk();
            $this->get('/laporan')->assertOk()
                ->assertSee('href="'.route('laporan.edit', $report).'"', false)
                ->assertSee('action="'.route('laporan.destroy', $report).'"', false);
            $this->get("/laporan/{$report->id}")->assertOk()->assertViewHas('canManageReport', true);
            $this->put("/laporan/{$report->id}", $this->data() + [
                'user_id' => $admin->id, 'wilayah_id' => $region->id, 'status' => 'draft',
                'reviewed_by' => $admin->id, 'reviewed_at' => '2020-01-01', 'reject_reason' => 'Palsu',
                'total_peserta' => 999, 'foto_url' => 'report-photos/forged.png',
            ])->assertRedirect("/laporan/{$report->id}");
            $after = $report->fresh();
            $this->assertSame('Judul Baru', $after->judul);
            $this->assertSame(9, $after->total_peserta);
            $this->assertSame($before, array_intersect_key($after->getRawOriginal(), array_flip($protected)));
            $this->assertNull($after->foto_url);
            $update = AuditLog::where('action', 'report.update')->where('entity_id', $report->id)->sole();
            $this->assertSame($admin->id, $update->actor_id);
            $this->assertSame('kajian_report', $update->entity_type);
            $this->assertTrue($update->payload_json['administrative']);
            $this->assertSame($status, $update->payload_json['before']['status']);
            $this->assertSame('Judul Baru', $update->payload_json['after']['judul']);
            $this->get("/laporan/{$report->id}")->assertOk()
                ->assertSee('Perubahan administratif terakhir')->assertSee($admin->name)
                ->assertViewHas('lastAdministrativeChange', fn ($audit) => $audit->is($update));

            Storage::disk('local')->put("report-photos/old{$report->id}.jpg", 'old photo');
            $after->update(['foto_url' => "report-photos/old{$report->id}.jpg"]);
            $this->delete("/laporan/{$report->id}")->assertRedirect('/laporan');
            $this->assertDatabaseMissing('kajian_reports', ['id' => $report->id]);
            Storage::disk('local')->assertMissing("report-photos/old{$report->id}.jpg");
            $delete = AuditLog::where('action', 'report.delete')->where('entity_id', $report->id)->sole();
            $this->assertSame($admin->id, $delete->actor_id);
            $this->assertTrue($delete->payload_json['administrative']);
            $this->assertSame($status, $delete->payload_json['before']['status']);
            $this->assertSame("report-photos/old{$report->id}.jpg", $delete->payload_json['before']['foto_url']);
        }
    }

    public function test_super_admin_can_manage_own_approved_report_but_cannot_submit_it_or_review_own_submitted_report(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        $admin = $this->member(['super_admin'], $region->id);
        $approved = $this->report($admin, $region->id, 'approved', $admin);
        $submitted = $this->report($admin, $region->id, 'submitted');
        $draft = $this->report($admin, $region->id, 'draft');
        $this->actingAs($admin)->put("/laporan/{$approved->id}", $this->data())->assertRedirect();
        $this->assertSame('approved', $approved->fresh()->status);
        $this->put("/laporan/{$draft->id}", $this->data())->assertRedirect();
        $this->assertSame(1, AuditLog::where('action', 'report.update')->where('entity_id', $draft->id)->count());
        $this->post("/laporan/{$approved->id}/submit")->assertStatus(409);
        $this->post("/laporan/{$submitted->id}/approve")->assertForbidden();
        $this->delete("/laporan/{$approved->id}")->assertRedirect();
        $this->delete("/laporan/{$draft->id}")->assertRedirect();
        $this->assertSame(1, AuditLog::where('action', 'report.delete')->where('entity_id', $draft->id)->count());
        $this->assertDatabaseMissing('kajian_reports', ['id' => $approved->id]);
    }

    public function test_inactive_super_admin_alumni_and_peer_admin_cannot_manage_foreign_reports(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        $author = $this->member(['koorda'], $region->id);
        $report = $this->report($author, $region->id, 'submitted');
        $inactive = $this->member(['super_admin'], $region->id, 'suspended');
        $alumni = $this->member(['alumni'], $region->id);
        $peer = $this->member(['admin'], $region->id);
        foreach ([$inactive, $alumni, $peer] as $actor) {
            $this->actingAs($actor)->get("/laporan/{$report->id}/edit")->assertStatus($actor === $peer ? 404 : 403);
            $this->put("/laporan/{$report->id}", $this->data())->assertStatus($actor === $peer ? 404 : 403);
            $this->delete("/laporan/{$report->id}")->assertStatus($actor === $peer ? 404 : 403);
        }
        $own = $this->report($peer, $region->id, 'submitted');
        $this->actingAs($peer)->put("/laporan/{$own->id}", $this->data())->assertStatus(409);
        $this->delete("/laporan/{$own->id}")->assertStatus(409);
        $this->get('/laporan')->assertOk()
            ->assertDontSee('href="'.route('laporan.edit', $own).'"', false)
            ->assertDontSee('action="'.route('laporan.destroy', $own).'"', false);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_photo_replacement_and_failed_audit_preserve_database_and_old_photo(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        $admin = $this->member(['super_admin'], $region->id);
        $report = $this->report($this->member(['koorda'], $region->id), $region->id, 'approved', $admin);
        Storage::disk('local')->put('report-photos/old.jpg', 'old photo');
        $report->update(['foto_url' => 'report-photos/old.jpg']);

        $this->actingAs($admin)->put("/laporan/{$report->id}", $this->data() + ['foto' => UploadedFile::fake()->image('new.png')])->assertRedirect();
        Storage::disk('local')->assertMissing('report-photos/old.jpg');
        $newPath = $report->fresh()->foto_url;
        Storage::disk('local')->assertExists($newPath);
        $this->assertSame('approved', $report->fresh()->status);

        AuditLog::creating(fn () => throw new RuntimeException('Audit unavailable'));
        try {
            $this->put("/laporan/{$report->id}", $this->data() + ['foto' => UploadedFile::fake()->image('failed.png')])->assertServerError();
            $this->assertSame($newPath, $report->fresh()->foto_url);
            $this->assertSame(['report-photos/'.basename($newPath)], Storage::disk('local')->allFiles('report-photos'));
            $this->delete("/laporan/{$report->id}")->assertServerError();
            $this->assertDatabaseHas('kajian_reports', ['id' => $report->id]);
            Storage::disk('local')->assertExists($newPath);
            $this->assertSame(1, AuditLog::where('entity_id', $report->id)->count());
        } finally {
            AuditLog::flushEventListeners();
        }
    }

    private function data(): array
    {
        return ['tanggal' => '2026-10-16', 'judul' => 'Judul Baru', 'peserta_l' => 4, 'peserta_p' => 5];
    }

    private function member(array $roles, ?int $regionId, string $status = 'active'): User
    {
        $user = User::factory()->create(['status' => $status, 'wilayah_id' => $regionId]);
        foreach ($roles as $role) {
            $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
        }

        return $user;
    }

    private function report(User $author, int $regionId, string $status, ?User $reviewer = null): KajianReport
    {
        return KajianReport::create([
            'user_id' => $author->id, 'wilayah_id' => $regionId, 'tanggal' => '2026-10-16',
            'judul' => 'Judul Lama', 'peserta_l' => 1, 'peserta_p' => 2, 'total_peserta' => 3,
            'status' => $status, 'reviewed_by' => $reviewer?->id,
            'reviewed_at' => $reviewer ? now() : null,
            'reject_reason' => $status === 'rejected' ? 'Alasan lama' : null,
        ]);
    }
}
