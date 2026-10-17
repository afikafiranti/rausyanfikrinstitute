<?php

namespace Tests\Feature;

use App\Models\{KajianReport, Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KajianReportSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_koorda_summary_and_export_ignore_forged_region_and_exclude_drafts(): void
    {
        $viewer = $this->member('koorda');
        $author = $this->member('alumni', $viewer->wilayah_id);
        $this->report($author, 'Kajian Lokal', 'submitted');
        $this->report($author, 'Draf Privat', 'draft');
        $foreign = $this->member('alumni');
        $this->report($foreign, 'Kajian Asing', 'approved');
        $query = '?wilayah_id='.$foreign->wilayah_id;
        $this->actingAs($viewer)->get('/laporan/rekap'.$query)->assertOk()->assertSee('Kajian Lokal')->assertDontSee('Kajian Asing')->assertDontSee('Draf Privat')
            ->assertViewHas('totals', fn ($totals) => $totals['reports'] === 1 && $totals['participants'] === 5);
        $csv = $this->get('/laporan/ekspor'.$query)->assertOk()->streamedContent();
        $this->assertStringContainsString('Kajian Lokal', $csv);
        $this->assertStringNotContainsString('Kajian Asing', $csv);
        $this->assertStringNotContainsString('Draf Privat', $csv);
    }

    public function test_admins_get_global_totals_or_explicit_region_and_inclusive_date_status_filters(): void
    {
        $a = $this->member('alumni');
        $b = $this->member('alumni');
        $this->report($a, 'Awal Periode', 'approved', '2026-10-01');
        $this->report($a, 'Akhir Periode', 'approved', '2026-10-16');
        $this->report($b, 'Ditolak Wilayah Lain', 'rejected');
        $this->report($a, 'Laporan Dikirim', 'submitted');
        $this->report($a, 'Luar Periode', 'approved', '2026-09-30');
        foreach (['admin', 'super_admin'] as $role) {
            $this->actingAs($this->member($role))->get('/laporan/rekap')->assertOk()
                ->assertSee('Ditolak Wilayah Lain')->assertViewHas('totals', fn ($t) => $t['reports'] === 5 && $t['participants'] === 25 && $t['approved'] === 3 && $t['rejected'] === 1 && $t['submitted'] === 1);
            $query = '?'.http_build_query(['wilayah_id' => $a->wilayah_id, 'dari' => '2026-10-01', 'sampai' => '2026-10-16', 'status' => 'approved']);
            $this->get('/laporan/rekap'.$query)->assertOk()->assertSee('Awal Periode')->assertSee('Akhir Periode')->assertDontSee('Luar Periode')->assertDontSee('Ditolak Wilayah Lain')->assertDontSee('Laporan Dikirim')
                ->assertViewHas('totals', fn ($t) => $t['reports'] === 2 && $t['participants'] === 10);
            $csv = $this->get('/laporan/ekspor'.$query)->assertOk()->streamedContent();
            $this->assertStringContainsString('Awal Periode', $csv);
            $this->assertStringContainsString('Akhir Periode', $csv);
            $this->assertStringNotContainsString('Luar Periode', $csv);
        }
    }

    public function test_summary_totals_and_export_cover_every_page_in_stable_order(): void
    {
        $author = $this->member('alumni');
        for ($i = 1; $i <= 14; $i++) { $this->report($author, 'Kajian '.$i); }
        $this->actingAs($this->member('admin'))->get('/laporan/rekap?status=submitted')->assertOk()
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 14 && $t['participants'] === 70)
            ->assertSee('status=submitted', false);
        $this->get('/laporan/rekap?status=submitted&page=2')->assertOk()->assertSee('Kajian 2')->assertSee('Kajian 1');
        $rows = $this->csvRows($this->get('/laporan/ekspor?status=submitted&page=2')->assertOk()->streamedContent());
        $this->assertCount(15, $rows);
        $this->assertSame('Kajian 1', $rows[1][2]);
        $this->assertSame('Kajian 14', $rows[14][2]);
    }

    public function test_csv_is_private_quoted_and_neutralizes_formulas_without_exposing_private_fields(): void
    {
        $author = $this->member('alumni');
        $author->update(['name' => '=HYPERLINK("bad")']);
        $report = $this->report($author, "Judul, dengan \"kutip\"\ndan baris");
        $report->update(['pemateri' => '+SUM(1,1)', 'tempat' => '  @formula', 'reject_reason' => "\t=cmd()", 'catatan' => 'CATATAN_RAHASIA', 'foto_url' => 'report-photos/RAHASIA.jpg']);
        $response = $this->actingAs($this->member('admin'))->get('/laporan/ekspor')->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $csv = $response->streamedContent();
        $rows = $this->csvRows($csv);
        $this->assertSame($report->judul, $rows[1][2]);
        $this->assertSame("'=HYPERLINK(\"bad\")", $rows[1][4]);
        $this->assertSame("'+SUM(1,1)", $rows[1][5]);
        $this->assertSame("'  @formula", $rows[1][6]);
        $this->assertSame("'\t=cmd()", $rows[1][15]);
        $this->assertSame('5', $rows[1][9]);
        $this->assertStringNotContainsString('CATATAN_RAHASIA', $csv);
        $this->assertStringNotContainsString('RAHASIA.jpg', $csv);
        $this->assertStringNotContainsString($author->email, $csv);
    }

    public function test_filters_are_validated_for_both_endpoints(): void
    {
        $this->actingAs($this->member('admin'));
        foreach (['/laporan/rekap', '/laporan/ekspor'] as $url) {
            foreach ([['status' => 'draft'], ['dari' => '2026-02-30'], ['dari' => '2026-10-16', 'sampai' => '2026-10-01'], ['wilayah_id' => 999999], ['status' => ['approved']]] as $query) {
                $this->getJson($url.'?'.http_build_query($query))->assertUnprocessable();
            }
        }
    }

    public function test_guests_alumni_inactive_and_unassigned_koorda_cannot_export_or_summarize(): void
    {
        foreach (['/laporan/rekap', '/laporan/ekspor'] as $url) { $this->get($url)->assertRedirect(route('login')); }
        $inactive = $this->member('admin');
        $inactive->update(['status' => 'pending']);
        $unassigned = $this->member('koorda');
        $unassigned->update(['wilayah_id' => null]);
        foreach ([$this->member('alumni'), $inactive, $unassigned] as $viewer) {
            foreach (['/laporan/rekap', '/laporan/ekspor'] as $url) { $this->actingAs($viewer)->get($url)->assertForbidden(); }
        }
    }

    public function test_empty_summary_has_zero_totals_and_export_only_header(): void
    {
        $this->actingAs($this->member('admin'))->get('/laporan/rekap')->assertOk()->assertSee('Belum ada laporan sesuai filter.')
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 0 && $t['participants'] === 0);
        $rows = $this->csvRows($this->get('/laporan/ekspor')->assertOk()->streamedContent());
        $this->assertCount(1, $rows);
    }

    private function csvRows(string $csv): array
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, ltrim($csv, "\xEF\xBB\xBF"));
        rewind($stream);
        $rows = [];
        while (($row = fgetcsv($stream, 0, ',', '"', '')) !== false) { $rows[] = $row; }
        fclose($stream);

        return $rows;
    }

    private function member(string $role, ?int $region = null): User
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => $region ?? Wilayah::create(['name' => 'Wilayah'])->id]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function report(User $author, string $title, string $status = 'submitted', string $date = '2026-10-16'): KajianReport
    {
        return KajianReport::create(['user_id' => $author->id, 'wilayah_id' => $author->wilayah_id, 'judul' => $title, 'tanggal' => $date, 'status' => $status, 'peserta_l' => 2, 'peserta_p' => 3, 'total_peserta' => 999]);
    }
}
