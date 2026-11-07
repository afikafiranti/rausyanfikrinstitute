<article data-materi-modal-content class="bg-white p-5 sm:p-7" aria-labelledby="materi-modal-heading">
    <header class="sticky top-0 z-10 flex items-start justify-between gap-3 border-b border-slate-200 bg-white pb-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-brand">Detail materi</p>
            <h1 id="materi-modal-heading" tabindex="-1" class="mt-1 break-words text-2xl font-semibold text-ink">{{ $playlist->title }}</h1>
            <p class="mt-2 text-sm text-muted">{{ $playlist->level?->description ?: ($playlist->level?->name ?? 'Tanpa level') }} · {{ $playlist->is_active ? 'Aktif' : 'Draf' }}</p>
        </div>
        <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary shrink-0 px-3">Tutup</a>
    </header>
    @include('materi.feedback')
    @if ($playlist->description)
        <p class="mt-5 whitespace-pre-line break-words text-sm leading-6 text-slate-700">{{ $playlist->description }}</p>
    @endif
    <div class="mt-5 flex flex-wrap gap-2">
        <a href="https://www.youtube.com/playlist?list={{ rawurlencode($playlist->youtube_playlist_id) }}" target="_blank" rel="noopener noreferrer" aria-label="Buka playlist di YouTube (tab baru)" class="rf-btn-secondary px-3">YouTube</a>
        @if ($canManage)
            <a href="{{ route('materi.playlists.edit', $playlist) }}" data-materi-modal class="rf-btn-secondary px-3">Edit</a>
            <a href="{{ route('materi.playlists.confirm-delete', $playlist) }}" data-materi-modal class="rf-btn-danger px-3">Hapus</a>
        @endif
    </div>

    <section class="mt-7 border-t border-slate-200 pt-5" aria-labelledby="materi-videos-heading">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 id="materi-videos-heading" class="text-lg font-semibold text-ink">Video</h2>
            @if ($canManage)
                <a href="{{ route('materi.videos.create', $playlist) }}" data-materi-modal class="rf-btn px-3">Tambah</a>
            @endif
        </div>
        <ul class="mt-3 divide-y divide-slate-200">
            @forelse ($videos as $video)
                <li class="flex flex-wrap items-center justify-between gap-3 py-3">
                    @include('materi.partials.thumbnail', ['videoId' => $video->youtube_video_id, 'size' => 'video'])
                    <div class="min-w-0 flex-1">
                        <h3 class="break-words font-medium text-ink">{{ $video->title }}</h3>
                        <p class="mt-1 text-xs text-muted">{{ $video->duration_sec ? gmdate('H:i:s', $video->duration_sec) : 'Durasi belum diisi' }}</p>
                    </div>
                    <div role="group" aria-label="Aksi video {{ $video->title }}" class="flex flex-wrap gap-2">
                        <a href="https://www.youtube.com/watch?v={{ rawurlencode($video->youtube_video_id) }}" target="_blank" rel="noopener noreferrer" aria-label="Tonton {{ $video->title }} di YouTube (tab baru)" class="rf-btn-secondary px-3">Tonton</a>
                        @if ($canManage)
                            <a href="{{ route('materi.videos.edit', [$playlist, $video]) }}" data-materi-modal class="rf-btn-secondary px-3">Edit</a>
                            <a href="{{ route('materi.videos.confirm-delete', [$playlist, $video]) }}" data-materi-modal class="rf-btn-danger px-3">Hapus</a>
                        @endif
                    </div>
                </li>
            @empty
                <li class="py-5 text-sm text-muted">Belum ada video dalam playlist ini.</li>
            @endforelse
        </ul>
        <p class="mt-3 text-xs text-muted">Menampilkan {{ $videos->firstItem() ?? 0 }}–{{ $videos->lastItem() ?? 0 }} dari {{ $videos->total() }} video · Halaman {{ $videos->currentPage() }} dari {{ $videos->lastPage() }}</p>
        @if ($videos->hasPages())
            <nav aria-label="Halaman video" class="mt-3 flex flex-wrap gap-2">
                @if ($videos->onFirstPage())
                    <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true">Sebelumnya</span>
                @else
                    <a href="{{ $videos->previousPageUrl() }}" data-materi-modal rel="prev" class="rf-btn-secondary px-3">Sebelumnya</a>
                @endif
                @if ($videos->hasMorePages())
                    <a href="{{ $videos->nextPageUrl() }}" data-materi-modal rel="next" class="rf-btn-secondary px-3">Berikutnya</a>
                @else
                    <span class="rf-btn-secondary cursor-not-allowed px-3 opacity-50" aria-disabled="true">Berikutnya</span>
                @endif
            </nav>
        @endif
    </section>
</article>
