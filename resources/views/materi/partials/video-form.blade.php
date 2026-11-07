<section data-materi-modal-content class="bg-white p-5 sm:p-7" aria-labelledby="materi-modal-heading">
    <header class="flex items-start justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <p class="break-words text-sm font-medium text-brand">{{ $playlist->title }}</p>
            <h1 id="materi-modal-heading" tabindex="-1" class="mt-1 text-2xl font-semibold text-ink">{{ $video->exists ? 'Edit video' : 'Buat video' }}</h1>
        </div>
        <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary shrink-0 px-3">Tutup</a>
    </header>
    @include('materi.feedback')
    @php $useOld = session('materi_form') === $formKey; @endphp
    <form data-materi-form action="{{ $video->exists ? route('materi.videos.update', [$playlist, $video]) : route('materi.videos.store', $playlist) }}" method="POST" class="mt-5 grid gap-4 sm:grid-cols-2">
        @csrf
        @if ($video->exists) @method('PATCH') @endif
        <div data-materi-form-errors role="alert" tabindex="-1" hidden class="rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800 sm:col-span-2"></div>
        <div class="sm:col-span-2">
            <label for="video_title" class="block font-medium">Judul video *</label>
            <input id="video_title" name="title" value="{{ $useOld ? old('title', $video->title) : $video->title }}" required maxlength="180" class="mt-2 w-full">
        </div>
        <div class="sm:col-span-2">
            <label for="youtube_video_id" class="block font-medium">ID video YouTube *</label>
            <input id="youtube_video_id" name="youtube_video_id" value="{{ $useOld ? old('youtube_video_id', $video->youtube_video_id) : $video->youtube_video_id }}" required minlength="11" maxlength="11" pattern="[A-Za-z0-9_-]{11}" aria-describedby="video-id-help" class="mt-2 w-full">
            <p id="video-id-help" class="mt-1 text-sm text-muted">Salin 11 karakter setelah <code>v=</code> dari tautan video YouTube.</p>
        </div>
        <div>
            <label for="duration_sec" class="block font-medium">Durasi (detik)</label>
            <input id="duration_sec" name="duration_sec" type="number" value="{{ $useOld ? old('duration_sec', $video->duration_sec) : $video->duration_sec }}" min="0" max="4294967295" class="mt-2 w-full">
        </div>
        <div>
            <label for="video_order_index" class="block font-medium">Urutan</label>
            <input id="video_order_index" name="order_index" type="number" value="{{ $useOld ? old('order_index', $video->order_index ?? 0) : ($video->order_index ?? 0) }}" min="0" max="2147483647" class="mt-2 w-full">
        </div>
        <div class="flex flex-wrap gap-2 sm:col-span-2">
            <button type="submit" class="rf-btn px-4">Simpan</button>
            <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary px-4">Tutup</a>
        </div>
    </form>
</section>
