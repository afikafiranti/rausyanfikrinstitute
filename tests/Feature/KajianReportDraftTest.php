<?php

namespace Tests\Feature;

use App\Models\KajianReport;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class KajianReportDraftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_member_saves_a_draft_with_server_owned_identity_region_totals_and_status(): void
    {
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $viewer = $this->member('alumni', $local->id);
        $this->actingAs($viewer)->post('/laporan?wilayah_id='.$other->id, $this->data() + [
            'wilayah_id' => $other->id, 'user_id' => 999, 'total_peserta' => 999,
            'status' => 'approved', 'reviewed_by' => $viewer->id, 'reviewed_at' => '2026-01-01',
            'reject_reason' => 'Dipalsukan', 'foto_url' => 'https://attacker.invalid/foto.jpg',
        ])->assertRedirect()->assertSessionHas('success');

        $report = KajianReport::firstOrFail();
        $this->assertSame($viewer->id, $report->user_id);
        $this->assertSame($local->id, $report->wilayah_id);
        $this->assertSame(7, $report->total_peserta);
        $this->assertSame('draft', $report->status);
        $this->assertNull($report->reviewed_by);
        $this->assertNull($report->reviewed_at);
        $this->assertNull($report->reject_reason);
        $this->assertNull($report->foto_url);
        $this->get('/laporan/'.$report->id)->assertOk()->assertSee('Kajian Tersimpan')->assertSee('Draf');
    }

    public function test_admin_and_super_admin_without_region_can_choose_a_valid_region(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Pilihan']);
        foreach (['admin', 'super_admin'] as $role) {
            $viewer = $this->member($role);
            $this->actingAs($viewer)->get('/laporan/buat')->assertOk()->assertSee('Wilayah Pilihan');
            $this->post('/laporan', $this->data() + ['wilayah_id' => $region->id])->assertRedirect()->assertSessionHasNoErrors();
            $this->assertDatabaseHas('kajian_reports', ['user_id' => $viewer->id, 'wilayah_id' => $region->id, 'status' => 'draft']);
            $this->post('/laporan', $this->data() + ['wilayah_id' => 999999])->assertSessionHasErrors('wilayah_id');
        }
        $this->assertDatabaseCount('kajian_reports', 2);
    }

    public function test_alumni_cannot_read_another_members_report_or_own_old_region_report(): void
    {
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $viewer = $this->member('alumni', $local->id);
        $own = $this->report($viewer, $local->id, 'Laporan Sendiri');
        $foreign = $this->report($this->member('alumni', $local->id), $local->id, 'Draf Orang Lain');
        $old = $this->report($viewer, $other->id, 'Wilayah Akun Lama');
        $this->actingAs($viewer)->get('/laporan')->assertOk()->assertSee('Laporan Sendiri')->assertDontSee('Draf Orang Lain')->assertDontSee('Wilayah Akun Lama');
        $this->get('/laporan/'.$own->id)->assertOk();
        foreach ([$foreign, $old] as $report) {
            $this->get('/laporan/'.$report->id)->assertNotFound();
        }
    }

    public function test_koorda_sees_own_drafts_and_non_drafts_only_in_their_region(): void
    {
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $viewer = $this->member('koorda', $local->id);
        $own = $this->report($viewer, $local->id, 'Draf Koorda');
        $author = $this->member('alumni', $local->id);
        $draft = $this->report($author, $local->id, 'Draf Privat');
        $submitted = $this->report($author, $local->id, 'Laporan Terkirim', 'submitted');
        $foreign = $this->report($author, $other->id, 'Laporan Luar Wilayah', 'submitted');
        $this->actingAs($viewer)->get('/laporan?wilayah_id='.$other->id)->assertOk()->assertSee('Draf Koorda')->assertSee('Laporan Terkirim')->assertDontSee('Draf Privat')->assertDontSee('Laporan Luar Wilayah');
        foreach ([$own, $submitted] as $report) { $this->get('/laporan/'.$report->id)->assertOk(); }
        foreach ([$draft, $foreign] as $report) { $this->get('/laporan/'.$report->id)->assertNotFound(); }
        $this->post('/laporan', $this->data() + ['wilayah_id' => $other->id])->assertRedirect();
        $this->assertDatabaseHas('kajian_reports', ['user_id' => $viewer->id, 'judul' => 'Kajian Tersimpan', 'wilayah_id' => $local->id]);
    }

    public function test_admins_can_read_cross_region_and_legacy_unassigned_reports(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Lain']);
        $author = $this->member('alumni', $region->id);
        $report = $this->report($author, $region->id, 'Lintas Wilayah');
        $legacy = $this->report($author, null, 'Wilayah Belum Ditentukan');
        foreach (['admin', 'super_admin'] as $role) {
            $this->actingAs($this->member($role))->get('/laporan')->assertOk()->assertSee('Lintas Wilayah')->assertSee('Wilayah Belum Ditentukan');
            $this->get('/laporan/'.$report->id)->assertOk();
            $this->get('/laporan/'.$legacy->id)->assertOk();
        }
    }

    public function test_guests_inactive_accounts_and_non_admins_without_region_cannot_use_drafts(): void
    {
        foreach (['/laporan', '/laporan/buat'] as $url) { $this->get($url)->assertRedirect(route('login')); }
        $this->post('/laporan', $this->data())->assertRedirect(route('login'));
        $region = Wilayah::create(['name' => 'Wilayah']);
        $noRole = User::factory()->create(['status' => 'active', 'wilayah_id' => $region->id]);
        $viewers = [$this->member('alumni'), $this->member('koorda'), $this->member('alumni', $region->id, 'pending'), $this->member('admin', $region->id, 'suspended'), $noRole];
        foreach ($viewers as $viewer) {
            $this->actingAs($viewer)->get('/laporan')->assertForbidden();
            $this->get('/laporan/buat')->assertForbidden();
            $this->post('/laporan', $this->data() + ['wilayah_id' => $region->id])->assertForbidden();
        }
        $this->assertDatabaseCount('kajian_reports', 0);
    }

    public function test_validation_preserves_input_and_rejects_invalid_schema_values_and_images(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        $this->actingAs($this->member('alumni', $region->id));
        $this->from('/laporan/buat')->post('/laporan', array_replace($this->data(), [
            'tanggal' => '2026-02-30', 'judul' => str_repeat('a', 181), 'peserta_l' => -1, 'peserta_p' => 2147483648,
            'durasi_menit' => 'invalid', 'pemateri' => str_repeat('p', 151), 'tempat' => str_repeat('t', 151), 'catatan' => str_repeat('字', 16384),
            'foto' => UploadedFile::fake()->image('foto.jpg')->size(2049),
        ]))->assertRedirect('/laporan/buat')->assertSessionHasErrors(['tanggal', 'judul', 'peserta_l', 'peserta_p', 'durasi_menit', 'pemateri', 'tempat', 'catatan', 'foto']);
        $this->get('/laporan/buat')->assertOk()->assertSee('role="alert"', false);
        $this->post('/laporan', $this->data() + ['foto' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')])->assertSessionHasErrors('foto');
        $this->assertDatabaseCount('kajian_reports', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('report-photos'));
    }

    public function test_photo_is_private_and_only_readable_by_viewers_of_the_report(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        $viewer = $this->member('alumni', $region->id);
        $photo = UploadedFile::fake()->image('foto.jpg');
        $bytes = file_get_contents($photo->getPathname());
        $this->actingAs($viewer)->post('/laporan', $this->data() + ['foto' => $photo])->assertRedirect();
        $report = KajianReport::firstOrFail();
        Storage::disk('local')->assertExists($report->foto_url);
        $this->assertSame([], Storage::disk('public')->allFiles('report-photos'));
        $response = $this->get('/laporan/'.$report->id.'/foto')->assertOk()->assertStreamedContent($bytes);
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->actingAs($this->member('alumni', $region->id))->get('/laporan/'.$report->id.'/foto')->assertNotFound();
        $this->actingAs($this->member('koorda', $region->id))->get('/laporan/'.$report->id.'/foto')->assertNotFound();
        $this->actingAs($this->member('admin'))->get('/laporan/'.$report->id.'/foto')->assertOk();
    }

    public function test_failed_database_save_cleans_private_photo_and_rolls_back_record(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        KajianReport::created(fn () => throw new RuntimeException('Simulated report post-insert failure'));
        $this->actingAs($this->member('alumni', $region->id))->post('/laporan', $this->data() + ['foto' => UploadedFile::fake()->image('foto.jpg')])->assertServerError();
        $this->assertDatabaseCount('kajian_reports', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('report-photos'));
    }

    public function test_cancelled_save_cleans_photo_without_reporting_success(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        KajianReport::saving(fn () => false);
        $this->actingAs($this->member('alumni', $region->id))
            ->post('/laporan', $this->data() + ['foto' => UploadedFile::fake()->image('foto.png')])
            ->assertServerError()->assertSessionMissing('success');
        $this->assertDatabaseCount('kajian_reports', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('report-photos'));
    }

    public function test_photo_endpoint_rejects_unsafe_legacy_paths_and_revoked_account_access(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah']);
        $viewer = $this->member('alumni', $region->id);
        $report = $this->report($viewer, $region->id, 'Laporan Privat');
        Storage::disk('local')->put('unrelated.png', 'private bytes');
        $this->actingAs($viewer);
        foreach (['report-photos/../unrelated.png', 'unrelated.png', 'https://example.test/foto.png', 'report-photos/missing.png'] as $path) {
            $report->update(['foto_url' => $path]);
            $this->get('/laporan/'.$report->id.'/foto')->assertNotFound();
        }
        $viewer->update(['status' => 'suspended']);
        $this->get('/laporan/'.$report->id)->assertForbidden();
        $this->get('/laporan/'.$report->id.'/foto')->assertForbidden();
    }

    private function data(): array
    {
        return ['tanggal' => '2026-10-16', 'judul' => 'Kajian Tersimpan', 'tempat' => 'Ruang Kajian', 'pemateri' => 'Pemateri', 'peserta_l' => 3, 'peserta_p' => 4, 'durasi_menit' => 90, 'catatan' => 'Catatan kegiatan.'];
    }

    private function member(string $role, ?int $wilayahId = null, string $status = 'active'): User
    {
        $user = User::factory()->create(['status' => $status, 'wilayah_id' => $wilayahId]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function report(User $user, ?int $wilayahId, string $title, string $status = 'draft'): KajianReport
    {
        return KajianReport::create(['user_id' => $user->id, 'wilayah_id' => $wilayahId, 'tanggal' => '2026-10-16', 'judul' => $title, 'status' => $status]);
    }
}
