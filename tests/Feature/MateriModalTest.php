<?php

namespace Tests\Feature;

use App\Models\{Level, Playlist, Role, User, Video};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_modal_gets_return_private_fragments_and_html_fallbacks(): void
    {
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $playlist = $this->playlist($level);
        $video = Video::create(['playlist_id' => $playlist->id, 'title' => 'Pembuka', 'youtube_video_id' => 'abcdefghijk']);
        $this->actingAs($this->account('admin'));

        foreach ([
            route('materi.playlists.create'),
            route('materi.playlists.edit', $playlist),
            route('materi.playlists.confirm-delete', $playlist),
            route('materi.videos.create', $playlist),
            route('materi.videos.edit', [$playlist, $video]),
            route('materi.videos.confirm-delete', [$playlist, $video]),
            route('materi.show', $playlist),
        ] as $url) {
            $fragment = $this->withHeader('X-Materi-Modal', '1')->get($url)->assertOk()
                ->assertSee('data-materi-modal-content', false)
                ->assertHeader('X-Materi-Modal', '1')
                ->assertHeader('Vary', 'X-Materi-Modal');
            $this->assertStringContainsString('private', $fragment->headers->get('Cache-Control'));
            $this->assertStringContainsString('no-store', $fragment->headers->get('Cache-Control'));
            $this->flushHeaders();
            $this->get($url)->assertOk()->assertSee('data-materi-modal-content', false);
        }
    }

    public function test_json_playlist_and_video_mutations_keep_modal_contract_and_server_scope(): void
    {
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $this->actingAs($this->account('super_admin'));
        $created = $this->postJson(route('materi.playlists.store'), [
            'title' => 'Kajian Baru', 'youtube_playlist_id' => 'PLNEW123', 'level_id' => $level->id,
        ])->assertOk()->assertJsonStructure(['message', 'modal_url']);
        $playlist = Playlist::where('title', 'Kajian Baru')->firstOrFail();
        $created->assertJsonPath('modal_url', route('materi.show', $playlist));
        $this->patchJson(route('materi.playlists.update', $playlist), [
            'title' => 'Kajian Baru Diperbarui', 'youtube_playlist_id' => 'PLNEW123',
            'level_id' => $level->id, 'is_active' => '1',
        ])->assertOk()->assertJsonPath('modal_url', route('materi.show', $playlist));
        $this->assertDatabaseHas('playlists', ['id' => $playlist->id, 'is_active' => 1]);

        $this->postJson(route('materi.videos.store', $playlist), [
            'title' => 'Sesi Pertama', 'youtube_video_id' => 'abcdefghijk', 'playlist_id' => 999999,
        ])->assertOk()->assertJsonPath('modal_url', route('materi.show', $playlist));
        $video = Video::where('youtube_video_id', 'abcdefghijk')->firstOrFail();
        $this->assertSame($playlist->id, $video->playlist_id);
        $this->postJson(route('materi.videos.store', $playlist), [
            'title' => 'Duplikat', 'youtube_video_id' => 'abcdefghijk',
        ])->assertUnprocessable()->assertJsonValidationErrors('youtube_video_id');
        $this->patchJson(route('materi.videos.update', [$playlist, $video]), [
            'title' => 'Sesi Pertama Diperbarui', 'youtube_video_id' => 'abcdefghijk',
        ])->assertOk()->assertJsonPath('modal_url', route('materi.show', $playlist));
        $this->deleteJson(route('materi.videos.destroy', [$playlist, $video]))
            ->assertUnprocessable()->assertJsonValidationErrors('confirm_delete');
        $this->deleteJson(route('materi.videos.destroy', [$playlist, $video]), ['confirm_delete' => '1'])
            ->assertOk()->assertJsonPath('modal_url', route('materi.show', $playlist));
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
        $this->deleteJson(route('materi.playlists.destroy', $playlist), ['confirm_delete' => '1'])
            ->assertOk()->assertJsonPath('deleted', true);
        $this->assertDatabaseMissing('playlists', ['id' => $playlist->id]);
    }

    public function test_modal_routes_enforce_role_and_nested_video_binding(): void
    {
        $level = Level::create(['name' => 'Dasar', 'is_active' => true]);
        $playlist = $this->playlist($level);
        $other = $this->playlist($level, 'Playlist Lain');
        $video = Video::create(['playlist_id' => $playlist->id, 'title' => 'Pembuka', 'youtube_video_id' => 'abcdefghijk']);

        $this->actingAs($this->account('admin'));
        foreach (['edit', 'confirm-delete'] as $action) {
            $this->get(route('materi.videos.'.$action, [$other, $video]))->assertNotFound();
        }
        $this->patchJson(route('materi.videos.update', [$other, $video]), [
            'title' => 'Bajak', 'youtube_video_id' => 'abcdefghijk',
        ])->assertNotFound();
        $this->deleteJson(route('materi.videos.destroy', [$other, $video]), ['confirm_delete' => '1'])->assertNotFound();

        foreach (['koorda', 'alumni'] as $role) {
            $this->actingAs($this->account($role))->get(route('materi.playlists.create'))->assertForbidden();
            $this->get(route('materi.videos.create', $playlist))->assertForbidden();
            $this->postJson(route('materi.playlists.store'), [])->assertForbidden();
        }
        $inactive = $this->account('admin');
        $inactive->update(['status' => User::STATUS_PENDING]);
        $this->actingAs($inactive)->get(route('materi.playlists.edit', $playlist))->assertForbidden();
    }

    private function account(string $role): User
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE, 'level_id' => null]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));

        return $user;
    }

    private function playlist(Level $level, string $title = 'Kajian Dasar'): Playlist
    {
        return Playlist::create([
            'title' => $title, 'youtube_playlist_id' => 'PL'.str_replace(' ', '', $title),
            'level_id' => $level->id, 'is_active' => false,
        ]);
    }
}
