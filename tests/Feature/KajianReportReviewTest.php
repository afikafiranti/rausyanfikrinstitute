<?php

namespace Tests\Feature;

use App\Models\{KajianReport, Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class KajianReportReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_only_contains_submitted_reports_in_scope_excluding_own(): void
    {
        $koorda = $this->member('koorda');
        $author = $this->member('alumni', $koorda->wilayah_id);
        $local = $this->report($author, 'Antrean Lokal');
        $foreign = $this->report($this->member('alumni'), 'Antrean Asing');
        $this->report($author, 'Draf Privat', 'draft');
        $this->report($author, 'Sudah Diputuskan', 'approved');
        $this->report($koorda, 'Laporan Sendiri');
        $this->actingAs($koorda)->get('/laporan/review?wilayah_id='.$foreign->wilayah_id)->assertOk()
            ->assertSee('Antrean Lokal')->assertDontSee('Antrean Asing')->assertDontSee('Draf Privat')->assertDontSee('Sudah Diputuskan')->assertDontSee('Laporan Sendiri');
        foreach (['admin', 'super_admin'] as $role) {
            $this->actingAs($this->member($role))->get('/laporan/review')->assertOk()->assertSee($local->judul)->assertSee($foreign->judul);
        }
    }

    public function test_reviewers_approve_with_server_owned_metadata_and_no_content_changes(): void
    {
        foreach (['koorda', 'admin', 'super_admin'] as $role) {
            $author = $this->member('alumni');
            $report = $this->report($author, 'Laporan Terverifikasi');
            $reviewer = $this->member($role, $role === 'koorda' ? $author->wilayah_id : null);
            $this->actingAs($reviewer)->get('/laporan/'.$report->id)->assertSee('Setujui laporan');
            $this->post('/laporan/'.$report->id.'/approve', ['user_id' => 999, 'judul' => 'Palsu', 'reviewed_by' => $author->id, 'status' => 'rejected', 'reject_reason' => 'Palsu'])
                ->assertRedirect('/laporan/'.$report->id)->assertSessionHas('success');
            $report->refresh();
            $this->assertSame('approved', $report->status);
            $this->assertSame($reviewer->id, $report->reviewed_by);
            $this->assertNotNull($report->reviewed_at);
            $this->assertNull($report->reject_reason);
            $this->assertSame('Laporan Terverifikasi', $report->judul);
            $this->get('/laporan/'.$report->id)->assertSee('Disetujui')->assertSee($reviewer->name)->assertDontSee('Setujui laporan');
        }
    }

    public function test_rejection_requires_reason_and_displays_escaped_reason_to_owner(): void
    {
        $author = $this->member('alumni');
        $report = $this->report($author, 'Perlu Perbaikan');
        $this->actingAs($this->member('koorda', $author->wilayah_id));
        foreach (['', '   ', str_repeat('字', 16384), ['invalid']] as $reason) {
            $this->from('/laporan/'.$report->id)->post('/laporan/'.$report->id.'/reject', ['reject_reason' => $reason])
                ->assertRedirect('/laporan/'.$report->id)->assertSessionHasErrors('reject_reason');
            $this->assertSame('submitted', $report->fresh()->status);
        }
        $this->post('/laporan/'.$report->id.'/reject', ['reject_reason' => '<script>alert(1)</script> Lengkapi data'])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('rejected', $report->fresh()->status);
        $this->actingAs($author)->get('/laporan/'.$report->id)->assertOk()->assertSee('<script>alert(1)</script> Lengkapi data')->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_foreign_koorda_cannot_decide_even_with_forged_region(): void
    {
        $author = $this->member('alumni');
        $report = $this->report($author, 'Rahasia');
        $this->actingAs($this->member('koorda'));
        foreach (['approve', 'reject'] as $action) {
            $this->post('/laporan/'.$report->id.'/'.$action.'?wilayah_id='.$author->wilayah_id, ['wilayah_id' => $author->wilayah_id, 'reject_reason' => 'Palsu'])->assertNotFound();
        }
        $this->assertSame('submitted', $report->fresh()->status);
    }

    public function test_guest_alumni_inactive_and_unassigned_koorda_are_denied(): void
    {
        $report = $this->report($this->member('alumni'), 'Laporan');
        foreach (['approve', 'reject'] as $action) { $this->post('/laporan/'.$report->id.'/'.$action)->assertRedirect(route('login')); }
        $inactive = $this->member('admin');
        $inactive->update(['status' => 'suspended']);
        $unassigned = $this->member('koorda');
        $unassigned->update(['wilayah_id' => null]);
        foreach ([$this->member('alumni'), $inactive, $unassigned] as $viewer) {
            $this->actingAs($viewer)->get('/laporan/review')->assertForbidden();
            foreach (['approve', 'reject'] as $action) { $this->post('/laporan/'.$report->id.'/'.$action, ['reject_reason' => 'Tidak sah'])->assertForbidden(); }
        }
    }

    public function test_self_review_and_non_submitted_decisions_are_rejected(): void
    {
        $reviewer = $this->member('admin');
        $own = $this->report($reviewer, 'Milik Sendiri');
        $this->actingAs($reviewer);
        foreach (['approve', 'reject'] as $action) { $this->post('/laporan/'.$own->id.'/'.$action, ['reject_reason' => 'Alasan'])->assertForbidden(); }
        foreach (['draft', 'approved', 'rejected'] as $status) {
            $report = $this->report($this->member('alumni'), 'Status terkunci', $status);
            foreach (['approve', 'reject'] as $action) { $this->post('/laporan/'.$report->id.'/'.$action, ['reject_reason' => 'Alasan'])->assertStatus(409); }
        }
    }

    public function test_failed_review_rolls_back_decision_and_metadata(): void
    {
        $report = $this->report($this->member('alumni'), 'Laporan');
        KajianReport::updated(fn () => throw new RuntimeException('Simulated review failure'));
        $this->actingAs($this->member('admin'))->post('/laporan/'.$report->id.'/approve')->assertServerError();
        $report->refresh();
        $this->assertSame('submitted', $report->status);
        $this->assertNull($report->reviewed_by);
        $this->assertNull($report->reviewed_at);
    }

    public function test_review_rechecks_status_after_binding_and_prevents_second_decision(): void
    {
        $report = $this->report($this->member('alumni'), 'Laporan');
        $changed = false;
        KajianReport::retrieved(function (KajianReport $record) use (&$changed) {
            if (! $changed) {
                $changed = true;
                DB::table('kajian_reports')->where('id', $record->id)->update(['status' => 'approved']);
            }
        });
        $this->actingAs($this->member('admin'))->post('/laporan/'.$report->id.'/reject', ['reject_reason' => 'Terlambat'])->assertStatus(409);
        $this->assertSame('approved', $report->fresh()->status);
        $this->assertNull($report->fresh()->reject_reason);
    }

    public function test_admin_region_filter_is_validated_visible_and_preserved_in_pagination(): void
    {
        $author = $this->member('alumni');
        for ($i = 1; $i <= 13; $i++) {
            $this->report($author, 'Antrean '.$i);
        }
        $this->report($this->member('alumni'), 'Luar Filter');
        $this->actingAs($this->member('admin'))->get('/laporan/review?wilayah_id='.$author->wilayah_id)
            ->assertOk()->assertSee('Antrean 1')->assertDontSee('Antrean 13')->assertDontSee('Luar Filter')
            ->assertSee('Tampilkan semua wilayah')->assertSee('wilayah_id='.$author->wilayah_id, false);
        $this->get('/laporan/review?wilayah_id='.$author->wilayah_id.'&page=2')->assertOk()->assertSee('Antrean 13')->assertDontSee('Luar Filter');
        foreach (['invalid', '999999', ['invalid']] as $value) {
            $this->getJson('/laporan/review?'.http_build_query(['wilayah_id' => $value]))->assertUnprocessable()->assertJsonValidationErrors('wilayah_id');
        }
    }

    public function test_cancelled_decision_does_not_report_success_or_mutate_status(): void
    {
        $report = $this->report($this->member('alumni'), 'Laporan');
        KajianReport::saving(fn () => false);
        $this->actingAs($this->member('admin'))->post('/laporan/'.$report->id.'/approve')->assertServerError()->assertSessionMissing('success');
        $this->assertSame('submitted', $report->fresh()->status);
        $this->assertNull($report->fresh()->reviewed_by);
    }

    private function member(string $role, ?int $region = null): User
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => $region ?? Wilayah::create(['name' => 'Wilayah'])->id]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function report(User $author, string $title, string $status = 'submitted'): KajianReport
    {
        return KajianReport::create(['user_id' => $author->id, 'wilayah_id' => $author->wilayah_id, 'judul' => $title, 'tanggal' => '2026-10-16', 'status' => $status]);
    }
}
