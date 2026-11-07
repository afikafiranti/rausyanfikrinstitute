<section data-materi-modal-content class="bg-white p-5 sm:p-7" aria-labelledby="materi-modal-heading">
    <header class="flex items-start justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <p class="text-sm font-medium text-red-700">Konfirmasi penghapusan</p>
            <h1 id="materi-modal-heading" tabindex="-1" class="mt-1 break-words text-2xl font-semibold text-ink">Hapus {{ $video ? 'video' : 'playlist' }}?</h1>
        </div>
        <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary shrink-0 px-3">Tutup</a>
    </header>
    @include('materi.feedback')
    <p class="mt-5 break-words font-semibold text-ink">{{ $video?->title ?? $playlist->title }}</p>
    <p class="mt-2 text-sm leading-6 text-slate-700">
        {{ $video ? 'Video ini akan dihapus secara permanen.' : 'Playlist ini dan semua video di dalamnya akan dihapus secara permanen.' }}
    </p>
    <form data-materi-form data-materi-delete action="{{ $video ? route('materi.videos.destroy', [$playlist, $video]) : route('materi.playlists.destroy', $playlist) }}" method="POST" class="mt-5">
        @csrf
        @method('DELETE')
        <div data-materi-form-errors role="alert" tabindex="-1" hidden class="mb-3 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800"></div>
        <label class="flex items-start gap-2"><input type="checkbox" name="confirm_delete" value="1" required class="mt-1"><span>Saya memahami akibat penghapusan ini.</span></label>
        <div class="mt-5 flex flex-wrap gap-2">
            <button type="submit" class="rf-btn-danger px-4">Hapus</button>
            <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary px-4">Batal</a>
        </div>
    </form>
</section>
