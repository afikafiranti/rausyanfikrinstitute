<?php

namespace Tests\Feature;

use App\Models\{KajianReport, Role, User, Wilayah};
use App\Support\ReportExcel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
        $this->actingAs($viewer)->get('/laporan/rekap'.$query)
            ->assertRedirect('/laporan?wilayah_id='.$viewer->wilayah_id);
        $this->get('/laporan'.$query)->assertOk()->assertSee('Kajian Lokal')->assertDontSee('Kajian Asing')->assertDontSee('Draf Privat')
            ->assertViewHas('totals', fn ($totals) => $totals['reports'] === 1 && $totals['participants'] === 5);
        $titles = array_column($this->downloadSheet($this->get('/laporan/ekspor'.$query)->assertOk()->streamedContent())->toArray(), 2);
        $this->assertContains('Kajian Lokal', $titles);
        $this->assertNotContains('Kajian Asing', $titles);
        $this->assertNotContains('Draf Privat', $titles);
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
            $this->actingAs($this->member($role))->get('/laporan/rekap')->assertRedirect('/laporan');
            $this->get('/laporan')->assertOk()
                ->assertSee('Ditolak Wilayah Lain')->assertViewHas('totals', fn ($t) => $t['reports'] === 5 && $t['participants'] === 25 && $t['approved'] === 3 && $t['rejected'] === 1 && $t['submitted'] === 1);
            $query = '?'.http_build_query(['wilayah_id' => $a->wilayah_id, 'dari' => '2026-10-01', 'sampai' => '2026-10-16', 'status' => 'approved']);
            $redirect = $this->get('/laporan/rekap'.$query)->assertRedirect();
            $this->assertSame('/laporan', parse_url($redirect->headers->get('Location'), PHP_URL_PATH));
            parse_str(ltrim($query, '?'), $expectedFilters);
            parse_str(parse_url($redirect->headers->get('Location'), PHP_URL_QUERY) ?? '', $redirectFilters);
            $this->assertEquals($expectedFilters, $redirectFilters);
            $this->get('/laporan'.$query)->assertOk()->assertSee('Awal Periode')->assertSee('Akhir Periode')->assertDontSee('Luar Periode')->assertDontSee('Ditolak Wilayah Lain')->assertDontSee('Laporan Dikirim')
                ->assertViewHas('totals', fn ($t) => $t['reports'] === 2 && $t['participants'] === 10);
            $titles = array_column($this->downloadSheet($this->get('/laporan/ekspor'.$query)->assertOk()->streamedContent())->toArray(), 2);
            $this->assertContains('Awal Periode', $titles);
            $this->assertContains('Akhir Periode', $titles);
            $this->assertNotContains('Luar Periode', $titles);
        }
    }

    public function test_summary_totals_and_export_cover_every_page_in_stable_order(): void
    {
        $author = $this->member('alumni');
        for ($i = 1; $i <= 14; $i++) { $this->report($author, 'Kajian '.$i); }
        $this->actingAs($this->member('admin'))->get('/laporan?status=submitted')->assertOk()
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 14 && $t['participants'] === 70)
            ->assertSee('status=submitted', false);
        $this->get('/laporan?status=submitted&page=2')->assertOk()->assertSee('Kajian 13')->assertSee('Kajian 14');
        $sheet = $this->downloadSheet($this->get('/laporan/ekspor?status=submitted&page=2')->assertOk()->streamedContent());
        $this->assertSame(15, $sheet->getHighestDataRow());
        $titles = array_column($sheet->toArray(), 2);
        $this->assertContains('Kajian 1', $titles);
        $this->assertContains('Kajian 14', $titles);
    }

    public function test_excel_is_private_typed_and_formula_safe_without_exposing_private_fields(): void
    {
        $author = $this->member('alumni');
        $author->update(['name' => '=HYPERLINK("bad")']);
        $report = $this->report($author, "Judul, dengan \"kutip\"\ndan baris");
        $report->update(['pemateri' => '+SUM(1,1)', 'tempat' => '  @formula', 'reject_reason' => "\t=cmd()", 'catatan' => 'CATATAN_RAHASIA', 'foto_url' => 'report-photos/RAHASIA.jpg']);
        $response = $this->actingAs($this->member('admin'))->get('/laporan/ekspor')->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));
        $this->assertSame('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $sheet = $this->downloadSheet($response->streamedContent());
        $this->assertSame('Laporan', $sheet->getTitle());
        $this->assertSame('A2', $sheet->getFreezePane());
        $this->assertSame('A1:P1', $sheet->getAutoFilter()->getRange());
        $this->assertTrue($sheet->getStyle('A1')->getFont()->getBold());
        $this->assertSame($report->judul, $sheet->getCell('C2')->getValue());
        $this->assertSame('Menunggu', $sheet->getCell('L2')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('L2')->getDataType());
        foreach (['E2' => '=HYPERLINK("bad")', 'F2' => '+SUM(1,1)', 'G2' => '  @formula', 'P2' => "\t=cmd()"] as $address => $value) {
            $this->assertSame($value, $sheet->getCell($address)->getValue());
            $this->assertSame(DataType::TYPE_STRING, $sheet->getCell($address)->getDataType());
        }
        foreach (['A2' => $report->id, 'H2' => 2, 'I2' => 3, 'J2' => 5] as $address => $value) {
            $this->assertSame($value, $sheet->getCell($address)->getValue());
            $this->assertSame(DataType::TYPE_NUMERIC, $sheet->getCell($address)->getDataType());
        }
        $values = json_encode($sheet->toArray());
        $this->assertStringNotContainsString('CATATAN_RAHASIA', $values);
        $this->assertStringNotContainsString('RAHASIA.jpg', $values);
        $this->assertStringNotContainsString($author->email, $values);
    }

    public function test_filters_are_validated_for_both_endpoints(): void
    {
        $this->actingAs($this->member('admin'));
        foreach (['/laporan/rekap', '/laporan/ekspor'] as $url) {
            foreach ([['status' => 'draft'], ['dari' => '2026-02-30'], ['dari' => '2026-10-16', 'sampai' => '2026-10-01'], ['wilayah_id' => 999999], ['status' => ['approved']]] as $query) {
                $this->getJson($url.'?'.http_build_query($query))->assertUnprocessable();
            }
        }
        $this->get('/laporan?status=draft')->assertOk();
        foreach ([['dari' => '2026-02-30'], ['wilayah_id' => 999999], ['status' => ['approved']]] as $query) {
            $this->getJson('/laporan?'.http_build_query($query))->assertUnprocessable();
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
        $this->actingAs($this->member('admin'))->get('/laporan')->assertOk()
            ->assertViewHas('totals', fn ($t) => $t['reports'] === 0 && $t['participants'] === 0);
        $sheet = $this->downloadSheet($this->get('/laporan/ekspor')->assertOk()->streamedContent());
        $this->assertSame(1, $sheet->getHighestDataRow());
    }

    public function test_excel_limit_rejects_extra_rows_and_allows_exact_limit_or_narrow_filter(): void
    {
        $author = $this->member('alumni');
        $now = now();
        for ($start = 1; $start <= ReportExcel::MAX_EXPORT_ROWS + 1; $start += 500) {
            $batch = [];
            for ($i = $start; $i < min($start + 500, ReportExcel::MAX_EXPORT_ROWS + 2); $i++) {
                $batch[] = [
                    'user_id' => $author->id, 'wilayah_id' => $author->wilayah_id,
                    'tanggal' => '2026-10-16', 'judul' => 'Batas '.$i, 'status' => 'submitted',
                    'created_at' => $now, 'updated_at' => $now,
                ];
            }
            DB::table('kajian_reports')->insert($batch);
        }
        $this->report($author, 'Filter Disetujui', 'approved');
        $admin = $this->member('admin');
        $this->actingAs($admin)->getJson('/laporan/ekspor?status=submitted')
            ->assertUnprocessable()->assertJsonValidationErrors('export');
        try {
            ReportExcel::export(KajianReport::query()->visibleTo($admin)->where('status', 'submitted')
                ->select(['id', 'tanggal', 'judul', 'wilayah_id', 'user_id', 'pemateri', 'tempat', 'peserta_l', 'peserta_p', 'durasi_menit', 'status', 'reviewed_by', 'reviewed_at', 'reject_reason'])
                ->with(['wilayah:id,name', 'user:id,name', 'reviewer:id,name']));
            $this->fail('Ekspor yang berubah saat iterasi tidak boleh menghasilkan workbook terpotong.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('export', $exception->errors());
        }
        $approved = $this->get('/laporan/ekspor?status=approved')->assertOk();
        $this->assertSame(2, $this->downloadSheet($approved->streamedContent())->getHighestDataRow());

        DB::table('kajian_reports')->where('judul', 'Batas 5001')->delete();
        $atLimit = $this->get('/laporan/ekspor?status=submitted')->assertOk();
        $this->assertSame(ReportExcel::MAX_EXPORT_ROWS + 1, $this->downloadSheet($atLimit->streamedContent())->getHighestDataRow());
    }

    private function downloadSheet(string $content): \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
    {
        $path = tempnam(sys_get_temp_dir(), 'report_export_');
        file_put_contents($path, $content);
        $this->beforeApplicationDestroyed(fn () => @unlink($path));

        return IOFactory::load($path)->getActiveSheet();
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
