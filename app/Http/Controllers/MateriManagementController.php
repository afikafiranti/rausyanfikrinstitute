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
        return redirect()->route('materi.index');
    }

    public function createPlaylist(Request $request)
    {
        return $this->formView($request, 'materi.partials.playlist-form', [
            'playlist' => new Playlist,
            'levels' => Level::where('is_active', true)->orderBy('id')->get(),
            'formKey' => 'playlist-new',
        ]);
    }

    public function storePlaylist(Request $request)
    {
        $request->session()->flash('materi_form', 'playlist-new');
        $playlist = Playlist::create($this->playlistData($request));

        return $this->saved($request, $playlist, 'Playlist ditambahkan.');
    }

    public function editPlaylist(Request $request, Playlist $playlist)
    {
        return $this->formView($request, 'materi.partials.playlist-form', [
            'playlist' => $playlist,
            'levels' => Level::where('is_active', true)->orWhere('id', $playlist->level_id)->orderBy('id')->get(),
            'formKey' => 'playlist-'.$playlist->id,
        ]);
    }

    public function confirmDeletePlaylist(Request $request, Playlist $playlist)
    {
        return $this->formView($request, 'materi.partials.delete-form', compact('playlist') + ['video' => null]);
    }

    public function updatePlaylist(Request $request, Playlist $playlist)
    {
        $request->session()->flash('materi_form', 'playlist-'.$playlist->id);
        $playlist->update($this->playlistData($request));

        return $this->saved($request, $playlist, 'Playlist disimpan.');
    }

    public function destroyPlaylist(Request $request, Playlist $playlist)
    {
        $request->validate(['confirm_delete' => ['required', 'accepted']]);
        $playlist->delete();
        $message = 'Playlist dan videonya dihapus.';

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'deleted' => true])->header('Cache-Control', 'private, no-store')
            : redirect()->route('materi.index')->with('success', $message);
    }

    public function createVideo(Request $request, Playlist $playlist)
    {
        return $this->formView($request, 'materi.partials.video-form', compact('playlist') + [
            'video' => new Video, 'formKey' => 'video-new-'.$playlist->id,
        ]);
    }

    public function storeVideo(Request $request, Playlist $playlist)
    {
        $request->session()->flash('materi_form', 'video-new-'.$playlist->id);
        $playlist->videos()->create($this->videoData($request));

        return $this->saved($request, $playlist, 'Video ditambahkan.');
    }

    public function editVideo(Request $request, Playlist $playlist, int $video)
    {
        $video = $playlist->videos()->findOrFail($video);

        return $this->formView($request, 'materi.partials.video-form', compact('playlist', 'video') + ['formKey' => 'video-'.$video->id]);
    }

    public function confirmDeleteVideo(Request $request, Playlist $playlist, int $video)
    {
        $video = $playlist->videos()->findOrFail($video);

        return $this->formView($request, 'materi.partials.delete-form', compact('playlist', 'video'));
    }

    public function updateVideo(Request $request, Playlist $playlist, int $video)
    {
        $item = $playlist->videos()->findOrFail($video);
        $request->session()->flash('materi_form', 'video-'.$video);
        $item->update($this->videoData($request, $item));

        return $this->saved($request, $playlist, 'Video disimpan.');
    }

    public function destroyVideo(Request $request, Playlist $playlist, int $video)
    {
        $item = $playlist->videos()->findOrFail($video);
        $request->validate(['confirm_delete' => ['required', 'accepted']]);
        $item->delete();

        return $this->saved($request, $playlist, 'Video dihapus.');
    }

    private function formView(Request $request, string $partial, array $data)
    {
        $fragment = view($partial, $data)->render();
        $response = $request->header('X-Materi-Modal') === '1'
            ? response($fragment)
            : response()->view('materi.page', $data + ['fragment' => $fragment]);

        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('Vary', 'X-Materi-Modal');
        if ($request->header('X-Materi-Modal') === '1') {
            $response->headers->set('X-Materi-Modal', '1');
        }

        return $response;
    }

    private function saved(Request $request, Playlist $playlist, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message, 'modal_url' => route('materi.show', $playlist)])->header('Cache-Control', 'private, no-store')
            : redirect()->route('materi.show', $playlist)->with('success', $message);
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
