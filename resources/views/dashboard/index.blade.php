@extends('layouts.app')

@section('page-header')
@endsection

@section('page-content')
    {{-- DESKTOP --}}
    <div class="hidden md:block">
        <div class="grid grid-cols-12 gap-4 pb-4">
            {{-- STATISTIK UTAMA --}}
            <div class="col-span-3 rounded-lg bg-white p-4 shadow">
                <div class="text-xs text-slate-500">Total Alumni</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($totalAlumni) }}</div>
            </div>

            <div class="col-span-3 rounded-lg bg-white p-4 shadow">
                <div class="text-xs text-slate-500">Alumni Baru Bulan Ini</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($alumniBaru) }}</div>
            </div>

            <div class="col-span-3 rounded-lg bg-white p-4 shadow">
                <div class="text-xs text-slate-500">Alumni Aktif</div>
                <div class="mt-1 text-2xl font-semibold">{{ number_format($alumniAktif) }}</div>
            </div>

            <div class="col-span-3 rounded-lg bg-white p-4 shadow">
                <div class="text-xs text-slate-500">Persentase Verifikasi</div>
                <div class="mt-1 text-2xl font-semibold">{{ round($persenVerifikasi, 1) }}%</div>
            </div>

            {{-- NOTIFIKASI VERIFIKASI --}}
            @can('review-user')
                <div class="col-span-12 rounded-lg bg-slate-50 p-4 shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-black font-semibold">Verifikasi Pending</div>
                            <div class="text-sm text-slate-800">Akun menunggu persetujuan Koorda</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="rf-badge bg-yellow-100 text-yellow-800">{{ $pendingCount }} akun</div>
                            <a href="{{ route('verification.index') }}" class="rf-btn">
                                <i class="fas fa-user-check"></i> Buka
                            </a>
                        </div>
                    </div>
                </div>
            @endcan

            {{-- GRAFIK TREN (ApexCharts) --}}
            <div class="col-span-8 rounded-lg bg-slate-900 p-4 shadow">
                <div class="flex items-center justify-between -mb-8">
                    <h3 class="text-white px-4 font-semibold ">Pertumbuhan Anggota per Bulan ({{ $year }})</h3>
                </div>
                <div class="p-4 flex-auto">
                    <!-- Chart -->
                    <div class="relative h-350-px text-gray-500">
                        <div id="chart-alumni" class="w-full text-white h-80"></div>
                    </div>
                </div>
            </div>

            {{-- GRAFIK KAJIAN Data Masih Dummy  --}}
            <div class="col-span-4 rounded-lg bg-slate-50  p-4 shadow">
                <div class="flex items-center justify-between -mb-8">
                    <h3 class="font-semibold mb-3">Jumlah Kajian per Bulan</h3>
                </div>
                <div class="p-4 flex-auto text-gray-500">
                    <!-- Chart -->
                    <div class="relative h-350-px">
                        <div id="chartKajian" class="w-full h-64"></div>
                    </div>
                </div>
            </div>

            {{-- BLOK TABEL BAWAH  --}}

            <div class="col-span-8 rounded-lg bg-slate-50 shadow">
                {{-- TABEL SEBARAN ANGKATAN --}}
                <div class="flex items-center justify-between p-4">
                    <h3 class="font-semibold text-base text-blueGray-700">Sebaran Alumni per Angkatan
                    </h3>
                    <div class="p-4 flex-auto text-right">
                        <a href="{{ route('alumni.index') }}"
                            class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none ease-linear transition-all duration-150">
                            See all
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
            <div class="col-span-4 rounded-lg bg-slate-50 shadow">
                <div class="flex items-center justify-between p-4">
                    <h3 class="font-semibold text-base text-blueGray-700">Sebaran Alumni per Wilayah
                    </h3>

                    <div class="p-4 flex-auto text-right">
                        <a href="#"
                            class="bg-indigo-500 text-white active:bg-indigo-600 text-xs font-bold uppercase px-3 py-1 rounded outline-none focus:outline-none ease-linear transition-all duration-150">
                            See all
                        </a>
                    </div>
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

    <script>
        // --- Dummy Data Kajian per Bulan ---
        const kajianPerBulan = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            data2024: [4, 6, 5, 3, 7, 4, 6, 5, 7, 6, 8, 5],
            data2025: [5, 7, 6, 4, 8, 5, 7, 6, 8, 10, 9, 7]
        };

        const optionsKajianBulan = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [{
                    name: 'Tahun 2024',
                    data: kajianPerBulan.data2024
                },
                {
                    name: 'Tahun 2025',
                    data: kajianPerBulan.data2025
                }
            ],
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '13px',
                fontWeight: 500,
                labels: {
                    colors: '#1e293b'
                },
                markers: {
                    width: 12,
                    height: 12,
                    radius: 12
                },
                itemMargin: {
                    horizontal: 12,
                    vertical: 4
                }
            },
            grid: {
                borderColor: '#E2E8F0',
                strokeDashArray: 3
            },
            xaxis: {
                categories: kajianPerBulan.labels,
                axisBorder: {
                    color: '#E2E8F0'
                },
                axisTicks: {
                    color: '#E2E8F0'
                },
                labels: {
                    style: {
                        colors: '#475569'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Kajian',
                    style: {
                        color: '#475569'
                    }
                },
                labels: {
                    style: {
                        colors: '#475569'
                    }
                }
            },
            fill: {
                opacity: 1
            },
            colors: ['#6366f1', '#ec4899'],
            tooltip: {
                theme: 'light',
                y: {
                    formatter: val => val + " Kajian"
                }
            }
        };

        new ApexCharts(document.querySelector("#chartKajian"), optionsKajianBulan).render();
    </script>
@endsection
