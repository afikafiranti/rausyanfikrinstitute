@extends('layouts.app')

@section('page-content')
    <section data-materi-workspace class="rf-card sm:p-6" aria-labelledby="materi-heading">
        <header class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">
            <div>
                <h1 id="materi-heading" class="text-2xl font-semibold text-ink">Materi Kajian</h1>
                <p class="mt-2 text-sm text-muted">{{ $canManage ? 'Kelola playlist dan video kajian.' : 'Pilihan kajian untuk level belajar Anda.' }}</p>
            </div>
            @if ($canManage)
                <a href="{{ route('materi.playlists.create') }}" data-materi-modal class="rf-btn px-3">Buat</a>
            @endif
        </header>

        @error('q')
            <p role="alert" class="mt-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800">{{ $message }}</p>
        @enderror
        <div data-materi-workspace-status role="status" aria-live="polite" tabindex="-1" hidden class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-ink"></div>

        <form data-materi-search action="{{ route('materi.index') }}" method="GET" class="mt-5 flex flex-wrap items-end gap-2">
            <div class="min-w-[12rem] flex-1">
                <label for="materi-q" class="block text-sm font-medium text-ink">Cari materi</label>
                <input id="materi-q" name="q" type="search" value="{{ $q }}" maxlength="150" placeholder="Judul atau deskripsi playlist" class="mt-1 w-full">
            </div>
            <button type="submit" data-materi-search-button class="rf-btn px-3">Cari</button>
        </form>

        <ul data-materi-list class="mt-5 space-y-3">
            @forelse ($items as $playlist)
                <li data-materi-row class="rounded-xl border border-slate-200 bg-white p-4 transition-colors hover:border-slate-400 focus-within:border-brand">
                    <div class="flex items-start gap-3">
                        @include('materi.partials.thumbnail', ['videoId' => $playlist->thumbnail_video_id, 'emptyLabel' => $playlist->videos_count ? 'Thumbnail tidak tersedia' : 'Belum ada video'])
                        <div class="min-w-0 flex-1">
                            <h2 class="break-words text-base font-semibold text-ink">
                                <a href="{{ route('materi.show', $playlist) }}" data-materi-modal class="rounded hover:text-brand hover:underline">{{ $playlist->title }}</a>
                            </h2>
                            <p class="mt-1 text-sm text-muted">{{ $playlist->level?->description ?: ($playlist->level?->name ?? 'Tanpa level') }} · {{ $playlist->videos_count }} video</p>
                        </div>
                        <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-ink">{{ $playlist->is_active ? 'Aktif' : 'Draf' }}</span>
                    </div>
                    @if ($playlist->description)
                        <p class="mt-2 line-clamp-2 break-words text-sm leading-6 text-muted">{{ $playlist->description }}</p>
                    @endif
                    <div role="group" aria-label="Aksi {{ $playlist->title }}" class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('materi.show', $playlist) }}" data-materi-modal class="rf-btn-secondary p-2" aria-label="Detail playlist {{ $playlist->title }}" title="Detail playlist {{ $playlist->title }}" data-action-tooltip="Detail playlist {{ $playlist->title }}"><i class="fas fa-magnifying-glass" aria-hidden="true"></i></a>
                        @if ($canManage)
                            <a href="{{ route('materi.playlists.edit', $playlist) }}" data-materi-modal class="rf-btn-secondary p-2" aria-label="Edit playlist {{ $playlist->title }}" title="Edit playlist {{ $playlist->title }}" data-action-tooltip="Edit playlist {{ $playlist->title }}"><i class="fas fa-pen" aria-hidden="true"></i></a>
                            <a href="{{ route('materi.playlists.confirm-delete', $playlist) }}" data-materi-modal class="rf-btn-danger p-2" aria-label="Hapus playlist {{ $playlist->title }}" title="Hapus playlist {{ $playlist->title }}" data-action-tooltip="Hapus playlist {{ $playlist->title }}"><i class="fas fa-trash" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </li>
            @empty
                <li class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-muted">
                    {{ $q !== '' ? 'Tidak ada materi yang cocok. Ubah pencarian untuk melihat playlist lain.' : ($canManage ? 'Belum ada playlist. Pilih Buat untuk menambahkan materi.' : 'Belum ada materi yang tersedia untuk level Anda.') }}
                </li>
            @endforelse
        </ul>

        <footer class="mt-5 flex flex-col gap-2 border-t border-slate-200 pt-4 text-sm text-muted">
            <p>Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} dari {{ number_format($items->total(), 0, ',', '.') }} playlist · Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}</p>
            @if ($items->hasPages())
                <nav aria-label="Halaman materi" class="flex flex-wrap gap-2">
                    @if ($items->onFirstPage())
                        <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true">Sebelumnya</span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" rel="prev" class="rf-btn-secondary px-3">Sebelumnya</a>
                    @endif
                    @if ($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" rel="next" class="rf-btn-secondary px-3">Berikutnya</a>
                    @else
                        <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true">Berikutnya</span>
                    @endif
                </nav>
            @endif
        </footer>
    </section>
    @include('materi.modal')
@endsection
