<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    /** query builder fleksibel: pakai Alumni jika ada, jika tidak pakai User role=alumni */
    protected function alumniQuery()
    {
        if (class_exists(\App\Models\Alumni::class)) {
            return \App\Models\Alumni::query();
        }
        return \App\Models\User::query()->where(function ($q) {
            $q->where('role', 'alumni')->orWhere('is_alumni', 1);
        });
    }

    /** GET /charts/alumni/monthly?year=2025 -> {year,labels[],values[]} */
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

    /** Opsional: kartu ringkas */
    public function alumniStatus()
    {
        $q = $this->alumniQuery();
        return response()->json([
            'total'   => (int) $q->count(),
            'active'  => (int) $this->alumniQuery()->where('status','active')->count(),
            'pending' => (int) $this->alumniQuery()->where('status','pending')->count(),
        ]);
    }
}
