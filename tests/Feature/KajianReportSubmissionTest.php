<?php

namespace Tests\Feature;

use App\Models\{KajianReport, Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class KajianReportSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_owner_edits_draft_preserving_identity_region_and_status(): void
    {
        foreach (['alumni', 'koorda', 'admin', 'super_admin'] as $role) {
            $owner = $this->member($role);
            $report = $this->draft($owner);
            $this->actingAs($owner)->get('/laporan/'.$report->id.'/edit')->assertOk()->assertSee('Judul Lama');
            $this->put('/laporan/'.$report->id, $this->data() + ['user_id' => 999, 'wilayah_id' => 999, 'status' => 'approved', 'reviewed_by' => $owner->id, 'total_peserta' => 999])
                ->assertRedirect('/laporan/'.$report->id)->assertSessionHas('success');
            $report->refresh();
            $this->assertSame('Judul Baru', $report->judul);
            $this->assertSame($owner->id, $report->user_id);
            $this->assertSame($owner->wilayah_id, $report->wilayah_id);
            $this->assertSame('draft', $report->status);
            $this->assertSame(9, $report->total_peserta);
            $this->assertNull($report->reviewed_by);
        }
    }

    public function test_only_owner_can_mutate_even_when_viewer_is_admin(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        foreach (['alumni', 'koorda', 'admin', 'super_admin'] as $role) {
            $this->actingAs($this->member($role, $owner->wilayah_id))->get('/laporan/'.$report->id.'/edit')->assertNotFound();
            $this->put('/laporan/'.$report->id, $this->data())->assertNotFound();
            $this->post('/laporan/'.$report->id.'/submit')->assertNotFound();
        }
        $this->assertSame('Judul Lama', $report->fresh()->judul);
        $this->assertSame('draft', $report->fresh()->status);
    }

    public function test_submit_uses_saved_values_and_locks_further_changes(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        $this->actingAs($owner)->get('/laporan/'.$report->id)->assertSee('Kirim laporan');
        $this->post('/laporan/'.$report->id.'/submit', ['judul' => 'Forged', 'status' => 'approved', 'user_id' => 999])
            ->assertRedirect('/laporan/'.$report->id)->assertSessionHas('success');
        $this->assertSame('submitted', $report->fresh()->status);
        $this->assertSame('Judul Lama', $report->fresh()->judul);
        $this->get('/laporan/'.$report->id)->assertOk()->assertSee('Dikirim')->assertDontSee('Kirim laporan')->assertDontSee('Edit draf');
        $this->get('/laporan/'.$report->id.'/edit')->assertStatus(409);
        $this->put('/laporan/'.$report->id, $this->data())->assertStatus(409);
        $this->post('/laporan/'.$report->id.'/submit')->assertStatus(409);
    }

    public function test_submitted_report_becomes_visible_only_to_local_koorda(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        $local = $this->member('koorda', $owner->wilayah_id);
        $foreign = $this->member('koorda');
        $this->actingAs($local)->get('/laporan/'.$report->id)->assertNotFound();
        $this->actingAs($owner)->post('/laporan/'.$report->id.'/submit')->assertRedirect();
        $this->actingAs($local)->get('/laporan/'.$report->id)->assertOk()->assertDontSee('Edit draf');
        $this->actingAs($foreign)->get('/laporan/'.$report->id)->assertNotFound();
    }

    public function test_validation_keeps_draft_and_rejects_invalid_saved_data_on_submit(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        $this->actingAs($owner)->from('/laporan/'.$report->id.'/edit')
            ->put('/laporan/'.$report->id, array_replace($this->data(), ['judul' => '', 'peserta_l' => -1]))
            ->assertRedirect('/laporan/'.$report->id.'/edit')->assertSessionHasErrors(['judul', 'peserta_l']);
        $report->update(['judul' => '']);
        $this->from('/laporan/'.$report->id)->post('/laporan/'.$report->id.'/submit')->assertSessionHasErrors('judul');
        $this->assertSame('draft', $report->fresh()->status);
    }

    public function test_photo_replacement_keeps_old_file_until_success_and_preserves_it_without_new_upload(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        Storage::disk('local')->put('report-photos/old.jpg', 'old photo');
        $report->update(['foto_url' => 'report-photos/old.jpg']);
        $this->actingAs($owner)->put('/laporan/'.$report->id, $this->data())->assertRedirect();
        Storage::disk('local')->assertExists('report-photos/old.jpg');
        $this->put('/laporan/'.$report->id, $this->data() + ['foto' => UploadedFile::fake()->image('new.png')])->assertRedirect();
        Storage::disk('local')->assertMissing('report-photos/old.jpg');
        Storage::disk('local')->assertExists($report->fresh()->foto_url);
        $this->assertCount(1, Storage::disk('local')->allFiles('report-photos'));
    }

    public function test_failed_update_rolls_back_fields_and_preserves_old_photo(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        Storage::disk('local')->put('report-photos/old.jpg', 'old photo');
        $report->update(['foto_url' => 'report-photos/old.jpg']);
        KajianReport::updated(fn () => throw new RuntimeException('Simulated update failure'));
        $this->actingAs($owner)->put('/laporan/'.$report->id, $this->data() + ['foto' => UploadedFile::fake()->image('new.png')])->assertServerError();
        $this->assertSame('Judul Lama', $report->fresh()->judul);
        $this->assertSame(['report-photos/old.jpg'], Storage::disk('local')->allFiles('report-photos'));
    }

    public function test_guests_inactive_and_moved_accounts_cannot_mutate(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        $this->get('/laporan/'.$report->id.'/edit')->assertRedirect(route('login'));
        $this->put('/laporan/'.$report->id, $this->data())->assertRedirect(route('login'));
        $this->post('/laporan/'.$report->id.'/submit')->assertRedirect(route('login'));
        $owner->update(['status' => 'suspended']);
        $this->actingAs($owner)->get('/laporan/'.$report->id.'/edit')->assertForbidden();
        $this->put('/laporan/'.$report->id, $this->data())->assertForbidden();
        $this->post('/laporan/'.$report->id.'/submit')->assertForbidden();
        $owner->update(['status' => 'active', 'wilayah_id' => Wilayah::create(['name' => 'Baru'])->id]);
        $this->get('/laporan/'.$report->id.'/edit')->assertNotFound();
        $this->put('/laporan/'.$report->id, $this->data())->assertNotFound();
        $this->post('/laporan/'.$report->id.'/submit')->assertNotFound();
    }

    public function test_status_is_rechecked_after_route_binding_before_update_or_submit(): void
    {
        $owner = $this->member('alumni');
        $update = $this->draft($owner);
        $submit = $this->draft($owner);
        $changed = [];
        KajianReport::retrieved(function (KajianReport $report) use (&$changed) {
            if (! isset($changed[$report->id])) {
                $changed[$report->id] = true;
                DB::table('kajian_reports')->where('id', $report->id)->update(['status' => 'submitted']);
            }
        });
        $this->actingAs($owner)->put('/laporan/'.$update->id, $this->data() + ['foto' => UploadedFile::fake()->image('new.png')])->assertStatus(409);
        $this->post('/laporan/'.$submit->id.'/submit')->assertStatus(409);
        $this->assertSame('Judul Lama', $update->fresh()->judul);
        $this->assertSame([], Storage::disk('local')->allFiles('report-photos'));
    }

    public function test_failed_submission_rolls_back_status_and_recalculated_total(): void
    {
        $owner = $this->member('alumni');
        $report = $this->draft($owner);
        $report->update(['total_peserta' => 99]);
        KajianReport::updated(fn () => throw new RuntimeException('Simulated submission failure'));
        $this->actingAs($owner)->post('/laporan/'.$report->id.'/submit')->assertServerError();
        $this->assertSame('draft', $report->fresh()->status);
        $this->assertSame(99, $report->fresh()->total_peserta);
    }

    private function data(): array
    {
        return ['tanggal' => '2026-10-16', 'judul' => 'Judul Baru', 'peserta_l' => 4, 'peserta_p' => 5];
    }

    private function member(string $role, ?int $wilayahId = null): User
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => $wilayahId ?? Wilayah::create(['name' => 'Wilayah'])->id]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function draft(User $owner): KajianReport
    {
        return KajianReport::create(['user_id' => $owner->id, 'wilayah_id' => $owner->wilayah_id, 'tanggal' => '2026-10-16', 'judul' => 'Judul Lama', 'peserta_l' => 1, 'peserta_p' => 2, 'total_peserta' => 3, 'status' => 'draft']);
    }
}
