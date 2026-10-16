@extends('layouts.app')

@section('page-content')
<div class="mx-auto max-w-5xl space-y-6 p-4">
    <div><a href="{{ route('materi.manage') }}" class="text-indigo-700 underline">Kembali ke Kelola Materi</a><h1 class="mt-2 text-2xl font-semibold">{{ $playlist->title }}</h1></div>
    @if (session('success'))<p role="status" class="rounded bg-green-50 p-3 text-green-800">{{ session('success') }}</p>@endif
    @if ($errors->any())
        <div role="alert" class="rounded bg-red-50 p-3 text-red-800"><p>Data belum tersimpan.</p><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-semibold">Edit playlist</h2>
        <form action="{{ route('materi.playlists.update', $playlist) }}" method="POST" class="grid gap-4 md:grid-cols-2">
            @csrf @method('PATCH')
            <div><label for="title" class="block text-sm">Judul</label><input id="title" name="title" value="{{ session('materi_form') === 'playlist' ? old('title', $playlist->title) : $playlist->title }}" maxlength="150" required class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="youtube_playlist_id" class="block text-sm">ID playlist YouTube</label><input id="youtube_playlist_id" name="youtube_playlist_id" value="{{ session('materi_form') === 'playlist' ? old('youtube_playlist_id', $playlist->youtube_playlist_id) : $playlist->youtube_playlist_id }}" maxlength="64" required class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="level_id" class="block text-sm">Level materi</label><select id="level_id" name="level_id" required class="mt-1 w-full rounded border-slate-300">@foreach ($levels as $level)<option value="{{ $level->id }}" @selected((session('materi_form') === 'playlist' ? old('level_id', $playlist->level_id) : $playlist->level_id) == $level->id) @disabled(!$level->is_active)>{{ $level->description ?: $level->name }}{{ $level->is_active ? '' : ' (nonaktif)' }}</option>@endforeach</select><p class="mt-1 text-xs text-slate-600">Pilih level aktif untuk menyimpan playlist.</p></div>
            <div><label for="order_index" class="block text-sm">Urutan</label><input id="order_index" type="number" name="order_index" value="{{ session('materi_form') === 'playlist' ? old('order_index', $playlist->order_index) : $playlist->order_index }}" min="0" max="2147483647" class="mt-1 w-full rounded border-slate-300"></div>
            <div class="md:col-span-2"><label for="description" class="block text-sm">Deskripsi</label><textarea id="description" name="description" maxlength="10000" rows="3" class="mt-1 w-full rounded border-slate-300">{{ session('materi_form') === 'playlist' ? old('description', $playlist->description) : $playlist->description }}</textarea></div>
            <div class="md:col-span-2"><input type="hidden" name="is_active" value="0"><label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked((session('materi_form') === 'playlist' ? old('is_active', $playlist->is_active) : $playlist->is_active) == '1')> Publikasikan untuk alumni yang memenuhi level</label></div>
            <button type="submit" class="rf-btn md:col-span-2">Simpan playlist</button>
        </form>
        <form action="{{ route('materi.playlists.destroy', $playlist) }}" method="POST" class="mt-6 border-t pt-4">
            @csrf @method('DELETE')
            <p class="mb-2 text-sm text-red-800">Menghapus playlist juga menghapus semua video dan progres menonton terkait.</p>
            <label class="inline-flex items-center gap-2"><input type="checkbox" name="confirm_delete" value="1" required> Saya memahami dan ingin menghapus playlist ini</label>
            <button type="submit" class="ml-3 text-red-700 underline">Hapus playlist</button>
        </form>
    </section>

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-semibold">Tambah video</h2>
        <form action="{{ route('materi.videos.store', $playlist) }}" method="POST" class="grid gap-4 md:grid-cols-2">
            @csrf
            <div><label for="new_title" class="block text-sm">Judul video</label><input id="new_title" name="title" value="{{ session('materi_form') === 'video-new' ? old('title') : '' }}" maxlength="180" required class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="new_youtube_video_id" class="block text-sm">ID video YouTube</label><input id="new_youtube_video_id" name="youtube_video_id" value="{{ session('materi_form') === 'video-new' ? old('youtube_video_id') : '' }}" minlength="11" maxlength="11" required class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="new_duration_sec" class="block text-sm">Durasi (detik, opsional)</label><input id="new_duration_sec" type="number" name="duration_sec" value="{{ session('materi_form') === 'video-new' ? old('duration_sec') : '' }}" min="0" max="4294967295" class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="new_order_index" class="block text-sm">Urutan</label><input id="new_order_index" type="number" name="order_index" value="{{ session('materi_form') === 'video-new' ? old('order_index', 0) : 0 }}" min="0" max="2147483647" class="mt-1 w-full rounded border-slate-300"></div>
            <button type="submit" class="rf-btn md:col-span-2">Tambah video</button>
        </form>
    </section>

    <section class="space-y-4"><h2 class="text-lg font-semibold">Video dalam playlist</h2>
        @forelse ($playlist->videos as $video)
            <div class="rounded-xl bg-white p-5 shadow">
                <form action="{{ route('materi.videos.update', [$playlist, $video]) }}" method="POST" class="grid gap-4 md:grid-cols-2">
                    @csrf @method('PATCH')
                    <div><label for="video_title_{{ $video->id }}" class="block text-sm">Judul video</label><input id="video_title_{{ $video->id }}" name="title" value="{{ session('materi_form') === 'video-'.$video->id ? old('title', $video->title) : $video->title }}" maxlength="180" required class="mt-1 w-full rounded border-slate-300"></div>
                    <div><label for="video_id_{{ $video->id }}" class="block text-sm">ID video YouTube</label><input id="video_id_{{ $video->id }}" name="youtube_video_id" value="{{ session('materi_form') === 'video-'.$video->id ? old('youtube_video_id', $video->youtube_video_id) : $video->youtube_video_id }}" minlength="11" maxlength="11" required class="mt-1 w-full rounded border-slate-300"></div>
                    <div><label for="video_duration_{{ $video->id }}" class="block text-sm">Durasi (detik, opsional)</label><input id="video_duration_{{ $video->id }}" type="number" name="duration_sec" value="{{ session('materi_form') === 'video-'.$video->id ? old('duration_sec', $video->duration_sec) : $video->duration_sec }}" min="0" max="4294967295" class="mt-1 w-full rounded border-slate-300"></div>
                    <div><label for="video_order_{{ $video->id }}" class="block text-sm">Urutan</label><input id="video_order_{{ $video->id }}" type="number" name="order_index" value="{{ session('materi_form') === 'video-'.$video->id ? old('order_index', $video->order_index) : $video->order_index }}" min="0" max="2147483647" class="mt-1 w-full rounded border-slate-300"></div>
                    <button type="submit" class="rf-btn md:col-span-2">Simpan video</button>
                </form>
                <form action="{{ route('materi.videos.destroy', [$playlist, $video]) }}" method="POST" class="mt-3">@csrf @method('DELETE')<label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="confirm_delete" value="1" required> Hapus video dan progres terkait</label><button type="submit" class="ml-3 text-red-700 underline">Hapus video {{ $video->title }}</button></form>
            </div>
        @empty
            <p class="rounded bg-white p-5 text-slate-600">Belum ada video pada playlist ini.</p>
        @endforelse
    </section>
</div>
@endsection
