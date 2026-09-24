<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Playlist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_active_playlists_available_for_their_level(): void
    {
        $levelOne = Level::create(['name' => 'Level 1', 'description' => 'dasar', 'is_active' => true]);
        $levelTwo = Level::create(['name' => 'Level 2', 'description' => 'menengah', 'is_active' => true]);
        $levelThree = Level::create(['name' => 'Level 3', 'description' => 'lanjutan', 'is_active' => true]);

        $user = User::factory()->create(['level_id' => $levelTwo->id]);

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
