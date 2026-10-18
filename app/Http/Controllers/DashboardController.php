<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $viewer = $request->user();
        if ($viewer->hasRole('alumni') && ! $viewer->hasAnyRole(['admin', 'super_admin', 'koorda'])) {
            $viewer->loadMissing(['level', 'wilayah']);
            $missingProfile = [];
            foreach (['phone' => 'nomor telepon', 'angkatan' => 'angkatan', 'wilayah_id' => 'wilayah'] as $field => $label) {
                if (blank($viewer->{$field})) {
                    $missingProfile[] = $label;
                }
            }

            return view('dashboard.alumni', [
                'alumni' => $viewer,
                'canOpenMateri' => $viewer->status === User::STATUS_ACTIVE && (bool) $viewer->level_id,
                'missingProfile' => $missingProfile,
                'unreadCount' => $viewer->unreadNotifications()->count(),
            ]);
        }
        if (! $viewer->hasAnyRole(['admin', 'super_admin', 'koorda'])) {
            return redirect()->route('profile.edit');
        }
        abort_unless($viewer->can('view-alumni'), 403);

        $canViewAll = $viewer->hasAnyRole(['admin', 'super_admin']);
        abort_if(! $canViewAll && ! $viewer->wilayah_id, 403, 'Wilayah belum ditetapkan pada akun.');
        $users = User::query()->when(! $canViewAll, fn ($query) => $query->where('wilayah_id', $viewer->wilayah_id));

        // --- KPI utama ---
        $totalAlumni = (clone $users)->count();
        $alumniAktif = (clone $users)->where('status', User::STATUS_ACTIVE)->count();
        $alumniBaru = (clone $users)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count();
        $persenVerifikasi = $totalAlumni > 0 ? ($alumniAktif / $totalAlumni) * 100 : 0;

        // --- Grafik: Pertumbuhan Alumni per Bulan (tahun berjalan) ---
        $year = now()->year;
        $byMonth = (clone $users)->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // buat labels Jan..Des dan data 1..12
        $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $chartAlumni = [
            'labels' => $monthNames,
            'series' => array_map(fn ($m) => (int) ($byMonth[$m] ?? 0), range(1, 12)),
        ];

        // --- Tabel: Sebaran Angkatan ---
        $sebaranAngkatan = (clone $users)->select('angkatan', DB::raw('COUNT(*) as total'))
            ->whereNotNull('angkatan')
            ->groupBy('angkatan')
            ->orderByDesc('total')
            ->get();

        // --- Tabel: Sebaran Wilayah ---
        // left join supaya wilayah tanpa user juga bisa muncul (jika perlu)
        $sebaranWilayah = Wilayah::select('wilayah.id','wilayah.name', DB::raw('COUNT(users.id) as total'))
            ->leftJoin('users','wilayah.id','=','users.wilayah_id')
            ->when(! $canViewAll, fn ($query) => $query->where('wilayah.id', $viewer->wilayah_id))
            ->groupBy('wilayah.id','wilayah.name')
            ->orderByDesc('total')
            ->get();

        // --- Pending verifikasi count (scope: adminlike lihat semua, koorda terbatas wilayah) ---
        $pendingCount = (clone $users)->where('status', User::STATUS_PENDING)
            ->count();

        return view('dashboard.index', compact(
            'totalAlumni',
            'alumniAktif',
            'alumniBaru',
            'persenVerifikasi',
            'chartAlumni',
            'sebaranAngkatan',
            'sebaranWilayah',
            'pendingCount',
            'year'
        ));
    }
}
