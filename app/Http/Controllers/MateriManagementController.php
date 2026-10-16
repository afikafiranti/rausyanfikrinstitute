<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MateriManagementController extends Controller
{
    public function index()
    {
        return view('materi.manage', [
            'playlists' => Playlist::with('level')->withCount('videos')->orderBy('order_index')->orderBy('id')->paginate(10),
            'levels' => Level::where('is_active', true)->orderBy('id')->get(),
        ]);
    }

    public function storePlaylist(Request $request)
    {
        Playlist::create($this->playlistData($request));
        return redirect()->route('materi.manage')->with('success', 'Playlist ditambahkan.');
    }

    public function editPlaylist(Playlist $playlist)
    {
        return view('materi.edit', [
            'playlist' => $playlist->load(['videos' => fn ($query) => $query->orderBy('order_index')->orderBy('id')]),
            'levels' => Level::where('is_active', true)->orWhere('id', $playlist->level_id)->orderBy('id')->get(),
        ]);
    }

    public function updatePlaylist(Request $request, Playlist $playlist)
    {
        $request->session()->flash('materi_form', 'playlist');
        $playlist->update($this->playlistData($request));
        return redirect()->route('materi.playlists.edit', $playlist)->with('success', 'Playlist disimpan.');
    }

    public function destroyPlaylist(Request $request, Playlist $playlist)
    {
        $request->validate(['confirm_delete' => ['required', 'accepted']]);
        $playlist->delete();
        return redirect()->route('materi.manage')->with('success', 'Playlist dan videonya dihapus.');
    }

    public function storeVideo(Request $request, Playlist $playlist)
    {
        $request->session()->flash('materi_form', 'video-new');
        $playlist->videos()->create($this->videoData($request));
        return redirect()->route('materi.playlists.edit', $playlist)->with('success', 'Video ditambahkan.');
    }

    public function updateVideo(Request $request, Playlist $playlist, int $video)
    {
        $item = $playlist->videos()->findOrFail($video);
        $request->session()->flash('materi_form', 'video-'.$video);
        $item->update($this->videoData($request, $item));
        return redirect()->route('materi.playlists.edit', $playlist)->with('success', 'Video disimpan.');
    }

    public function destroyVideo(Request $request, Playlist $playlist, int $video)
    {
        $item = $playlist->videos()->findOrFail($video);
        $request->validate(['confirm_delete' => ['required', 'accepted']]);
        $item->delete();
        return redirect()->route('materi.playlists.edit', $playlist)->with('success', 'Video dihapus.');
    }

    private function playlistData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'youtube_playlist_id' => ['required', 'string', 'max:64', 'regex:/\A[A-Za-z0-9_-]+\z/'],
            'level_id' => ['required', 'integer', Rule::exists('levels', 'id')->where('is_active', true)],
            'description' => ['nullable', 'string', 'max:10000'],
            'order_index' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['order_index'] = $data['order_index'] ?? 0;
        return $data;
    }

    private function videoData(Request $request, ?Video $video = null): array
    {
        $unique = Rule::unique('videos', 'youtube_video_id');
        if ($video) {
            $unique->ignore($video);
        }
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'youtube_video_id' => ['required', 'string', 'size:11', 'regex:/\A[A-Za-z0-9_-]{11}\z/', $unique],
            'duration_sec' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'order_index' => ['nullable', 'integer', 'min:0', 'max:2147483647'],
        ]);
        $data['order_index'] = $data['order_index'] ?? 0;
        return $data;
    }
}
