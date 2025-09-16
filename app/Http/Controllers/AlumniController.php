<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        $q          = trim((string) $request->input('q'));
        $angkatan   = trim((string) $request->input('angkatan'));
        $wilayahId  = $request->integer('wilayah_id');
        $perPage    = min(max((int) $request->input('per_page', 15), 5), 100);

        // Whitelist kolom sorting agar aman dari SQL injection
        $sortMap = [
            'name'       => 'users.name',
            'email'      => 'users.email',
            'angkatan'   => 'users.angkatan',
            'wilayah'    => 'w.name',
            'level'      => 'l.name',
            'status'     => 'users.status',
            'created_at' => 'users.created_at',
        ];
        $sort = $sortMap[$request->input('sort', 'created_at')] ?? 'users.created_at';
        $dir  = $request->input('dir') === 'asc' ? 'asc' : 'desc';

        // Query dengan join agar bisa sort by wilayah/level name
        $query = DB::table('users')
            ->leftJoin('wilayah as w', 'w.id', '=', 'users.wilayah_id')
            ->leftJoin('levels as l', 'l.id', '=', 'users.level_id')
            ->select('users.*', 'w.name as wilayah_name', 'l.name as level_name');

        // Scope: Koorda hanya wilayahnya; Admin/Super semua
        if (!$me->isAdminLike()) {
            $query->where('users.wilayah_id', $me->wilayah_id);
        }

        // Filter
        if ($q !== '') {
            $query->where(function ($x) use ($q) {
                $x->where('users.name', 'like', "%{$q}%")
                  ->orWhere('users.email', 'like', "%{$q}%");
            });
        }
        if ($angkatan !== '') {
            $query->where('users.angkatan', $angkatan);
        }
        if ($wilayahId) {
            $query->where('users.wilayah_id', $wilayahId);
        }

        // Sorting + Pagination
        $alumni = $query->orderBy($sort, $dir)
                        ->paginate($perPage)
                        ->appends($request->query()); // keep querystring

        // Data untuk filter dropdown
        $wilayah = DB::table('wilayah')->select('id','name')->orderBy('name')->get();
        $angkatanList = DB::table('users')
            ->whereNotNull('angkatan')
            ->select('angkatan')
            ->distinct()
            ->orderBy('angkatan')
            ->pluck('angkatan');

        return view('alumni.index', compact('alumni','wilayah','angkatanList','sort','dir','q','angkatan','wilayahId','perPage'));
    }
}
