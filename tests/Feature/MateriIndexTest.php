<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Playlist;
use App\Models\Role;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MateriIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_roles_see_material_management_link(): void
    {
        $level = Level::create(['name' => 'Level 1']);
        $alumni = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $level->id]);
        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $level->id]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));

        $this->actingAs($alumni)->get(route('materi.index'))->assertOk()->assertDontSee('Kelola Materi');
        $this->actingAs($admin)->get(route('materi.index'))->assertOk()->assertSee('Kelola Materi');
    }

    public function test_admin_without_learning_level_reaches_management_from_material_navigation(): void
    {
        $admin = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => null]);
        $admin->roles()->attach(Role::firstOrCreate(['name' => 'admin']));

        $this->actingAs($admin)->get(route('materi.index'))->assertRedirect(route('materi.manage'));
        $this->get(route('materi.manage'))->assertOk()->assertSee('Kelola Materi');
    }

    public function test_search_matches_title_or_description_without_exposing_restricted_playlists(): void
    {
        $basic = Level::create(['name' => 'Level 1']);
        $advanced = Level::create(['name' => 'Level 2']);
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $basic->id]);
        foreach ([
            ['title' => 'Akhlak Dasar', 'description' => null, 'level_id' => $basic->id, 'is_active' => true],
            ['title' => 'Kajian Harian', 'description' => 'Pembahasan akhlak', 'level_id' => $basic->id, 'is_active' => true],
            ['title' => 'Topik Lain', 'description' => null, 'level_id' => $basic->id, 'is_active' => true],
            ['title' => 'Akhlak Rahasia', 'description' => 'akhlak', 'level_id' => $advanced->id, 'is_active' => true],
            ['title' => 'Akhlak Draft', 'description' => 'akhlak', 'level_id' => $basic->id, 'is_active' => false],
        ] as $data) {
            Playlist::create($data + ['youtube_playlist_id' => 'PLSEARCH']);
        }

        $this->actingAs($user)->get(route('materi.index', ['q' => ' akhlak ']))
            ->assertOk()
            ->assertViewHas('items', fn ($items) => $items->total() === 2)
            ->assertSee('Akhlak Dasar')->assertSee('Kajian Harian')
            ->assertDontSee('Akhlak Rahasia')->assertDontSee('Akhlak Draft')->assertDontSee('Topik Lain');

        $this->get(route('materi.index', ['q' => 'tidak-ditemukan']))
            ->assertOk()->assertSee('Tidak ada materi yang cocok.')->assertSee('Reset pencarian');
        $this->get(route('materi.index', ['q' => ['akhlak']]))->assertSessionHasErrors('q');
    }

    public function test_material_detail_has_watch_links_in_video_order_and_no_dead_edit_actions(): void
    {
        $level = Level::create(['name' => 'Level 1']);
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $level->id]);
        $playlist = Playlist::create([
            'title' => 'Kajian Dasar', 'youtube_playlist_id' => 'PLDasar', 'level_id' => $level->id, 'is_active' => true,
        ]);
        Video::create(['playlist_id' => $playlist->id, 'title' => 'Video Kedua', 'youtube_video_id' => 'abcdefghijk', 'order_index' => 2]);
        Video::create(['playlist_id' => $playlist->id, 'title' => 'Video Pertama', 'youtube_video_id' => 'lmnopqrstuv', 'order_index' => 1]);

        $this->actingAs($user)->get(route('materi.show', $playlist))->assertOk()
            ->assertSee('href="https://www.youtube.com/playlist?list=PLDasar"', false)
            ->assertSee('href="https://www.youtube.com/watch?v=lmnopqrstuv"', false)
            ->assertSeeInOrder(['Video Pertama', 'Video Kedua']);
        $this->get(route('materi.index'))->assertOk()
            ->assertSee('href="' . route('materi.show', $playlist) . '"', false)
            ->assertDontSee('>Edit</a>', false);
    }

    public function test_pagination_retains_search_and_invalid_search_is_rejected(): void
    {
        $level = Level::create(['name' => 'Level 1']);
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => $level->id]);
        foreach (range(1, 11) as $number) {
            Playlist::create([
                'title' => "Akhlak {$number}", 'youtube_playlist_id' => "PL{$number}",
                'level_id' => $level->id, 'is_active' => true,
            ]);
        }

        $this->actingAs($user)->get(route('materi.index', ['q' => 'Akhlak']))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->total() === 11 && str_contains($items->nextPageUrl(), 'q=Akhlak'));
        $this->get(route('materi.index', ['q' => 'Akhlak', 'page' => 2]))->assertOk()
            ->assertViewHas('items', fn ($items) => $items->count() === 1);
        $this->get(route('materi.index', ['q' => str_repeat('a', 151)]))->assertSessionHasErrors('q');
    }

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
