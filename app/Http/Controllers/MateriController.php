<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->status === User::STATUS_ACTIVE, 403);
        $canManage = $user->can('manage-content');
        abort_unless($canManage || $user->level_id, 403, 'Akun aktif dan level materi diperlukan.');

        $validated = $request->validate(['q' => ['nullable', 'string', 'max:150']]);
        $q = trim($validated['q'] ?? '');

        $query = Playlist::query()->select('playlists.*')->addSelect([
            'thumbnail_video_id' => Video::select('youtube_video_id')->whereColumn('playlist_id', 'playlists.id')
                ->orderBy('order_index')->orderBy('id')->limit(1),
        ])->with('level')->withCount('videos')
            ->when(! $canManage, fn ($query) => $query->where('is_active', true)->where('level_id', '<=', $user->level_id))
            ->when($q !== '', fn ($query) => $query->where(fn ($search) => $search
                ->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")))
            ->orderBy('order_index')
            ->orderBy('id');
        $items = $query->paginate(10)->appends($q !== '' ? ['q' => $q] : []);
        if ($items->currentPage() > $items->lastPage()) {
            return redirect()->route('materi.index', array_filter(['q' => $q, 'page' => $items->lastPage() > 1 ? $items->lastPage() : null]));
        }

        return response()->view('materi.index', compact('items', 'q', 'canManage'))
            ->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        abort_unless($user->status === User::STATUS_ACTIVE, 403);
        $canManage = $user->can('manage-content');
        abort_unless($canManage || $user->level_id, 403, 'Akun aktif dan level materi diperlukan.');

        $playlist = Playlist::query()->with('level')
            ->when(! $canManage, fn ($query) => $query->where('is_active', true)->where('level_id', '<=', $user->level_id))
            ->findOrFail($id);
        $videos = $playlist->videos()->orderBy('order_index')->orderBy('id')->paginate(10, ['*'], 'video_page');
        if ($videos->currentPage() > $videos->lastPage()) {
            return redirect()->route('materi.show', array_filter([
                'id' => $playlist->id,
                'video_page' => $videos->lastPage() > 1 ? $videos->lastPage() : null,
            ]));
        }

        $data = compact('playlist', 'videos', 'canManage');
        $fragment = view('materi.partials.detail', $data)->render();
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
}
