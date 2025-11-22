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
        return User::query();
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

        for ($m = 1; $m <= 12; $m++) {
            $values[] = (int) ($raw[$m] ?? 0);
        }

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
    $raw = \App\Models\User::selectRaw('ab, COUNT(*) as c')
        ->groupBy('ab')
        ->pluck('c', 'ab'); // ['iya' => 10, 'tidak' => 5]

    return response()->json([
        'labels' => $raw->keys()->values(),
        'values' => $raw->values(),
    ]);
}

}
