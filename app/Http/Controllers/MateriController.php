<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $items = Playlist::with('level')
            ->where('is_active', true)
            ->when($user->level_id, fn ($query) => $query->where('level_id', '<=', $user->level_id))
            ->orderBy('order_index')
            ->paginate(10);

        return view('materi.index', compact('items'));
    }

    public function show($id)
    {
        $user = auth()->user();

        $playlist = Playlist::with(['level', 'videos' => fn ($query) => $query->orderBy('order_index')])
            ->where('is_active', true)
            ->when($user->level_id, fn ($query) => $query->where('level_id', '<=', $user->level_id))
            ->findOrFail($id);

        return view('materi.show', compact('playlist'));
    }
}
