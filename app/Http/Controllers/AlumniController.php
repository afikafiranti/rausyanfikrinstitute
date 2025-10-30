<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wilayah;
use App\Models\Level;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        $q          = trim((string) $request->input('q'));
        $angkatan   = trim((string) $request->input('angkatan'));
        $wilayahId  = $request->integer('wilayah_id');
        $levelId    = $request->integer('level_id');
        $perPage    = min(max((int) $request->input('per_page', 15), 5), 100);

        // Kolom yang diizinkan untuk sorting
        $sortMap = [
            'name'       => 'users.name',
            'email'      => 'users.email',
            'angkatan'   => 'users.angkatan',
            'wilayah'    => 'wilayah.name',
            'level'      => 'level.description', // ubah ke description
            'status'     => 'users.status',
            'created_at' => 'users.created_at',
        ];
        $sort = $sortMap[$request->input('sort', 'created_at')] ?? 'users.created_at';
        $dir  = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        // Query pakai Eloquent agar relasi tetap bisa diakses di view
        $query = User::with([
            'wilayah:id,name',
            'level:id,description', // ambil description
        ])->whereIn('level_id', [3, 4]); // hanya level 3 dan 4

        // Filter level manual jika dipilih
        if ($levelId && in_array($levelId, [3,4])) {
            $query->where('level_id', $levelId);
        }

        // Batasi wilayah untuk non-admin
        if (!$me->isAdminLike()) {
            $query->where('wilayah_id', $me->wilayah_id);
        }

        // Filter pencarian
        if ($q !== '') {
            $query->where(fn($x) => $x->where('name', 'like', "%{$q}%")
                                      ->orWhere('email', 'like', "%{$q}%"));
        }
        if ($angkatan !== '') {
            $query->where('angkatan', $angkatan);
        }
        if ($wilayahId) {
            $query->where('wilayah_id', $wilayahId);
        }

        // Sorting dan pagination
        $query = $query->orderByRaw("$sort $dir");
        $alumni = $query->paginate($perPage)->appends($request->query());

        // Data untuk dropdown
        $wilayah = Wilayah::select('id', 'name')->orderBy('name')->get();
        $level   = Level::select('id', 'description')->whereIn('id', [3,4])->orderBy('description')->get();
        $angkatanList = User::whereNotNull('angkatan')->distinct()->orderBy('angkatan')->pluck('angkatan');

        return view('alumni.index', compact(
            'alumni', 'wilayah', 'level', 'angkatanList',
            'q', 'angkatan', 'wilayahId', 'levelId', 'perPage', 'sort', 'dir'
        ));
    }
}
