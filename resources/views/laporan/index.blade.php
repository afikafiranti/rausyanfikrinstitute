@extends('layouts.app')
@section('page-content')
    @php
        // Data demo; ganti dengan $reports dari controller jika sudah ada
        $rows = collect([
            (object) [
                'id' => 1,
                'tgl' => '2025-09-10',
                'wilayah' => 'Luwu',
                'judul' => 'Kajian Rutin Masjid Al-...',
                'status' => 'Menunggu Review',
            ],
            (object) [
                'id' => 2,
                'tgl' => '2025-09-08',
                'wilayah' => 'Luwu Timur',
                'judul' => 'Koordinasi Koorda',
                'status' => 'Disetujui',
            ],
            (object) [
                'id' => 3,
                'tgl' => '2025-09-05',
                'wilayah' => 'Luwu',
                'judul' => 'Pengumpulan Donasi',
                'status' => 'Ditolak',
            ],
        ]);
        if (isset($reports)) {
            $rows = collect($reports);
        }
        $totalRows = $rows->count();

        $routeShow = fn($id) => Route::has('laporan.show')
            ? route('laporan.show', $id)
            : (Route::has('reports.show')
                ? route('reports.show', $id)
                : (Route::has('report.show')
                    ? route('report.show', $id)
                    : '#'));

        $routeEdit = fn($id) => Route::has('laporan.edit')
            ? route('laporan.edit', $id)
            : (Route::has('reports.edit')
                ? route('reports.edit', $id)
                : (Route::has('report.edit')
                    ? route('report.edit', $id)
                    : '#'));

        $storeAction = Route::has('laporan.store')
            ? route('laporan.store')
            : (Route::has('reports.store')
                ? route('reports.store')
                : (Route::has('report.store')
                    ? route('report.store')
                    : '#'));
    @endphp

    {{-- ===================== DESKTOP (≥ md) ===================== --}}
    <div class="hidden md:block">
        <div class="grid grid-cols-12 gap-4">
            {{-- Form Input Laporan --}}
            <section class="col-span-4 rounded-xl bg-white p-4 shadow">
                <h3 class="font-semibold mb-3">Input Laporan</h3>
                <form action="{{ $storeAction }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Tanggal</label>
                        <input type="date" name="tanggal" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Wilayah</label>
                        <select name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            <option value="1">Yogyakarta</option>
                            <option value="2">Makassar</option>
                            <option value="3">Palopo</option>
                            <option value="4">Buton</option>
                            <option value="5">Kendari</option>
                            <option value="6">Malang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Judul Kegiatan</label>
                        <input type="text" name="judul" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                            placeholder="Nama kegiatan...">
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Ringkasan</label>
                        <textarea name="ringkasan" rows="4" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                            placeholder="Uraian singkat..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Lampiran (opsional)</label>
                        <input type="file" name="lampiran"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <button class="rf-btn w-full" type="submit">Simpan Laporan</button>
                </form>
            </section>

            {{-- Riwayat Laporan --}}
            <section class="col-span-8 rounded-xl bg-white p-4 shadow">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Riwayat Terbaru</h3>
                    <div class="text-sm text-slate-500">{{ $totalRows }} item</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-slate-600 border-b">
                            <tr>
                                <th class="py-2 pr-4">Tanggal</th>
                                <th class="py-2 pr-4">Wilayah</th>
                                <th class="py-2 pr-4">Judul</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($rows as $r)
                                @php
                                    $tgl = is_array($r)
                                        ? $r['tgl']
                                        : $r->tgl ??
                                            ($r->tanggal ??
                                                \Illuminate\Support\Str::of($r->created_at ?? '')->toString());
                                    $wil = is_array($r) ? $r['wilayah'] : $r->wilayah->nama ?? ($r->wilayah ?? '-');
                                    $judul = is_array($r) ? $r['judul'] : $r->judul ?? ($r->title ?? '-');
                                    $status = is_array($r)
                                        ? $r['status']
                                        : \Illuminate\Support\Str::title($r->status ?? 'Menunggu Review');
                                    $color =
                                        $status === 'Disetujui'
                                            ? 'green'
                                            : ($status === 'Menunggu Review'
                                                ? 'yellow'
                                                : 'red');
                                    $id = is_array($r) ? $r['id'] ?? null : $r->id ?? null;
                                @endphp
                                <tr>
                                    <td class="py-2 pr-4">{{ $tgl }}</td>
                                    <td class="py-2 pr-4">{{ $wil }}</td>
                                    <td class="py-2 pr-4">{{ $judul }}</td>
                                    <td class="py-2 pr-4"><x-badge :color="$color">{{ $status }}</x-badge></td>
                                    <td class="py-2">
                                        <div class="flex gap-2">
                                            <a href="{{ $id ? $routeShow($id) : '#' }}"
                                                class="text-indigo-600 hover:underline">Detail</a>
                                            <a href="{{ $id ? $routeEdit($id) : '#' }}"
                                                class="text-slate-600 hover:underline">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-500">Belum ada laporan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination jika tersedia --}}
                @if (method_exists($rows, 'links'))
                    <div class="mt-4">{{ $rows->withQueryString()->links() }}</div>
                @endif
            </section>
        </div>
    </div>

    {{-- ====================== MOBILE (< md) ====================== --}}
    <div class="md:hidden">
        <h1 class="text-xl font-semibold">Laporan</h1>

        {{-- Form ringkas --}}
        <div class="rf-section">
            <h3 class="font-semibold mb-3">Input Laporan</h3>
            <form action="{{ $storeAction }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="date" name="tanggal" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                <select name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    <option value="">Wilayah</option>
                    <option value="1">Luwu</option>
                    <option value="2">Luwu Timur</option>
                </select>
                <input type="text" name="judul" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                    placeholder="Judul kegiatan...">
                <textarea name="ringkasan" rows="3" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                    placeholder="Ringkasan..."></textarea>
                <input type="file" name="lampiran" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                <button class="rf-btn w-full" type="submit">Simpan</button>
            </form>
        </div>

        {{-- Riwayat ringkas --}}
        <div class="mt-4 space-y-3">
            @forelse($rows as $r)
                @php
                    $tgl = is_array($r) ? $r['tgl'] : $r->tgl ?? ($r->tanggal ?? '');
                    $wil = is_array($r) ? $r['wilayah'] : $r->wilayah->nama ?? ($r->wilayah ?? '-');
                    $judul = is_array($r) ? $r['judul'] : $r->judul ?? ($r->title ?? '-');
                    $status = is_array($r)
                        ? $r['status']
                        : \Illuminate\Support\Str::title($r->status ?? 'Menunggu Review');
                    $color = $status === 'Disetujui' ? 'green' : 'yellow';
                    $id = is_array($r) ? $r['id'] ?? null : $r->id ?? null;
                @endphp
                <div class="rf-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $judul }}</div>
                            <div class="text-xs text-slate-500">{{ $tgl }} • {{ $wil }}</div>
                        </div>
                        <x-badge :color="$color">{{ $status }}</x-badge>
                    </div>
                    <div class="mt-3 flex gap-3 text-sm">
                        <a href="{{ $id ? $routeShow($id) : '#' }}" class="text-indigo-600">Detail</a>
                        <a href="{{ $id ? $routeEdit($id) : '#' }}" class="text-slate-600">Edit</a>
                    </div>
                </div>
            @empty
                <div class="rf-card text-slate-500">Belum ada laporan.</div>
            @endforelse

            @if (method_exists($rows, 'links'))
                <div class="pt-2">{{ $rows->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
@endsection
