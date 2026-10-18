@extends('layouts.app')

@section('page-header')
@endsection

@section('page-content')
    <div>
        <div class="grid grid-cols-1 gap-4 pb-4 sm:grid-cols-2 lg:grid-cols-12">
            {{-- STATISTIK UTAMA --}}
            <div class="rf-card lg:col-span-3">
                <div class="text-xs text-slate-500">Total Alumni</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($totalAlumni) }}</div>
            </div>

            <div class="rf-card lg:col-span-3">
                <div class="text-xs text-slate-500">Alumni Baru Bulan Ini</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($alumniBaru) }}</div>
            </div>

            <div class="rf-card lg:col-span-3">
                <div class="text-xs text-slate-500">Alumni Aktif</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($alumniAktif) }}</div>
            </div>

            <div class="rf-card lg:col-span-3">
                <div class="text-xs text-slate-500">Persentase Verifikasi</div>
                <div class="mt-1 text-2xl font-semibold">{{ round($persenVerifikasi, 1) }}%</div>
            </div>

            {{-- NOTIFIKASI VERIFIKASI --}}
            @can('review-user')
                <div class="rf-card sm:col-span-2 lg:col-span-12">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="font-semibold text-[#14213D]">Verifikasi pending</div>
                            <div class="text-sm text-[#536173]">Akun menunggu persetujuan Koordinator Daerah</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="rf-badge bg-yellow-100 text-yellow-800">{{ $pendingCount }} akun</div>
                            <a href="{{ route('verification.index') }}" class="rf-btn">
                                <i class="fas fa-user-check"></i> Buka
                            </a>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- GRAFIK TREN (ApexCharts) --}}
            <div class="min-w-0 rounded-xl bg-[#14213D] p-4 shadow sm:col-span-2 lg:col-span-8">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-white">Pertumbuhan Anggota per Bulan ({{ $year }})</h3>
                </div>
                <div class="min-w-0 flex-auto pt-4">
                    <!-- Chart -->
                    <div class="relative min-w-0 text-slate-300">
                        <div id="chart-alumni" class="h-80 w-full min-w-0 text-white"></div>
                    </div>
                </div>
            </div>

            <div class="rf-card sm:col-span-2 lg:col-span-4">
                <h3 class="font-semibold mb-3">Jumlah Kajian per Bulan</h3>
                <p class="text-sm text-slate-600" role="status">Statistik kajian belum tersedia.</p>
            </div>

            {{-- ========================= 2 CHART DALAM 1 BARIS ========================= --}}
            <div class="rf-card min-w-0 sm:col-span-2 lg:col-span-6">
                <h3 class="font-semibold mb-3">Status Pernikahan Alumni</h3>
                <div class="relative min-w-0">
                    <div id="chartStatusPernikahan" class="h-80 w-full min-w-0"></div>
                </div>
            </div>

            <div class="rf-card min-w-0 sm:col-span-2 lg:col-span-6">
                <h3 class="font-semibold mb-3">Status AB Alumni</h3>
                <div class="relative min-w-0">
                    <div id="chartAb" class="h-72 w-full min-w-0"></div>
                </div>
            </div>




            {{-- BLOK TABEL BAWAH  --}}

            <div class="rf-card min-w-0 sm:col-span-2 lg:col-span-8">
                {{-- TABEL SEBARAN ANGKATAN --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pb-4">
                    <h3 class="font-semibold text-base text-blueGray-700">Sebaran Alumni per Angkatan
                    </h3>
                    <div>
                        <a href="{{ route('alumni.index') }}"
                            class="rf-btn">
                            Lihat semua
                        </a>
                    </div>

                </div>
                <div class="block w-full overflow-x-auto">
                    <table class="items-center w-full bg-transparent border-collapse">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 bg-blueGray-50 text-blueGray-500 border border-solid border-blueGray-100 py-3 text-xs uppercase whitespace-nowrap font-semibold text-left">
                                    Angkatan
                                </th>
                                <th
                                    class="px-6 bg-blueGray-50 text-blueGray-500 border border-solid border-blueGray-100 py-3 text-xs uppercase whitespace-nowrap font-semibold text-left">
                                    Jumlah
                                </th>
                                <th
                                    class="px-6 bg-blueGray-50 text-blueGray-500 border border-solid border-blueGray-100 py-3 text-xs uppercase whitespace-nowrap font-semibold text-left">
                                    Persentase
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sebaranAngkatan as $item)
                                @php $persen = $totalAlumni > 0 ? ($item->total / $totalAlumni) * 100 : 0; @endphp
                                <tr>
                                    <th class="border-t-0 px-6 align-middle text-xs whitespace-nowrap p-4 text-left">
                                        {{ $item->angkatan ?? '-' }}
                                    </th>
                                    <td class="border-t-0 px-6 align-middle text-xs whitespace-nowrap p-4">
                                        {{ number_format($item->total) }}
                                    </td>
                                    <td class="border-t-0 px-6 align-middle text-xs whitespace-nowrap p-4">
                                        <div class="flex items-center">
                                            <span class="mr-2">{{ number_format($persen, 1) }}%</span>
                                            <div class="relative w-full">
                                                <div class="overflow-hidden h-2 text-xs flex rounded bg-indigo-100">
                                                    <div style="width: {{ $persen }}%"
                                                        class="transition-all duration-500 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-500">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($sebaranAngkatan->isEmpty())
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-sm text-slate-500">Belum ada
                                        data
                                        angkatan</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TABEL SEBARAN WILAYAH --}}
            <div class="rf-card min-w-0 sm:col-span-2 lg:col-span-4">
                <div class="flex items-center justify-between pb-4">
                    <h3 class="font-semibold text-base text-blueGray-700">Sebaran Alumni per Wilayah
                    </h3>

                </div>
                <div class="block w-full overflow-x-auto">
                    <table class="items-center w-full bg-transparent border-collapse">
                        <thead class="thead-light">
                            <tr>
                                <th
                                    class="px-6 bg-blueGray-50 text-blueGray-500 border border-solid border-blueGray-100 py-3 text-xs uppercase whitespace-nowrap font-semibold text-left">
                                    Wilayah
                                </th>
                                <th
                                    class="px-6 bg-blueGray-50 text-blueGray-500 border border-solid border-blueGray-100 py-3 text-xs uppercase whitespace-nowrap font-semibold text-left">
                                    Jumlah
                                </th>
                                <th
                                    class="px-6 bg-blueGray-50 text-blueGray-500 border border-solid border-blueGray-100 py-3 text-xs uppercase whitespace-nowrap font-semibold text-left min-w-140-px">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sebaranWilayah as $item)
                                @php $persen = $totalAlumni > 0 ? ($item->total / $totalAlumni) * 100 : 0; @endphp
                                <tr>
                                    <th class="border-t-0 px-6 align-middle text-xs whitespace-nowrap p-4 text-left">
                                        {{ $item->name ?? '-' }}
                                    </th>
                                    <td class="border-t-0 px-6 align-middle text-xs whitespace-nowrap p-4">
                                        {{ number_format($item->total) }}
                                    </td>
                                    <td class="border-t-0 px-6 align-middle text-xs whitespace-nowrap p-4">
                                        <div class="flex items-center">
                                            <span class="mr-2">{{ number_format($persen, 1) }}%</span>
                                            <div class="relative w-full">
                                                <div class="overflow-hidden h-2 text-xs flex rounded bg-emerald-100">
                                                    <div style="width: {{ $persen }}%"
                                                        class="transition-all duration-500 shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-emerald-500">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($sebaranWilayah->isEmpty())
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-sm text-slate-500">Belum ada
                                        data
                                        wilayah</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>


    {{-- SCRIPTS APEXCHARTS --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        /* ============================== Grafik: Pertumbuhan Alumni (Line) ============================== */
        (function() {
            const labels = @json($chartAlumni['labels']);
            const seriesData = @json($chartAlumni['series']);

            const options = {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    foreColor: '#CBD5E1',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                series: [{
                    name: 'Alumni Baru',
                    data: seriesData
                }],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#6366F1'],
                grid: {
                    borderColor: '#334155',
                    strokeDashArray: 3
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        style: {
                            colors: '#CBD5E1'
                        }
                    },
                    axisBorder: {
                        color: '#475569'
                    },
                    axisTicks: {
                        color: '#475569'
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#CBD5E1'
                        },
                        formatter: val => parseInt(val)
                    },
                    title: {
                        text: 'Jumlah Alumni',
                        style: {
                            color: '#CBD5E1'
                        }
                    }
                },
                tooltip: {
                    theme: 'dark'
                }
            };

            new ApexCharts(document.querySelector("#chart-alumni"), options).render();
        })();
    </script>



    {{-- ========================= PIE CHART STATUS PERNIKAHAN ========================= --}}

    <script>
        window.addEventListener('load', function() {

            fetch('/charts/alumni/status-pernikahan')
                .then(response => response.json())
                .then(data => {

                    const el = document.querySelector("#chartStatusPernikahan");

                    const chart = new ApexCharts(el, {
                        chart: {
                            type: 'pie',
                            height: 300
                        },
                        labels: data.labels,
                        series: data.values,
                        legend: {
                            show: true,
                            position: 'bottom'
                        }
                    });

                    chart.render();
                });

        });
    </script>

    {{-- <script>
        window.addEventListener('load', function() {

            const options = {
                chart: {
                    type: 'pie',
                    height: 300
                },
                labels: ['Duda', 'Janda', 'Lajang', 'Menikah'],
                series: [1, 1, 5, 1],
                legend: {
                    show: true,
                    position: 'bottom'
                }
            };

            new ApexCharts(
                document.querySelector("#chartStatusPernikahan"),
                options
            ).render();

        });
    </script> --}}


    {{-- ========================= PIE CHART STATUS AB ========================= --}}
    <script>
        window.addEventListener('load', function() {
            fetch('/charts/alumni/ab')
                .then(res => res.json())
                .then(data => {

                    const chart = new ApexCharts(
                        document.querySelector("#chartAb"), {
                            chart: {
                                type: 'pie',
                                height: 300
                            },
                            labels: data.labels,
                            series: data.values,
                            legend: {
                                show: true,
                                position: 'bottom'
                            }
                        }
                    );

                    chart.render();
                });
        });
    </script>
@endsection
