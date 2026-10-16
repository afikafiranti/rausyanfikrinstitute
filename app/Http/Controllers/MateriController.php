<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;

class MateriController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->level_id && $user->can('manage-content')) {
            return redirect()->route('materi.manage');
        }
        abort_unless($user->status === User::STATUS_ACTIVE && $user->level_id, 403, 'Akun aktif dan level materi diperlukan.');

        $validated = request()->validate(['q' => ['nullable', 'string', 'max:150']]);
        $q = trim($validated['q'] ?? '');

        $items = Playlist::with('level')
            ->where('is_active', true)
            ->where('level_id', '<=', $user->level_id)
            ->when($q !== '', fn ($query) => $query->where(fn ($search) => $search
                ->where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")))
            ->orderBy('order_index')
            ->orderBy('id')
            ->paginate(10)->withQueryString();

        return view('materi.index', compact('items', 'q'));
    }

    public function show($id)
    {
        $user = auth()->user();
        abort_unless($user->status === User::STATUS_ACTIVE && $user->level_id, 403, 'Akun aktif dan level materi diperlukan.');

        $playlist = Playlist::with(['level', 'videos' => fn ($query) => $query->orderBy('order_index')])
            ->where('is_active', true)
            ->where('level_id', '<=', $user->level_id)
            ->findOrFail($id);

        return view('materi.show', compact('playlist'));
    }
}
