@extends('layouts.app')

@section('page-content')
    <section class="rounded-xl bg-white p-6 shadow sm:p-8" aria-labelledby="review-heading">
        <h1 id="review-heading" class="text-2xl font-semibold text-slate-900">Review Laporan</h1>
        <p class="mt-4 text-slate-700">Laporan yang menunggu keputusan ditampilkan dari tanggal kegiatan paling awal. Laporan sendiri tidak masuk antrean review.</p>
        <p class="mt-3 text-sm text-slate-600">Wilayah: {{ $regionName ?? 'Semua wilayah' }}</p>
        @if ($wilayahId && auth()->user()->hasAnyRole(['super_admin', 'admin']))
            <a href="{{ route('laporan.review') }}" class="mt-2 inline-block text-indigo-700 hover:underline">Tampilkan semua wilayah</a>
        @endif
        @include('laporan.feedback')
        <ul class="mt-5 divide-y divide-slate-200">
            @forelse ($reports as $report)
                <li class="py-5">
                    <a href="{{ route('laporan.show', $report) }}" class="break-words text-lg font-semibold text-indigo-700 hover:underline">{{ $report->judul }}</a>
                    <p class="mt-2 text-sm text-slate-600">{{ $report->tanggal->format('d/m/Y') }} · {{ $report->wilayah?->name ?? 'Wilayah belum ditentukan' }} · {{ $report->user?->name }}</p>
                </li>
            @empty
                <li class="py-5 text-slate-600">Tidak ada laporan yang menunggu review.</li>
            @endforelse
        </ul>
        <div class="mt-5">{{ $reports->links() }}</div>
        <a href="{{ route('laporan.summary') }}" class="mt-5 inline-block text-indigo-700 hover:underline">Rekap dan ekspor</a>
        <a href="{{ route('laporan.index') }}" class="mt-6 inline-block rounded-lg bg-brand px-5 py-3 font-medium text-white hover:bg-brand-dark">Kembali ke laporan</a>
    </section>
@endsection
