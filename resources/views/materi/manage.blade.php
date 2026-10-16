@extends('layouts.app')

@section('page-content')
<div class="mx-auto max-w-5xl space-y-6 p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold text-slate-900">Kelola Materi</h1>
        <a class="text-indigo-700 underline" href="{{ route('materi.index') }}">Kembali ke daftar materi</a>
    </div>

    @if (session('success'))<p role="status" class="rounded bg-green-50 p-3 text-green-800">{{ session('success') }}</p>@endif
    @if ($errors->any())
        <div role="alert" class="rounded bg-red-50 p-3 text-red-800">
            <p>Playlist belum tersimpan.</p>
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-semibold">Tambah playlist</h2>
        <form action="{{ route('materi.playlists.store') }}" method="POST" class="grid gap-4 md:grid-cols-2">
            @csrf
            <div><label for="title" class="block text-sm">Judul</label><input id="title" name="title" value="{{ old('title') }}" maxlength="150" required class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="youtube_playlist_id" class="block text-sm">ID playlist YouTube</label><input id="youtube_playlist_id" name="youtube_playlist_id" value="{{ old('youtube_playlist_id') }}" maxlength="64" required class="mt-1 w-full rounded border-slate-300"></div>
            <div><label for="level_id" class="block text-sm">Level materi</label><select id="level_id" name="level_id" required class="mt-1 w-full rounded border-slate-300"><option value="">Pilih level</option>@foreach ($levels as $level)<option value="{{ $level->id }}" @selected(old('level_id') == $level->id)>{{ $level->description ?: $level->name }}</option>@endforeach</select></div>
            <div><label for="order_index" class="block text-sm">Urutan</label><input id="order_index" type="number" name="order_index" value="{{ old('order_index', 0) }}" min="0" max="2147483647" class="mt-1 w-full rounded border-slate-300"></div>
            <div class="md:col-span-2"><label for="description" class="block text-sm">Deskripsi</label><textarea id="description" name="description" maxlength="10000" rows="3" class="mt-1 w-full rounded border-slate-300">{{ old('description') }}</textarea></div>
            <div class="md:col-span-2"><input type="hidden" name="is_active" value="0"><label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active') == '1')> Publikasikan untuk alumni yang memenuhi level</label></div>
            <button type="submit" class="rf-btn md:col-span-2">Tambah playlist</button>
        </form>
    </section>

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="mb-4 text-lg font-semibold">Daftar playlist</h2>
        <div class="space-y-3">
            @forelse ($playlists as $playlist)
                <div class="flex flex-wrap items-center justify-between gap-2 border-b pb-3">
                    <div><strong>{{ $playlist->title }}</strong><p class="text-sm text-slate-600">{{ $playlist->level?->description ?: $playlist->level?->name }} · {{ $playlist->is_active ? 'Publik' : 'Draft' }} · {{ $playlist->videos_count }} video</p></div>
                    <a href="{{ route('materi.playlists.edit', $playlist) }}" class="text-indigo-700 underline">Edit playlist dan video</a>
                </div>
            @empty
                <p class="text-slate-600">Belum ada playlist.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $playlists->links() }}</div>
    </section>
</div>
@endsection
