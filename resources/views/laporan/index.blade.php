@extends('layouts.app')

@section('page-content')
    <section class="rounded-xl bg-white p-6 shadow sm:p-8" aria-labelledby="laporan-heading">
        <h1 id="laporan-heading" class="text-2xl font-semibold text-slate-900">Laporan Kajian</h1>
        <p class="mt-3 text-slate-700">Simpan catatan kegiatan sebagai draf, lalu kirim setelah diperiksa.</p>
        @can('review-report')
            <a href="{{ route('laporan.review') }}" class="mt-4 inline-block font-medium text-indigo-700 hover:underline">Buka antrean review</a>
            <a href="{{ route('laporan.summary') }}" class="mt-4 ml-4 inline-block font-medium text-indigo-700 hover:underline">Rekap dan ekspor</a>
        @endcan
        <a href="{{ route('laporan.create') }}" class="mt-5 inline-block rounded-lg bg-brand px-5 py-3 font-medium text-white hover:bg-brand-dark">Buat draf laporan</a>
        @include('laporan.feedback')
        <ul class="mt-6 divide-y divide-slate-200">
            @forelse ($reports as $report)
                <li class="py-5">
                    <a href="{{ route('laporan.show', $report) }}" class="break-words text-lg font-semibold text-indigo-700 hover:underline">{{ $report->judul }}</a>
                    <p class="mt-2 text-sm text-slate-600">{{ $report->tanggal->format('d/m/Y') }} · {{ $report->wilayah?->name ?? 'Wilayah belum ditentukan' }} · {{ $report->user?->name }}</p>
                    <p class="mt-1 text-sm text-slate-700">@include('laporan.status', ['status' => $report->status]) · {{ $report->total_peserta }} peserta</p>
                </li>
            @empty
                <li class="py-6 text-slate-600">Belum ada laporan.</li>
            @endforelse
        </ul>
        <div class="mt-5">{{ $reports->links() }}</div>
    </section>
@endsection
