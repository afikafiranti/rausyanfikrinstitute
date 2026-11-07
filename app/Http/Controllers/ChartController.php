<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ChartController extends Controller
{
    /**
     * Ambil semua user (karena semua user = alumni)
     * Tidak perlu pakai role atau model Alumni.
     */
    protected function alumniQuery()
    {
        $viewer = request()->user();

        return User::query()->when(
            ! $viewer->hasAnyRole(['admin', 'super_admin']),
            fn ($query) => $query->where('wilayah_id', $viewer->wilayah_id),
        );
    }

    /** GET /charts/alumni/monthly?year=2025 */
    public function alumniMonthly(Request $request)
    {
        $year = (int) ($request->query('year') ?: now()->year);

        $raw = $this->alumniQuery()
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as m, COUNT(*) as c')
            ->groupBy('m')
            ->pluck('c', 'm'); // [m => count]

        $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $values = [];

        $monthCount = $year === now()->year ? now()->month : 12;
        for ($m = 1; $m <= $monthCount; $m++) {
            $values[] = (int) ($raw[$m] ?? 0);
        }

        $labels = array_slice($labels, 0, $monthCount);

        return response()->json([
            'year'   => $year,
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    /** Kartu ringkas */
    public function alumniStatus()
    {
        return response()->json([
            'total'   => (int) $this->alumniQuery()->count(),
            'active'  => (int) $this->alumniQuery()->where('status','active')->count(),
            'pending' => (int) $this->alumniQuery()->where('status','pending')->count(),
        ]);
    }

    /** Pie Chart: Status Pernikahan */
    public function alumniStatusPernikahan()
    {
        $raw = $this->alumniQuery()
            ->selectRaw('COALESCE(status_pernikahan, "Tidak Diisi") as status_pernikahan, COUNT(*) as c')
            ->groupBy('status_pernikahan')
            ->pluck('c', 'status_pernikahan');

        return response()->json([
            'labels' => $raw->keys()->values(),
            'values' => $raw->values(),
        ]);
    }

    public function alumniAb()
{
    $raw = $this->alumniQuery()->selectRaw('ab, COUNT(*) as c')
        ->groupBy('ab')
        ->pluck('c', 'ab'); // ['iya' => 10, 'tidak' => 5]

    return response()->json([
        'labels' => $raw->keys()->values(),
        'values' => $raw->values(),
    ]);
}

    public function alumniJobs()
    {
        $raw = $this->alumniQuery()
            ->selectRaw('MIN(pekerjaan) as pekerjaan, COUNT(*) as c')
            ->groupByRaw('BINARY pekerjaan')
            ->get();

        $jobs = [];
        $missing = 0;
        foreach ($raw as $row) {
            $label = trim(preg_replace('/\s+/u', ' ', $row->pekerjaan ?? ''));
            if ($label === '') {
                $missing += (int) $row->c;
                continue;
            }

            $key = mb_strtolower($label);
            if (! isset($jobs[$key])) {
                $jobs[$key] = ['label' => $label, 'count' => 0];
            } elseif (strcmp($label, $jobs[$key]['label']) < 0) {
                $jobs[$key]['label'] = $label;
            }
            $jobs[$key]['count'] += (int) $row->c;
        }

        uasort($jobs, fn ($a, $b) => $b['count'] <=> $a['count'] ?: strcmp($a['label'], $b['label']));
        $top = array_slice(array_values($jobs), 0, 5);
        $labels = array_column($top, 'label');
        $values = array_column($top, 'count');
        $other = array_sum(array_column(array_slice(array_values($jobs), 5), 'count'));
        foreach ([['Lainnya', $other, 'gabungan'], ['Belum diisi', $missing, 'profil kosong']] as [$name, $count, $context]) {
            if ($count === 0) continue;
            $label = $name;
            for ($suffix = 1; isset($jobs[mb_strtolower($label)]); $suffix++) {
                $label = $name.' ('.$context.($suffix > 1 ? ' '.$suffix : '').')';
            }
            $labels[] = $label;
            $values[] = $count;
        }

        return response()->json(['labels' => $labels, 'values' => $values])
            ->header('Cache-Control', 'private, no-store');
    }

}
