@extends('layouts.app')

@section('page-content')
    <section data-report-workspace class="rf-card sm:p-8" aria-labelledby="summary-heading">
        <a href="{{ route('laporan.index') }}" class="rf-btn-secondary px-3">Kembali</a>
        <h1 id="summary-heading" class="mt-4 text-2xl font-semibold text-slate-900">Rekap laporan kajian</h1>
        <p class="mt-3 text-slate-700">Hanya laporan yang sudah dikirim. Jumlah peserta adalah total kehadiran dalam laporan sesuai filter, bukan jumlah orang unik.</p>
        @include('laporan.feedback')
        <form action="{{ route('laporan.summary') }}" method="GET" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @if ($canChooseRegion)
                <div>
                    <label for="wilayah_id" class="block font-medium">Wilayah</label>
                    <select id="wilayah_id" name="wilayah_id" class="mt-2 w-full rounded-lg border-slate-300">
                        <option value="">Semua wilayah</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" @selected((string) ($filters['wilayah_id'] ?? '') === (string) $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <p class="self-center text-slate-700">Wilayah: {{ $regions->first()?->name }}</p>
            @endif
            <div>
                <label for="status" class="block font-medium">Status</label>
                <select id="status" name="status" class="mt-2 w-full rounded-lg border-slate-300">
                    <option value="">Semua status terkirim</option>
                    @foreach (['submitted' => 'Dikirim', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @foreach (['dari' => 'Dari tanggal', 'sampai' => 'Sampai tanggal'] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="block font-medium">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="date" value="{{ $filters[$field] ?? '' }}" min="1000-01-01" max="9999-12-31" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
            @endforeach
            <div class="flex flex-wrap items-center gap-4 sm:col-span-2 lg:col-span-4">
                <button type="submit" class="rf-btn px-3">Terapkan</button>
                <a href="{{ route('laporan.summary') }}" class="rf-btn-secondary px-3">Reset</a>
                <a href="{{ route('laporan.export', $filters) }}" data-report-tooltip="Unduh CSV sesuai filter saat ini" class="rf-btn-secondary px-3">Ekspor</a>
            </div>
        </form>
        <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (['reports' => 'Jumlah laporan', 'submitted' => 'Menunggu review', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'male' => 'Kehadiran laki-laki', 'female' => 'Kehadiran perempuan', 'participants' => 'Total kehadiran'] as $key => $label)
                <div class="rounded-lg bg-slate-50 p-4"><dt class="text-sm text-slate-600">{{ $label }}</dt><dd class="mt-2 text-2xl font-semibold text-slate-900">{{ number_format($totals[$key], 0, ',', '.') }}</dd></div>
            @endforeach
        </dl>
        <ul class="mt-6 divide-y divide-slate-200">
            @forelse ($reports as $report)
                <li class="py-5">
                    <a href="{{ route('laporan.show', $report) }}" data-report-modal class="break-words text-lg font-semibold text-indigo-700 hover:underline">{{ $report->judul }}</a>
                    <p class="mt-2 text-sm text-slate-600">{{ $report->tanggal->format('d/m/Y') }} · {{ $report->wilayah?->name ?? 'Wilayah belum ditentukan' }} · {{ $report->user?->name }}</p>
                    <p class="mt-1 text-sm text-slate-700">@include('laporan.status', ['status' => $report->status]) · {{ (int) $report->peserta_l + (int) $report->peserta_p }} peserta</p>
                </li>
            @empty
                <li class="py-6 text-slate-600">Belum ada laporan sesuai filter.</li>
            @endforelse
        </ul>
        <div class="mt-5">{{ $reports->links() }}</div>
    </section>
    @include('laporan.modal')
@endsection
