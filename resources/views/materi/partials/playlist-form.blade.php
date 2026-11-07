<section data-materi-modal-content class="bg-white p-5 sm:p-7" aria-labelledby="materi-modal-heading">
    <header class="flex items-start justify-between gap-3 border-b border-slate-200 pb-4">
        <div>
            <p class="text-sm font-medium text-brand">Playlist materi</p>
            <h1 id="materi-modal-heading" tabindex="-1" class="mt-1 break-words text-2xl font-semibold text-ink">{{ $playlist->exists ? 'Edit playlist' : 'Buat playlist' }}</h1>
        </div>
        <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary shrink-0 px-3">Tutup</a>
    </header>
    @include('materi.feedback')
    @php
        $hasActiveLevels = $levels->contains(fn ($level) => $level->is_active);
        $useOld = session('materi_form') === $formKey;
    @endphp
    @if (! $hasActiveLevels)
        <p class="mt-5 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900">Belum ada level aktif. Aktifkan satu level sebelum menyimpan playlist.</p>
    @endif
    <form data-materi-form action="{{ $playlist->exists ? route('materi.playlists.update', $playlist) : route('materi.playlists.store') }}" method="POST" class="mt-5 grid gap-4 sm:grid-cols-2">
        @csrf
        @if ($playlist->exists) @method('PATCH') @endif
        <div data-materi-form-errors role="alert" tabindex="-1" hidden class="rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800 sm:col-span-2"></div>
        <div class="sm:col-span-2">
            <label for="title" class="block font-medium">Judul *</label>
            <input id="title" name="title" value="{{ $useOld ? old('title', $playlist->title) : $playlist->title }}" required maxlength="150" class="mt-2 w-full">
        </div>
        <div class="sm:col-span-2">
            <label for="youtube_playlist_id" class="block font-medium">ID playlist YouTube *</label>
            <input id="youtube_playlist_id" name="youtube_playlist_id" value="{{ $useOld ? old('youtube_playlist_id', $playlist->youtube_playlist_id) : $playlist->youtube_playlist_id }}" required maxlength="64" pattern="[A-Za-z0-9_-]+" aria-describedby="playlist-id-help" class="mt-2 w-full">
            <p id="playlist-id-help" class="mt-1 text-sm text-muted">Salin bagian setelah <code>list=</code> dari tautan playlist YouTube.</p>
        </div>
        <div>
            <label for="level_id" class="block font-medium">Level *</label>
            <select id="level_id" name="level_id" required class="mt-2 w-full">
                <option value="">Pilih level</option>
                @foreach ($levels as $level)
                <option value="{{ $level->id }}" @selected((string) ($useOld ? old('level_id', $playlist->level_id) : $playlist->level_id) === (string) $level->id) @disabled(! $level->is_active)>{{ $level->description ?: $level->name }}{{ $level->is_active ? '' : ' (nonaktif)' }}</option>
                @endforeach
            </select>
            @if ($playlist->exists && ! $playlist->level?->is_active)
                <p class="mt-1 text-sm text-amber-800">Level saat ini nonaktif. Pilih level aktif untuk menyimpan.</p>
            @endif
        </div>
        <div>
            <label for="order_index" class="block font-medium">Urutan</label>
            <input id="order_index" name="order_index" type="number" value="{{ $useOld ? old('order_index', $playlist->order_index ?? 0) : ($playlist->order_index ?? 0) }}" min="0" max="2147483647" class="mt-2 w-full">
        </div>
        <div class="sm:col-span-2">
            <label for="description" class="block font-medium">Deskripsi</label>
            <textarea id="description" name="description" rows="4" maxlength="10000" class="mt-2 w-full">{{ $useOld ? old('description', $playlist->description) : $playlist->description }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <input type="hidden" name="is_active" value="0">
            <label class="flex items-start gap-2"><input type="checkbox" name="is_active" value="1" @checked(($useOld ? old('is_active', $playlist->is_active) : $playlist->is_active) == '1') class="mt-1"><span>Aktifkan untuk alumni sesuai level</span></label>
        </div>
        <div class="flex flex-wrap gap-2 sm:col-span-2">
            <button type="submit" class="rf-btn px-4" @disabled(! $hasActiveLevels)>Simpan</button>
            <a href="{{ route('materi.index') }}" data-materi-close class="rf-btn-secondary px-4">Tutup</a>
        </div>
    </form>
</section>
