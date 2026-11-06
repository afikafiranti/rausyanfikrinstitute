<?php

namespace Tests\Feature;

use App\Models\KajianReport;
use App\Models\Role;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KajianReportListingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_listing_shows_private_photo_thumbnail_and_no_photo_fallback(): void
    {
        $region = Wilayah::create(['name' => 'Wilayah Kajian']);
        $author = $this->member('alumni', $region->id);
        $photoPath = UploadedFile::fake()->image('dokumentasi.jpg')->store('report-photos', 'local');
        $withPhoto = $this->report($author, $region->id, 'Kajian dengan foto', 'submitted', $photoPath);
        $withoutPhoto = $this->report($author, $region->id, 'Kajian tanpa foto', 'approved');

        $response = $this->actingAs($this->member('admin'))->get(route('laporan.index'))->assertOk()
            ->assertSee('Kajian dengan foto')->assertSee('Kajian tanpa foto')->assertSee('Belum ada foto')
            ->assertDontSee($photoPath);

        $this->assertSame(1, $this->thumbnailCount($response->getContent(), $withPhoto));
        $this->assertSame(0, $this->thumbnailCount($response->getContent(), $withoutPhoto));
    }

    public function test_koorda_only_receives_thumbnails_for_reports_visible_in_their_region(): void
    {
        $local = Wilayah::create(['name' => 'Wilayah Lokal']);
        $other = Wilayah::create(['name' => 'Wilayah Lain']);
        $viewer = $this->member('koorda', $local->id);
        $peer = $this->member('alumni', $local->id);
        $photoPath = UploadedFile::fake()->image('dokumentasi.jpg')->store('report-photos', 'local');
        $ownDraft = $this->report($viewer, $local->id, 'Draf saya', 'draft', $photoPath);
        $submitted = $this->report($peer, $local->id, 'Laporan terkirim', 'submitted', $photoPath);
        $privateDraft = $this->report($peer, $local->id, 'Draf anggota lain', 'draft', $photoPath);
        $foreign = $this->report($peer, $other->id, 'Laporan wilayah lain', 'submitted', $photoPath);

        $response = $this->actingAs($viewer)->get(route('laporan.index', ['wilayah_id' => $other->id]))->assertOk()
            ->assertSee('Draf saya')->assertSee('Laporan terkirim')
            ->assertDontSee('Draf anggota lain')->assertDontSee('Laporan wilayah lain');

        foreach ([$ownDraft, $submitted] as $report) {
            $this->assertSame(1, $this->thumbnailCount($response->getContent(), $report));
        }
        foreach ([$privateDraft, $foreign] as $report) {
            $this->assertSame(0, $this->thumbnailCount($response->getContent(), $report));
            $response->assertDontSee(route('laporan.photo', $report), false);
        }
    }

    private function member(string $role, ?int $regionId = null): User
    {
        $user = User::factory()->create(['status' => 'active', 'wilayah_id' => $regionId]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function report(User $author, int $regionId, string $title, string $status, ?string $photoPath = null): KajianReport
    {
        return KajianReport::create([
            'user_id' => $author->id,
            'wilayah_id' => $regionId,
            'tanggal' => '2026-11-02',
            'judul' => $title,
            'status' => $status,
            'foto_url' => $photoPath,
        ]);
    }

    private function thumbnailCount(string $html, KajianReport $report): int
    {
        $document = new \DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

        return (new \DOMXPath($document))->query('//img[@src="'.route('laporan.photo', $report).'"]')->length;
    }
}
