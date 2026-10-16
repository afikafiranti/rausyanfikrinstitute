<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\User;

class MateriController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->status === User::STATUS_ACTIVE && $user->level_id, 403, 'Akun aktif dan level materi diperlukan.');

        $items = Playlist::with('level')
            ->where('is_active', true)
            ->where('level_id', '<=', $user->level_id)
            ->orderBy('order_index')
            ->paginate(10);

        return view('materi.index', compact('items'));
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
