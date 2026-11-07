<?php

namespace Tests\Feature;

use App\Models\{KajianReport, Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class UnifiedReportWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_workspace_scopes_drafts_and_counts_without_exposing_peer_or_foreign_reports(): void
    {
        $region = Wilayah::create(['name' => 'Lokal']);
        $other = Wilayah::create(['name' => 'Asing']);
        $viewer = $this->member('koorda', $region->id);
        $peer = $this->member('alumni', $region->id);
        $foreign = $this->member('alumni', $other->id);
        $this->report($viewer, 'Draf Sendiri', 'draft', 7, 8);
        $this->report($peer, 'Draf Anggota', 'draft', 1, 1);
        $this->report($peer, 'Laporan Lokal', 'submitted', 2, 3);
        $this->report($foreign, 'Laporan Asing', 'approved', 4, 5);

        $this->actingAs($viewer)->get('/laporan?wilayah_id='.$other->id)->assertOk()
            ->assertSee('Draf Sendiri')->assertSee('Laporan Lokal')
            ->assertDontSee('Draf Anggota')->assertDontSee('Laporan Asing')
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 2 && $t['draft'] === 1
                && $t['submitted'] === 1 && $t['participants'] === 5 && $t['male'] === 2 && $t['female'] === 3);
        $this->get('/laporan?status=draft')->assertOk()->assertSee('Draf Sendiri')
            ->assertDontSee('Laporan Lokal')->assertDontSee('Draf Anggota')
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 1 && $t['participants'] === 0);
        $this->get('/laporan?status=submitted')->assertOk()->assertSee('Laporan Lokal')
            ->assertDontSee('Draf Sendiri')->assertDontSee('Laporan Asing');
    }

    public function test_workspace_filters_totals_and_export_share_visible_non_draft_rows(): void
    {
        $region = Wilayah::create(['name' => 'Lokal']);
        $author = $this->member('alumni', $region->id);
        $this->report($author, 'Dalam Filter', 'approved', 2, 3, '2026-10-01');
        $this->report($author, 'Di Luar Tanggal', 'approved', 9, 9, '2026-09-30');
        $this->report($author, 'Status Lain', 'rejected', 1, 1, '2026-10-01');
        $this->report($author, 'Draf Tak Diekspor', 'draft', 5, 5, '2026-10-01');
        $query = '?'.http_build_query(['wilayah_id' => $region->id, 'status' => 'approved', 'dari' => '2026-10-01', 'sampai' => '2026-10-01']);

        $this->actingAs($this->member('admin'))->get('/laporan'.$query)->assertOk()
            ->assertSee('Dalam Filter')->assertDontSee('Di Luar Tanggal')->assertDontSee('Status Lain')
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 1 && $t['approved'] === 1
                && $t['participants'] === 5 && $t['male'] === 2 && $t['female'] === 3);
        $csv = $this->get('/laporan/ekspor'.$query)->assertOk()->streamedContent();
        $this->assertStringContainsString('Dalam Filter', $csv);
        foreach (['Di Luar Tanggal', 'Status Lain', 'Draf Tak Diekspor'] as $title) {
            $this->assertStringNotContainsString($title, $csv);
        }
        $this->getJson('/laporan/ekspor?status=draft')->assertUnprocessable()->assertJsonValidationErrors('status');
    }

    public function test_submitted_filter_keeps_own_report_visible_without_self_review_action(): void
    {
        $region = Wilayah::create(['name' => 'Lokal']);
        $reviewer = $this->member('koorda', $region->id);
        $peer = $this->member('alumni', $region->id);
        $own = $this->report($reviewer, 'Laporan Sendiri', 'submitted');
        $other = $this->report($peer, 'Menunggu Keputusan', 'submitted');

        $response = $this->actingAs($reviewer)->get('/laporan?status=submitted')->assertOk()
            ->assertSee('Laporan Sendiri')->assertSee('Menunggu Keputusan');
        $document = new \DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $links = new \DOMXPath($document);
        $this->assertSame(0, $links->query('//a[@href="'.route('laporan.show', $own).'"][@data-report-focus="approve"]')->length);
        $this->assertSame(1, $links->query('//a[@href="'.route('laporan.show', $other).'"][@data-report-focus="approve"]')->length);
        $this->post(route('laporan.approve', $own))->assertForbidden();
        $this->post(route('laporan.approve', $other))->assertRedirect();
        $this->post(route('laporan.approve', $other))->assertStatus(409);
    }

    public function test_only_owner_may_delete_an_unsent_draft_and_its_private_photo(): void
    {
        $author = $this->member('koorda', Wilayah::create(['name' => 'Lokal'])->id);
        $photo = UploadedFile::fake()->image('dokumentasi.jpg')->store('report-photos', 'local');
        $draft = $this->report($author, 'Draf', 'draft');
        $draft->update(['foto_url' => $photo]);
        $sent = $this->report($author, 'Terkirim', 'submitted');
        $other = $this->member('koorda', $author->wilayah_id);

        $this->actingAs($other)->deleteJson('/laporan/'.$draft->id)->assertNotFound();
        $this->actingAs($author)->deleteJson('/laporan/'.$sent->id)->assertStatus(409);
        $this->deleteJson('/laporan/'.$draft->id)->assertOk()->assertJsonPath('deleted', true);
        $this->assertDatabaseMissing('kajian_reports', ['id' => $draft->id]);
        Storage::disk('local')->assertMissing($photo);
        $this->deleteJson('/laporan/'.$draft->id)->assertNotFound();
    }

    public function test_failed_delete_preserves_draft_and_private_photo(): void
    {
        $author = $this->member('koorda', Wilayah::create(['name' => 'Lokal'])->id);
        $photo = UploadedFile::fake()->image('dokumentasi.jpg')->store('report-photos', 'local');
        $draft = $this->report($author, 'Draf', 'draft');
        $draft->update(['foto_url' => $photo]);
        KajianReport::deleting(fn () => throw new RuntimeException('Simulated delete failure'));

        $this->actingAs($author)->deleteJson('/laporan/'.$draft->id)->assertServerError();
        $this->assertDatabaseHas('kajian_reports', ['id' => $draft->id]);
        Storage::disk('local')->assertExists($photo);
    }

    public function test_deleting_the_only_draft_on_page_two_returns_to_the_last_valid_filtered_page(): void
    {
        $author = $this->member('koorda', Wilayah::create(['name' => 'Lokal'])->id);
        $oldest = $this->report($author, 'Draf Pertama', 'draft');
        for ($i = 2; $i <= 13; $i++) {
            $this->report($author, 'Draf '.$i, 'draft');
        }

        $this->actingAs($author)->get('/laporan?status=draft&page=2')->assertOk()->assertSee('Draf Pertama');
        $this->deleteJson('/laporan/'.$oldest->id)->assertOk()->assertJsonPath('deleted', true);
        $this->get('/laporan?status=draft&page=2')
            ->assertRedirect('/laporan?wilayah_id='.$author->wilayah_id.'&status=draft');
        $this->get('/laporan?status=draft')->assertOk()
            ->assertDontSee('Draf Pertama')
            ->assertViewHas('reports', fn ($reports) => $reports->count() === 12 && $reports->total() === 12);
    }

    public function test_delete_is_denied_to_guest_alumni_and_inactive_members(): void
    {
        $author = $this->member('koorda', Wilayah::create(['name' => 'Lokal'])->id);
        $draft = $this->report($author, 'Draf', 'draft');
        $this->delete('/laporan/'.$draft->id)->assertRedirect(route('login'));
        $this->actingAs($this->member('alumni', $author->wilayah_id))->deleteJson('/laporan/'.$draft->id)->assertForbidden();
        $inactive = $this->member('koorda', $author->wilayah_id);
        $inactive->update(['status' => 'suspended']);
        $this->actingAs($inactive)->deleteJson('/laporan/'.$draft->id)->assertForbidden();
        $this->assertDatabaseHas('kajian_reports', ['id' => $draft->id]);
    }

    private function member(string $role, ?int $regionId = null): User
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => $regionId]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function report(User $author, string $title, string $status, int $male = 0, int $female = 0, string $date = '2026-10-16'): KajianReport
    {
        return KajianReport::create([
            'user_id' => $author->id, 'wilayah_id' => $author->wilayah_id,
            'judul' => $title, 'tanggal' => $date, 'status' => $status,
            'peserta_l' => $male, 'peserta_p' => $female, 'total_peserta' => 999,
        ]);
    }
}
