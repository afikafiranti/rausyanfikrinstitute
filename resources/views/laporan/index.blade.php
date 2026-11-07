@extends('layouts.app')

@section('page-content')
    <section data-report-workspace class="rf-card sm:p-6" aria-labelledby="laporan-heading">
        <header class="border-b border-slate-200 pb-5">
            <h1 id="laporan-heading" class="text-2xl font-semibold text-ink">Laporan Kajian</h1>
            <p class="mt-2 text-sm leading-6 text-muted">Simpan catatan sebagai draf, periksa, lalu kirim. Hanya pembuat draf yang dapat mengeditnya.</p>
            <nav class="mt-4 flex flex-wrap items-center gap-2" aria-label="Aksi laporan">
                <a href="{{ route('laporan.create') }}" data-report-modal class="rf-btn px-3">Buat</a>
                @can('review-report')
                    <a href="{{ route('laporan.review') }}" data-report-tooltip="Buka antrean laporan yang menunggu review" class="rf-btn-secondary px-3">Antrean</a>
                    <a href="{{ route('laporan.summary') }}" class="rf-btn-secondary px-3">Rekap</a>
                @endcan
            </nav>
        </header>
        @include('laporan.feedback')
        <ul class="divide-y divide-slate-200">
            @forelse ($reports as $report)
                <li class="flex flex-wrap items-center gap-x-3 gap-y-1 py-3">
                    <a href="{{ route('laporan.show', $report) }}" data-report-modal class="group flex min-w-0 flex-1 items-start gap-3 rounded-lg py-1 sm:gap-4"
                        aria-labelledby="report-title-{{ $report->id }}" aria-describedby="report-photo-{{ $report->id }}">
                        <span id="report-photo-{{ $report->id }}" role="img" aria-label="{{ $report->foto_url ? 'Foto dokumentasi tersedia' : 'Belum ada foto dokumentasi' }}"
                            class="relative flex h-16 w-20 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                            <span class="px-2 text-center text-xs leading-4 text-muted" aria-hidden="true">{{ $report->foto_url ? 'Foto tidak tersedia' : 'Belum ada foto' }}</span>
                            @if ($report->foto_url)
                                <img src="{{ route('laporan.photo', $report) }}" alt="" width="80" height="64" loading="lazy" decoding="async"
                                    class="absolute inset-0 h-full w-full bg-slate-100 object-cover" onerror="this.hidden = true; this.parentElement.setAttribute('aria-label', 'Foto dokumentasi tidak tersedia')">
                            @endif
                        </span>
                        <div class="min-w-0 flex-1">
                            <h2 id="report-title-{{ $report->id }}" class="break-words text-base font-semibold leading-6 text-ink group-hover:text-brand group-hover:underline">{{ $report->judul }}</h2>
                            <p class="mt-1 break-words text-sm leading-5 text-muted">{{ $report->tanggal->format('d/m/Y') }} · {{ $report->wilayah?->name ?? 'Wilayah belum ditentukan' }} · {{ $report->user?->name }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted">
                                <span class="rounded-md bg-slate-100 px-2 py-1 font-medium text-ink">@include('laporan.status', ['status' => $report->status])</span>
                                <span>{{ $report->total_peserta }} peserta</span>
                            </div>
                        </div>
                    </a>
                    @if ($report->status === 'draft' && $report->user_id === auth()->id())
                        <a href="{{ route('laporan.edit', $report) }}" data-report-modal data-report-tooltip="Edit isi dan foto draf milik Anda" class="rf-btn-secondary px-3">Edit</a>
                    @endif
                </li>
            @empty
                <li class="py-8 text-sm text-muted">Belum ada laporan. Pilih Buat untuk mencatat kegiatan pertama.</li>
            @endforelse
        </ul>
        <div class="mt-5">{{ $reports->links() }}</div>
    </section>
    @include('laporan.modal')
@endsection
