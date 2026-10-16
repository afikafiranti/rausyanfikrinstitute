<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Playlist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MateriIndexTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('ineligibleAccounts')]
    public function test_ineligible_accounts_cannot_list_or_open_material(string $status, bool $hasLevel): void
    {
        $level = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);
        $playlist = Playlist::create([
            'title' => 'Materi Terbatas', 'youtube_playlist_id' => 'PLPRIVATE',
            'level_id' => $level->id, 'is_active' => true,
        ]);
        $user = User::factory()->create(['status' => $status, 'level_id' => $hasLevel ? $level->id : null]);

        $this->actingAs($user)->get(route('materi.index'))->assertForbidden();
        $this->get(route('materi.show', $playlist))->assertForbidden();
    }

    public static function ineligibleAccounts(): array
    {
        return [
            'active without level' => [User::STATUS_ACTIVE, false],
            'pending with level' => [User::STATUS_PENDING, true],
            'suspended with level' => [User::STATUS_SUSPENDED, true],
        ];
    }

    public function test_direct_playlist_links_respect_level_publication_and_login(): void
    {
        $basic = Level::create(['name' => 'Level 1', 'description' => 'dasar']);
        $advanced = Level::create(['name' => 'Level 2', 'description' => 'lanjutan']);
        $available = Playlist::create([
            'title' => 'Materi Dasar', 'youtube_playlist_id' => 'PLBASIC', 'level_id' => $basic->id, 'is_active' => true,
        ]);
        $restricted = Playlist::create([
            'title' => 'Materi Lanjutan', 'youtube_playlist_id' => 'PLADVANCED', 'level_id' => $advanced->id, 'is_active' => true,
        ]);
        $draft = Playlist::create([
            'title' => 'Materi Draft', 'youtube_playlist_id' => 'PLDRAFT', 'level_id' => $basic->id, 'is_active' => false,
        ]);

        $this->get(route('materi.index'))->assertRedirect(route('login'));
        $this->get(route('materi.show', $available))->assertRedirect(route('login'));

        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $basic->id]);
        $this->actingAs($user)->get(route('materi.show', $available))->assertOk();
        $this->get(route('materi.show', $restricted))->assertNotFound();
        $this->get(route('materi.show', $draft))->assertNotFound();
    }

    public function test_user_can_view_active_playlists_available_for_their_level(): void
    {
        $levelOne = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);
        $levelTwo = Level::create(['name' => 'Level 2', 'description' => 'menengah', 'is_active' => true]);
        $levelThree = Level::create(['name' => 'Level 3', 'description' => 'lanjutan', 'is_active' => true]);

        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $levelTwo->id]);

        Playlist::create([
            'title' => 'Materi dasar',
            'youtube_playlist_id' => 'PLDASAR',
            'level_id' => $levelOne->id,
            'is_active' => true,
        ]);
        Playlist::create([
            'title' => 'Materi Nonaktif',
            'youtube_playlist_id' => 'PLNONAKTIF',
            'level_id' => $levelOne->id,
            'is_active' => false,
        ]);
        Playlist::create([
            'title' => 'Materi lanjutan',
            'youtube_playlist_id' => 'PLLANJUTAN',
            'level_id' => $levelThree->id,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('materi.index'));

        $response
            ->assertOk()
            ->assertSee('Materi dasar')
            ->assertSee('dasar')
            ->assertDontSee('Materi Nonaktif')
            ->assertDontSee('Materi lanjutan');
    }
}
