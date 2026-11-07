@extends('layouts.app')

@section('page-header')
@endsection

@section('page-content')
    <div>
        <p class="mb-3 text-sm text-muted">Statistik mencakup seluruh akun dalam cakupan Anda, termasuk akun pengelola.</p>
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

            {{-- Grafik tren --}}
            <div class="min-w-0 rounded-xl bg-[#14213D] p-4 shadow sm:col-span-2 lg:col-span-8">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-white">Pertumbuhan Anggota per Bulan ({{ $year }})</h3>
                </div>
                <div class="min-w-0 flex-auto pt-4">
                    <!-- Chart -->
                    <div class="relative h-80 min-w-0 text-slate-300">
                        <canvas id="chart-alumni" class="h-full w-full min-w-0" role="img" aria-label="Grafik pertumbuhan anggota per bulan"></canvas>
                    </div>
                    <p id="chart-alumni-status" class="mt-2 text-sm text-slate-200" role="status">Memuat grafik anggota.</p>
                    <details class="mt-2 text-sm text-white">
                        <summary class="cursor-pointer font-medium">Lihat angka per bulan</summary>
                        <div class="mt-2 max-h-56 overflow-auto">
                            <table class="w-full text-left">
                                <thead><tr><th scope="col" class="py-1 !text-white">Bulan</th><th scope="col" class="py-1 !text-white">Anggota baru</th></tr></thead>
                                <tbody>
                                    @foreach ($chartAlumni['labels'] as $index => $month)
                                        <tr><th scope="row" class="py-1 font-normal !text-white">{{ $month }}</th><td class="py-1">{{ number_format($chartAlumni['series'][$index]) }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                </div>
            </div>

            <div class="rf-card sm:col-span-2 lg:col-span-4">
                <h3 class="font-semibold mb-3">Jumlah Kajian per Bulan</h3>
                <p class="text-sm text-slate-600" role="status">Statistik kajian belum tersedia.</p>
            </div>

            {{-- ========================= 2 CHART DALAM 1 BARIS ========================= --}}
            <div class="rf-card min-w-0 sm:col-span-2 lg:col-span-6">
                <h3 class="font-semibold mb-3">Status Pernikahan Alumni</h3>
                <div class="relative h-80 min-w-0">
                    <canvas id="chartStatusPernikahan" class="h-full w-full min-w-0" role="img" aria-label="Grafik status pernikahan"></canvas>
                </div>
                <p id="chartStatusPernikahan-status" class="mt-2 text-sm text-slate-600" role="status">Memuat data status pernikahan.</p>
                <details id="chartStatusPernikahan-data" class="mt-2 hidden text-sm">
                    <summary class="cursor-pointer font-medium">Lihat angka status pernikahan</summary>
                    <table class="mt-2 w-full text-left"><thead><tr><th scope="col">Status</th><th scope="col">Jumlah</th></tr></thead><tbody></tbody></table>
                </details>
            </div>

            <div class="rf-card min-w-0 sm:col-span-2 lg:col-span-6">
                <h3 class="font-semibold mb-3">Pekerjaan Alumni</h3>
                <div class="relative h-72 min-w-0">
                    <canvas id="chartJobs" class="h-full w-full min-w-0" role="img" aria-label="Grafik pekerjaan alumni"></canvas>
                </div>
                <p id="chartJobs-status" class="mt-2 text-sm text-slate-600" role="status">Memuat data pekerjaan.</p>
                <details id="chartJobs-data" class="mt-2 hidden text-sm">
                    <summary class="cursor-pointer font-medium">Lihat angka pekerjaan</summary>
                    <table class="mt-2 w-full text-left"><thead><tr><th scope="col">Pekerjaan</th><th scope="col">Jumlah</th></tr></thead><tbody></tbody></table>
                </details>
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
    <script id="chart-alumni-data" type="application/json">@json($chartAlumni)</script>
    <div id="chart-data-urls" class="hidden" data-marriage-url="{{ route('charts.alumni.status_pernikahan') }}" data-jobs-url="{{ route('charts.alumni.jobs') }}"></div>
@endsection
