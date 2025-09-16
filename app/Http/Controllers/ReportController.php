<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Report; // aktifkan bila model Report sudah ada

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $wilayahId = (int) $request->get('wilayah_id'); // dari middleware

        // Contoh tanpa model: tampilkan placeholder
        // Jika punya model Report, filter by wilayah_id:
        // $reports = Report::when($wilayahId, fn($q) => $q->where('wilayah_id', $wilayahId))
        //                  ->latest()->paginate(10);

        return view('laporan.review', [
            'wilayahId' => $wilayahId,
            // 'reports' => $reports ?? collect(),
        ]);
    }
}
