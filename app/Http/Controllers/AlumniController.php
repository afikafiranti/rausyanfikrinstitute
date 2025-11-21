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
            'level'      => 'level.description',
            'status'     => 'users.status',
            'created_at' => 'users.created_at',
        ];
        $sort = $sortMap[$request->input('sort', 'created_at')] ?? 'users.created_at';
        $dir  = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        // QUERY UTAMA — TANPA filter level_id
        $query = User::with([
            'wilayah:id,name',
            'level:id,description'
        ]);

        // Filter dropdown level jika dipilih
        if ($levelId) {
            $query->where('level_id', $levelId);
        }

        // Batasi wilayah untuk non-admin
        if (!$me->isAdminLike()) {
            $query->where('wilayah_id', $me->wilayah_id);
        }

        // Filter pencarian
        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($angkatan !== '') {
            $query->where('angkatan', $angkatan);
        }

        if ($wilayahId) {
            $query->where('wilayah_id', $wilayahId);
        }

        // Sorting & pagination
        $query = $query->orderByRaw("$sort $dir");
        $alumni = $query->paginate($perPage)->appends($request->query());

        // Dropdown data
        $wilayah = Wilayah::select('id', 'name')->orderBy('name')->get();

        // Semua level ditampilkan (1–4 atau sesuai database)
        $level   = Level::select('id', 'description')->orderBy('description')->get();

        $angkatanList = User::whereNotNull('angkatan')
                            ->distinct()
                            ->orderBy('angkatan')
                            ->pluck('angkatan');

        return view('alumni.index', compact(
            'alumni', 'wilayah', 'level', 'angkatanList',
            'q', 'angkatan', 'wilayahId', 'levelId', 'perPage', 'sort', 'dir'
        ));
    }

    public function show($id)
    {
        // DETAIL: juga tampilkan semua level (tanpa filter)
        $alumni = User::with(['wilayah:id,name', 'level:id,description'])
            ->findOrFail($id);

        return view('alumni.show', compact('alumni'));
    }
}
