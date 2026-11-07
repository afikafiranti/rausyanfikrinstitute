@extends('layouts.app')

@section('page-content')
    <section data-report-workspace class="rf-card sm:p-6" aria-labelledby="laporan-heading">
        <header class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">
            <div class="min-w-0">
                <h1 id="laporan-heading" class="text-2xl font-semibold text-ink">Laporan Kajian</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-muted">Catat kegiatan, pantau status, dan tindak lanjuti laporan dari satu daftar.</p>
            </div>
            <div class="flex flex-wrap gap-2" aria-label="Aksi laporan">
                <a href="{{ route('laporan.create') }}" data-report-modal class="rf-btn px-3">Buat</a>
                @if (($filters['status'] ?? '') === 'draft')
                    <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true" aria-label="Ekspor tidak tersedia untuk draf">Ekspor</span>
                @else
                    <a href="{{ route('laporan.export', array_filter($filters, fn ($value) => $value !== null && $value !== '')) }}" data-report-tooltip="Unduh Excel (.xlsx) laporan terkirim sesuai filter" class="rf-btn-secondary px-3">Ekspor</a>
                @endif
            </div>
        </header>

        @include('laporan.feedback')
        <div data-report-workspace-status role="status" aria-live="polite" tabindex="-1" hidden class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-ink"></div>

        <form data-report-filters action="{{ route('laporan.index') }}" method="GET" class="mt-5 grid items-end gap-3 sm:grid-cols-2 xl:grid-cols-[repeat(4,minmax(0,1fr))_auto]">
            <div>
                <label for="filter-status" class="block text-sm font-medium text-ink">Status</label>
                <select id="filter-status" name="status" class="mt-1 w-full">
                    <option value="">Semua status</option>
                    @foreach (['draft' => 'Draf', 'submitted' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if ($canChooseRegion)
                <div>
                    <label for="filter-wilayah_id" class="block text-sm font-medium text-ink">Wilayah</label>
                    <select id="filter-wilayah_id" name="wilayah_id" class="mt-1 w-full">
                        <option value="">Semua wilayah</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" @selected((string) ($filters['wilayah_id'] ?? '') === (string) $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <p class="self-end pb-3 text-sm text-muted">Wilayah: {{ $regions->first()?->name ?? 'Belum ditentukan' }}</p>
            @endif
            <div>
                <label for="filter-dari" class="block text-sm font-medium text-ink">Dari</label>
                <input id="filter-dari" name="dari" type="date" value="{{ $filters['dari'] ?? '' }}" min="1000-01-01" max="9999-12-31" class="mt-1 w-full">
            </div>
            <div>
                <label for="filter-sampai" class="block text-sm font-medium text-ink">Sampai</label>
                <input id="filter-sampai" name="sampai" type="date" value="{{ $filters['sampai'] ?? '' }}" min="1000-01-01" max="9999-12-31" class="mt-1 w-full">
            </div>
            <div class="flex sm:col-span-2 xl:col-span-1">
                <button type="submit" data-report-filter-button class="rf-btn px-3">Filter</button>
            </div>
        </form>

        <dl class="mt-5 flex flex-wrap gap-x-7 gap-y-2 border-y border-slate-200 py-3 text-sm">
            <div class="flex items-baseline gap-2"><dt class="text-muted">Laporan</dt><dd class="font-semibold text-ink">{{ number_format($totals['reports'], 0, ',', '.') }}</dd></div>
            <div class="flex items-baseline gap-2"><dt class="text-muted">Menunggu</dt><dd class="font-semibold text-ink">{{ number_format($totals['submitted'], 0, ',', '.') }}</dd></div>
            <div class="flex items-baseline gap-2"><dt class="text-muted">Peserta</dt><dd class="font-semibold text-ink">{{ number_format($totals['participants'], 0, ',', '.') }}</dd><span class="text-xs text-muted">L {{ number_format($totals['male'], 0, ',', '.') }} / P {{ number_format($totals['female'], 0, ',', '.') }}</span></div>
        </dl>

        <ul data-report-list class="mt-4 space-y-3">
            @forelse ($reports as $report)
                <li data-report-row class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 lg:flex-row lg:items-center">
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
                                <span>{{ (int) $report->peserta_l + (int) $report->peserta_p }} peserta</span>
                            </div>
                        </div>
                    </a>
                    <div role="group" class="flex flex-wrap items-center gap-2 pl-0 sm:pl-24 lg:pl-0" aria-label="Aksi {{ $report->judul }}">
                        <a href="{{ route('laporan.show', $report) }}" data-report-modal class="rf-btn-secondary px-3">Detail</a>
                        @if ($report->status === 'draft' && $report->user_id === auth()->id())
                            <a href="{{ route('laporan.edit', $report) }}" data-report-modal class="rf-btn-secondary px-3">Edit</a>
                            <form data-report-row-form action="{{ route('laporan.destroy', $report) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rf-btn-danger px-3" data-report-tooltip="Hapus draf dan foto secara permanen">Hapus</button>
                            </form>
                        @elseif ($report->status === 'submitted' && $report->user_id !== auth()->id())
                            <a href="{{ route('laporan.show', $report) }}" data-report-modal data-report-focus="approve" data-report-tooltip="Periksa laporan sebelum menyetujui" class="rf-btn-secondary px-3">Setujui</a>
                            <a href="{{ route('laporan.show', $report) }}" data-report-modal data-report-focus="reject" data-report-tooltip="Periksa laporan dan beri alasan penolakan" class="rf-btn-secondary px-3">Tolak</a>
                        @endif
                    </div>
                </li>
            @empty
                <li class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-muted">Tidak ada laporan sesuai filter. Ubah filter atau pilih Buat untuk mencatat kegiatan.</li>
            @endforelse
        </ul>
        <footer class="mt-5 flex flex-col gap-2 border-t border-slate-200 pt-4 text-sm text-muted">
            <p>Menampilkan {{ $reports->firstItem() ?? 0 }}–{{ $reports->lastItem() ?? 0 }} dari {{ number_format($reports->total(), 0, ',', '.') }} laporan · Halaman {{ $reports->currentPage() }} dari {{ $reports->lastPage() }}</p>
            @if ($reports->hasPages())
                <nav aria-label="Halaman laporan" class="flex flex-wrap gap-2">
                    @if ($reports->onFirstPage())
                        <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true">Sebelumnya</span>
                    @else
                        <a href="{{ $reports->previousPageUrl() }}" rel="prev" class="rf-btn-secondary px-3">Sebelumnya</a>
                    @endif
                    @if ($reports->hasMorePages())
                        <a href="{{ $reports->nextPageUrl() }}" rel="next" class="rf-btn-secondary px-3">Berikutnya</a>
                    @else
                        <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true">Berikutnya</span>
                    @endif
                </nav>
            @endif
        </footer>
    </section>
    @include('laporan.modal')
@endsection
