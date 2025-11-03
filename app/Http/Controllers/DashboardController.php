<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Level;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // --- KPI utama ---
        $totalAlumni = User::count();
        $alumniAktif = User::where('status', User::STATUS_ACTIVE)->count();
        $alumniBaru  = User::whereMonth('created_at', now()->month)->count();
        $persenVerifikasi = $totalAlumni > 0 ? ($alumniAktif / $totalAlumni) * 100 : 0;

        // --- Grafik: Pertumbuhan Alumni per Bulan (tahun berjalan) ---
        $year = now()->year;
        $byMonth = User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // buat labels Jan..Des dan data 1..12
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $chartAlumni = [
            'labels' => $monthNames,
            'series' => array_map(fn($m) => $byMonth[$m] ?? 0, range(1,12)),
        ];

        // Dummy data sementara untuk grafik Jumlah Kajian per Wilayah
        $chartKajianWilayah = [
            'labels' => ['Makassar', 'Palopo', 'Luwu', 'Toraja'],
            'data2024' => [8, 12, 5, 4],
            'data2025' => [10, 9, 7, 6],
        ];

        // --- Tabel: Sebaran Angkatan ---
        $sebaranAngkatan = User::select('angkatan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('angkatan')
            ->groupBy('angkatan')
            ->orderByDesc('total')
            ->get();

        // --- Tabel: Sebaran Wilayah ---
        // left join supaya wilayah tanpa user juga bisa muncul (jika perlu)
        $sebaranWilayah = Wilayah::select('wilayah.id','wilayah.name', DB::raw('COUNT(users.id) as total'))
            ->leftJoin('users','wilayah.id','=','users.wilayah_id')
            ->groupBy('wilayah.id','wilayah.name')
            ->orderByDesc('total')
            ->get();

        // --- Pending verifikasi count (scope: adminlike lihat semua, koorda terbatas wilayah) ---
        $pendingCount = User::where('status', User::STATUS_PENDING)
            ->when(!auth()->user()->isAdminLike(), fn($q) => $q->where('wilayah_id', auth()->user()->wilayah_id ?? 0))
            ->count();

        return view('dashboard.index', compact(
            'totalAlumni',
            'alumniAktif',
            'alumniBaru',
            'persenVerifikasi',
            'chartAlumni',
            'chartKajianWilayah',
            'sebaranAngkatan',
            'sebaranWilayah',
            'pendingCount',
            'year'
        ));
    }
}
