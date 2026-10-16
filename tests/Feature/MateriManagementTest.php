<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Playlist;
use App\Models\Role;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriManagementTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role = 'admin', string $status = User::STATUS_ACTIVE): User
    {
        $user = User::factory()->create(['status' => $status, 'level_id' => null]);
        $user->roles()->attach(Role::firstOrCreate(['name' => $role]));
        return $user;
    }

    private function playlist(Level $level, array $extra = []): Playlist
    {
        return Playlist::create($extra + [
            'title' => 'Kajian Dasar', 'youtube_playlist_id' => 'PL123456',
            'level_id' => $level->id, 'is_active' => false,
        ]);
    }

    public function test_admin_can_manage_draft_playlists_without_a_material_level(): void
    {
        $level = Level::create(['name' => 'Level 1', 'is_active' => true]);
        $high = Level::create(['name' => 'Level 2', 'is_active' => true]);
        $this->actingAs($this->account());

        $this->post(route('materi.playlists.store'), [
            'title' => 'Materi Tinggi', 'youtube_playlist_id' => 'PLabc_123',
            'level_id' => $high->id, 'description' => 'Uraian', 'order_index' => 2,
        ])->assertRedirect();
        $playlist = Playlist::where('title', 'Materi Tinggi')->firstOrFail();
        $this->assertFalse($playlist->is_active);
        $this->get(route('materi.manage'))->assertOk()->assertSee('Materi Tinggi')->assertSee('Draft');
        $this->get(route('materi.playlists.edit', $playlist))->assertOk()->assertSee('Materi Tinggi');

        $this->patch(route('materi.playlists.update', $playlist), [
            'title' => 'Materi Terbit', 'youtube_playlist_id' => 'PLabc_123',
            'level_id' => $level->id, 'description' => '', 'order_index' => 0,
            'is_active' => '1',
        ])->assertRedirect();
        $this->assertDatabaseHas('playlists', ['id' => $playlist->id, 'title' => 'Materi Terbit', 'level_id' => $level->id, 'is_active' => 1]);
    }

    public function test_video_crud_is_scoped_to_its_playlist(): void
    {
        $level = Level::create(['name' => 'Level 1', 'is_active' => true]);
        $first = $this->playlist($level);
        $second = $this->playlist($level, ['title' => 'Kedua', 'youtube_playlist_id' => 'PLSECOND']);
        $this->actingAs($this->account('super_admin'));

        $this->post(route('materi.videos.store', $first), [
            'title' => 'Pembuka', 'youtube_video_id' => 'abcdefghijk',
            'duration_sec' => 120, 'order_index' => 1, 'playlist_id' => $second->id,
        ])->assertRedirect();
        $video = Video::where('youtube_video_id', 'abcdefghijk')->firstOrFail();
        $this->assertSame($first->id, $video->playlist_id);
        $progress = VideoProgress::create(['user_id' => auth()->id(), 'video_id' => $video->id]);

        $this->patch(route('materi.videos.update', [$second, $video]), [
            'title' => 'Bajak', 'youtube_video_id' => 'abcdefghijk',
        ])->assertNotFound();
        $this->delete(route('materi.videos.destroy', [$second, $video]))->assertNotFound();

        $this->patch(route('materi.videos.update', [$first, $video]), [
            'title' => 'Pembuka Baru', 'youtube_video_id' => 'abcdefghijk',
            'duration_sec' => 180, 'order_index' => 2,
        ])->assertRedirect();
        $this->assertDatabaseHas('videos', ['id' => $video->id, 'title' => 'Pembuka Baru', 'playlist_id' => $first->id]);
        $this->delete(route('materi.videos.destroy', [$first, $video]))->assertSessionHasErrors('confirm_delete');
        $this->assertDatabaseHas('videos', ['id' => $video->id]);
        $this->delete(route('materi.videos.destroy', [$first, $video]), ['confirm_delete' => '1'])->assertRedirect();
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
        $this->assertDatabaseMissing('video_progress', ['id' => $progress->id]);
    }

    public function test_other_roles_and_inactive_admin_cannot_use_management(): void
    {
        $level = Level::create(['name' => 'Level 1', 'is_active' => true]);
        $playlist = $this->playlist($level);
        $this->get(route('materi.manage'))->assertRedirect(route('login'));
        foreach (['koorda', 'alumni'] as $role) {
            $this->actingAs($this->account($role))->get(route('materi.manage'))->assertForbidden();
            $this->post(route('materi.playlists.store'), [])->assertForbidden();
            $this->delete(route('materi.playlists.destroy', $playlist), ['confirm_delete' => '1'])->assertForbidden();
        }
        $this->actingAs($this->account('admin', User::STATUS_PENDING))->get(route('materi.manage'))->assertForbidden();
    }

    public function test_invalid_playlist_and_video_values_are_rejected(): void
    {
        $active = Level::create(['name' => 'Level 1', 'is_active' => true]);
        $inactive = Level::create(['name' => 'Level 2', 'is_active' => false]);
        $playlist = $this->playlist($active);
        Video::create(['playlist_id' => $playlist->id, 'title' => 'Ada', 'youtube_video_id' => 'abcdefghijk']);
        $this->actingAs($this->account());

        $this->post(route('materi.playlists.store'), [
            'title' => str_repeat('x', 151), 'youtube_playlist_id' => 'https://youtube.com/',
            'level_id' => $inactive->id, 'order_index' => -1, 'is_active' => 'yes',
        ])->assertSessionHasErrors(['title', 'youtube_playlist_id', 'level_id', 'order_index', 'is_active']);
        $this->post(route('materi.videos.store', $playlist), [
            'title' => 'Duplikat', 'youtube_video_id' => 'abcdefghijk',
            'duration_sec' => -1, 'order_index' => -1,
        ])->assertSessionHasErrors(['youtube_video_id', 'duration_sec', 'order_index']);
        $this->post(route('materi.videos.store', $playlist), [
            'title' => 'Salah ID', 'youtube_video_id' => 'not-an-id',
        ])->assertSessionHasErrors('youtube_video_id');
        $this->assertDatabaseCount('videos', 1);
    }

    public function test_playlist_deletion_needs_confirmation_and_cascades_videos(): void
    {
        $level = Level::create(['name' => 'Level 1', 'is_active' => true]);
        $playlist = $this->playlist($level);
        $video = Video::create(['playlist_id' => $playlist->id, 'title' => 'Ada', 'youtube_video_id' => 'abcdefghijk']);
        $this->actingAs($this->account());
        $progress = VideoProgress::create(['user_id' => auth()->id(), 'video_id' => $video->id]);

        $this->delete(route('materi.playlists.destroy', $playlist))->assertSessionHasErrors('confirm_delete');
        $this->assertDatabaseHas('playlists', ['id' => $playlist->id]);
        $this->delete(route('materi.playlists.destroy', $playlist), ['confirm_delete' => '1'])->assertRedirect(route('materi.manage'));
        $this->assertDatabaseMissing('playlists', ['id' => $playlist->id]);
        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
        $this->assertDatabaseMissing('video_progress', ['id' => $progress->id]);
    }

    public function test_failed_video_form_restores_only_its_own_values(): void
    {
        $level = Level::create(['name' => 'Level 1', 'is_active' => true]);
        $playlist = $this->playlist($level);
        $video = Video::create(['playlist_id' => $playlist->id, 'title' => 'Asli', 'youtube_video_id' => 'abcdefghijk']);
        $this->actingAs($this->account());

        $this->post(route('materi.videos.store', $playlist), [
            'title' => 'Belum Valid', 'youtube_video_id' => 'salah', 'order_index' => 8,
        ])->assertSessionHasErrors('youtube_video_id');
        $html = $this->get(route('materi.playlists.edit', $playlist))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/id="title"[^>]*value="Kajian Dasar"/', $html);
        $this->assertMatchesRegularExpression('/id="new_title"[^>]*value="Belum Valid"/', $html);
        $this->assertMatchesRegularExpression('/id="video_title_'.$video->id.'"[^>]*value="Asli"/', $html);

        $this->patch(route('materi.videos.update', [$playlist, $video]), [
            'title' => 'Edit Gagal', 'youtube_video_id' => 'salah',
        ])->assertSessionHasErrors('youtube_video_id');
        $html = $this->get(route('materi.playlists.edit', $playlist))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/id="title"[^>]*value="Kajian Dasar"/', $html);
        $this->assertMatchesRegularExpression('/id="new_title"[^>]*value=""/', $html);
        $this->assertMatchesRegularExpression('/id="video_title_'.$video->id.'"[^>]*value="Edit Gagal"/', $html);
    }

    public function test_inactive_current_level_is_visible_but_cannot_be_selected_for_update(): void
    {
        $inactive = Level::create(['name' => 'Level Lama', 'is_active' => false]);
        $active = Level::create(['name' => 'Level Aktif', 'is_active' => true]);
        $playlist = $this->playlist($inactive);
        $this->actingAs($this->account());

        $this->get(route('materi.playlists.edit', $playlist))->assertOk()
            ->assertSee('Level Lama (nonaktif)')
            ->assertSee('Pilih level aktif untuk menyimpan playlist.');
        $this->patch(route('materi.playlists.update', $playlist), [
            'title' => 'Kajian Dasar', 'youtube_playlist_id' => 'PL123456',
            'level_id' => $inactive->id,
        ])->assertSessionHasErrors('level_id');
        $this->patch(route('materi.playlists.update', $playlist), [
            'title' => 'Kajian Dasar', 'youtube_playlist_id' => 'PL123456',
            'level_id' => $active->id,
        ])->assertRedirect();
        $this->assertDatabaseHas('playlists', ['id' => $playlist->id, 'level_id' => $active->id]);
    }
}
