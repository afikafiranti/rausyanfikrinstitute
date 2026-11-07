<?php

namespace Tests\Feature;

use App\Models\{KajianReport, Role, User, Wilayah};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KajianReportModalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_authorized_modal_requests_return_private_fragments_and_normal_gets_keep_full_pages(): void
    {
        $owner = $this->member('koorda');
        $report = $this->draft($owner);
        $this->actingAs($owner);

        foreach (['/laporan/buat', '/laporan/'.$report->id.'/edit', '/laporan/'.$report->id] as $url) {
            $modal = $this->get($url, ['X-Report-Modal' => '1'])
                ->assertOk()
                ->assertHeader('X-Report-Modal', '1')
                ->assertHeader('Vary', 'X-Report-Modal')
                ->assertSee('data-report-modal-content', false)
                ->assertDontSee('<html', false);
            $this->assertStringContainsString('private', $modal->headers->get('Cache-Control'));
            $this->assertStringContainsString('no-store', $modal->headers->get('Cache-Control'));

            $fullPage = $this->get($url)->assertOk()
                ->assertSee('<html', false);
            $this->assertStringContainsString('private', $fullPage->headers->get('Cache-Control'));
            $this->assertStringContainsString('no-store', $fullPage->headers->get('Cache-Control'));
        }
    }

    public function test_modal_requests_keep_existing_role_scope_and_draft_locks(): void
    {
        $owner = $this->member('koorda');
        $draft = $this->draft($owner);
        $local = $this->member('koorda', $owner->wilayah_id);
        $foreign = $this->member('koorda');
        $alumni = $this->member('alumni', $owner->wilayah_id);
        $headers = ['X-Report-Modal' => '1'];

        $this->actingAs($alumni)->get('/laporan/buat', $headers)->assertForbidden();
        $this->get('/laporan/'.$draft->id, $headers)->assertForbidden();
        $this->actingAs($local)->get('/laporan/'.$draft->id, $headers)->assertNotFound();
        $this->get('/laporan/'.$draft->id.'/edit', $headers)->assertNotFound();
        $this->actingAs($foreign)->get('/laporan/'.$draft->id, $headers)->assertNotFound();

        $draft->update(['status' => KajianReport::STATUS_SUBMITTED]);
        $this->actingAs($local)->get('/laporan/'.$draft->id, $headers)
            ->assertOk()->assertSee('data-report-modal-content', false);
        $this->actingAs($foreign)->get('/laporan/'.$draft->id, $headers)->assertNotFound();
        $this->actingAs($owner)->get('/laporan/'.$draft->id.'/edit', $headers)->assertStatus(409);
    }

    public function test_json_create_and_update_replace_private_photo_then_submit_without_redirect(): void
    {
        $owner = $this->member('koorda');
        $this->actingAs($owner);

        $created = $this->post('/laporan', $this->data() + ['foto' => UploadedFile::fake()->image('first.jpg')], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonStructure(['message', 'modal_url']);
        $report = KajianReport::query()->sole();
        $created->assertJsonPath('modal_url', route('laporan.show', $report));
        $this->assertSame('draft', $report->status);
        $this->assertSame($owner->id, $report->user_id);
        $this->assertSame($owner->wilayah_id, $report->wilayah_id);
        $this->assertSame(7, $report->total_peserta);
        $this->assertNotNull($report->foto_url);
        Storage::disk('local')->assertExists($report->foto_url);
        $firstPhoto = $report->foto_url;

        $this->put('/laporan/'.$report->id, $this->data('Draf diperbarui'), ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('modal_url', route('laporan.show', $report));
        $this->assertSame($firstPhoto, $report->fresh()->foto_url);
        Storage::disk('local')->assertExists($firstPhoto);

        $this->put('/laporan/'.$report->id, $this->data('Foto baru') + ['foto' => UploadedFile::fake()->image('second.png')], ['Accept' => 'application/json'])
            ->assertOk()->assertJsonPath('modal_url', route('laporan.show', $report));
        $report->refresh();
        $this->assertSame('Foto baru', $report->judul);
        $this->assertNotSame($firstPhoto, $report->foto_url);
        Storage::disk('local')->assertMissing($firstPhoto);
        Storage::disk('local')->assertExists($report->foto_url);

        $this->postJson('/laporan/'.$report->id.'/submit')
            ->assertOk()->assertJsonPath('modal_url', route('laporan.show', $report));
        $this->assertSame('submitted', $report->fresh()->status);
        $this->putJson('/laporan/'.$report->id, $this->data('Terlambat'))->assertStatus(409);
        $this->postJson('/laporan/'.$report->id.'/submit')->assertStatus(409);
    }

    public function test_json_validation_and_nonowner_update_leave_report_unchanged(): void
    {
        $owner = $this->member('koorda');
        $this->actingAs($owner)->postJson('/laporan', array_replace($this->data(), ['judul' => '']))
            ->assertUnprocessable()->assertJsonValidationErrors('judul');
        $this->assertSame(0, KajianReport::query()->count());

        $report = $this->draft($owner);
        $this->putJson('/laporan/'.$report->id, array_replace($this->data(), ['peserta_l' => -1]))
            ->assertUnprocessable()->assertJsonValidationErrors('peserta_l');
        $this->actingAs($this->member('admin', $owner->wilayah_id))
            ->putJson('/laporan/'.$report->id, $this->data('Bukan pemilik'))->assertNotFound();
        $this->assertSame('Draf awal', $report->fresh()->judul);
    }

    public function test_json_review_decisions_keep_self_review_and_repeat_guards(): void
    {
        $author = $this->member('koorda');
        $reviewer = $this->member('koorda', $author->wilayah_id);
        $approved = $this->draft($author);
        $rejected = $this->draft($author);
        $approved->update(['status' => KajianReport::STATUS_SUBMITTED]);
        $rejected->update(['status' => KajianReport::STATUS_SUBMITTED]);

        $this->actingAs($author)->postJson('/laporan/'.$approved->id.'/approve')->assertForbidden();
        $this->actingAs($reviewer)->postJson('/laporan/'.$approved->id.'/approve')
            ->assertOk()->assertJsonPath('modal_url', route('laporan.show', $approved));
        $this->assertSame('approved', $approved->fresh()->status);
        $this->postJson('/laporan/'.$approved->id.'/approve')->assertStatus(409);

        $this->postJson('/laporan/'.$rejected->id.'/reject', [])
            ->assertUnprocessable()->assertJsonValidationErrors('reject_reason');
        $this->assertSame('submitted', $rejected->fresh()->status);
        $this->postJson('/laporan/'.$rejected->id.'/reject', ['reject_reason' => 'Perlu perbaikan'])
            ->assertOk()->assertJsonPath('modal_url', route('laporan.show', $rejected));
        $this->assertSame('rejected', $rejected->fresh()->status);
        $this->postJson('/laporan/'.$rejected->id.'/reject', ['reject_reason' => 'Ulang'])->assertStatus(409);
    }

    private function data(string $title = 'Draf baru'): array
    {
        return [
            'tanggal' => '2026-10-16', 'judul' => $title, 'tempat' => 'Ruang Kajian',
            'pemateri' => 'Pemateri', 'peserta_l' => 3, 'peserta_p' => 4,
            'durasi_menit' => 90, 'catatan' => 'Catatan kegiatan.',
        ];
    }

    private function member(string $role, ?int $regionId = null): User
    {
        $user = User::factory()->create([
            'status' => 'active',
            'wilayah_id' => $regionId ?? Wilayah::create(['name' => 'Wilayah'])->id,
        ]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function draft(User $owner): KajianReport
    {
        return KajianReport::create([
            'user_id' => $owner->id, 'wilayah_id' => $owner->wilayah_id,
            'tanggal' => '2026-10-16', 'judul' => 'Draf awal',
            'tempat' => 'Ruang Kajian', 'pemateri' => 'Pemateri',
            'peserta_l' => 3, 'peserta_p' => 4, 'total_peserta' => 7,
            'durasi_menit' => 90, 'catatan' => 'Catatan kegiatan.',
            'status' => KajianReport::STATUS_DRAFT,
        ]);
    }
}
