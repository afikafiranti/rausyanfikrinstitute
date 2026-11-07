@extends('layouts.app')

@section('page-content')
@php
    $totalRows = $posts->count();
    $isEditing = (bool) $editingPost;
    $restoring = $errors->any() && (string) old('_editing_id') === (string) ($editingPost?->id ?? '');
    $showEditor = $isEditing || $errors->any();
    $statusLabel = $editingPost?->published_at ? ($editingPost->published_at->isFuture() ? 'Terjadwal' : 'Terbit') : 'Draf';
@endphp

<div>
    <a href="#content-editor" data-editor-panel-toggle aria-controls="content-editor" aria-expanded="true"
        class="rf-btn mb-4 lg:hidden">{{ $showEditor ? 'Tutup' : 'Buat' }}</a>
    <div class="grid gap-4 lg:grid-cols-12">
        <section id="content-editor" data-content-editor data-editor-label="artikel" data-create-url="{{ route('management.post.store') }}" data-initial-id="{{ $editingPost?->id ?? '' }}" data-editor-current-image="{{ $editingPost?->public_cover_url ?? '' }}" data-has-errors="{{ $restoring && $errors->any() ? 'true' : 'false' }}"
            class="rf-card min-w-0 {{ $showEditor ? 'order-1' : 'order-2' }} lg:order-1 lg:col-span-4 lg:block">
            <div class="flex items-center justify-between mb-3">
                <h2 data-editor-heading tabindex="-1" class="text-lg font-semibold">{{ $isEditing ? 'Edit artikel' : 'Tambah artikel' }}</h2>
            </div>

            <div data-editor-errors>
                @include('management.feedback')
            </div>
            <form data-editor-form action="{{ $isEditing ? route('management.post.update', $editingPost) : route('management.post.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="_editing_id" value="{{ $editingPost?->id ?? '' }}">
                <input type="hidden" name="_method" value="PUT" @disabled(! $isEditing)>
                <div>
                    <label for="contentTitle" class="block text-sm mb-1">Judul</label>
                    <input type="text" name="title" id="contentTitle" @error('title') aria-invalid="true" aria-describedby="contentTitleError" @enderror required value="{{ $restoring ? old('title') : ($editingPost?->title ?? '') }}"
                        class="w-full min-h-11 rounded-lg border border-slate-300 px-3 py-2"
                        placeholder="Judul artikel">
                    @error('title') <p id="contentTitleError" data-editor-error class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contentType" class="block text-sm mb-1">Jenis artikel</label>
                    <select name="type" id="contentType" @error('type') aria-invalid="true" aria-describedby="contentTypeError" @enderror class="w-full min-h-11 rounded-lg border border-slate-300 px-3 py-2">
                        <option value="reflection" @selected(($restoring ? old('type') : ($editingPost?->type ?? 'reflection')) === 'reflection')>Refleksi</option>
                        <option value="article" @selected(($restoring ? old('type') : $editingPost?->type) === 'article')>Artikel</option>
                    </select>
                    @error('type') <p id="contentTypeError" data-editor-error class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contentStatus" class="block text-sm mb-1">Status publikasi</label>
                    <select name="publication_status" id="contentStatus" @error('publication_status') aria-invalid="true" aria-describedby="contentStatusError" @enderror class="w-full min-h-11 rounded-lg border border-slate-300 px-3 py-2">
                        <option value="" data-editor-preserve-status @selected($isEditing && (! $restoring || old('publication_status') === null || old('publication_status') === '')) @disabled(! $isEditing) @if(! $isEditing) hidden @endif>Pertahankan status saat ini</option>
                        <option value="published" @selected(($restoring ? old('publication_status') : ($isEditing ? '' : 'published')) === 'published')>Terbit</option>
                        <option value="draft" @selected(($restoring ? old('publication_status') : '') === 'draft')>Draf</option>
                    </select>
                    <p data-editor-status-hint class="mt-1 text-sm text-[#536173]" @if(! $isEditing) hidden @endif>Status saat ini: {{ $statusLabel }}</p>
                    @error('publication_status') <p id="contentStatusError" data-editor-error class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contentExcerpt" class="block text-sm mb-1">Ringkasan</label>
                    <textarea name="excerpt" id="contentExcerpt" @error('excerpt') aria-invalid="true" aria-describedby="contentExcerptError" @enderror rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                        placeholder="Ringkasan artikel">{{ $restoring ? old('excerpt') : ($editingPost?->excerpt ?? '') }}</textarea>
                    @error('excerpt') <p id="contentExcerptError" data-editor-error class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contentContent" class="block text-sm mb-1">Isi lengkap</label>
                    <textarea name="content" id="contentContent" @error('content') aria-invalid="true" aria-describedby="contentContentError" @enderror rows="6"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                        placeholder="Tulis isi artikel di sini...">{{ $restoring ? old('content') : ($editingPost?->content ?? '') }}</textarea>
                    @error('content') <p id="contentContentError" data-editor-error class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="contentCover" class="block text-sm mb-1">Gambar JPG/PNG (opsional, maks. 2 MB)</label>
                    <input type="file" name="cover_url" id="contentCover" @error('cover_url') aria-invalid="true" aria-describedby="contentCoverError" @enderror accept="image/jpeg,image/png"
                        class="min-w-0 w-full rounded-lg border border-slate-300 px-3 py-2">
                    <p data-editor-image-help class="mt-1 text-sm text-[#536173]">{{ $isEditing ? 'Kosongkan untuk mempertahankan gambar saat ini.' : 'Gambar boleh dikosongkan.' }}{{ $restoring ? ' Pilih kembali berkas gambar jika akan menggantinya.' : '' }}</p>
                    <img data-editor-preview src="{{ $editingPost?->public_cover_url ?? '' }}" alt="Pratinjau gambar artikel" class="mt-2 h-24 w-24 rounded-lg object-cover" @if(! $editingPost?->public_cover_url) hidden @endif>
                    <div data-editor-no-image class="mt-2 flex h-24 w-24 items-center justify-center rounded-lg bg-slate-100 px-1 text-center text-xs text-[#536173]" role="img" aria-label="Tanpa gambar" @if($editingPost?->public_cover_url) hidden @endif>Tanpa gambar</div>
                    @error('cover_url') <p id="contentCoverError" data-editor-error class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-2">
                    <a data-editor-cancel href="{{ route('management.post.index') }}" class="rf-btn-secondary" @if(! $isEditing) hidden @endif>Batal</a>
                    <button type="submit" class="rf-btn flex-1 justify-center">Simpan</button>
                </div>
            </form>
        </section>

        {{-- ================= DAFTAR BERITA ================= --}}
        <section class="rf-card min-w-0 order-1 lg:order-2 lg:col-span-8">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold">Daftar artikel</h2>
                <div class="text-sm text-slate-500">{{ $totalRows }} artikel</div>
            </div>

            <div class="space-y-3 lg:hidden">
                @forelse($posts as $post)
                    <article data-editor-item="{{ $post->id }}" class="rounded-lg border border-slate-200 p-4 {{ $editingPost?->id === $post->id ? 'bg-blue-50' : '' }}">
                        <div class="flex min-w-0 gap-3">
                            @if ($post->public_cover_url)
                                <img src="{{ $post->public_cover_url }}" alt="" class="h-16 w-16 shrink-0 rounded-lg object-cover">
                            @else
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-slate-100 px-1 text-center text-xs text-[#536173]" role="img" aria-label="Tanpa gambar">Tanpa gambar</div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="break-words font-semibold text-[#14213D]">{{ $post->title }}</h3>
                                <p class="mt-1 text-sm text-[#536173]">{{ $post->type === 'article' ? 'Artikel' : 'Refleksi' }} · {{ $post->published_at ? ($post->published_at->isFuture() ? 'Terjadwal' : 'Terbit') : 'Draf' }}</p>
                                <p class="text-sm text-[#536173]">{{ $post->published_at?->locale('id')->translatedFormat('d F Y') ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 border-t border-slate-200 pt-3">
                            <a href="{{ route('management.post.index', ['edit' => $post->id]) }}#content-editor" data-editor-select class="rf-btn-secondary p-2" aria-label="Edit artikel {{ $post->title }}" title="Edit artikel {{ $post->title }}" data-action-tooltip="Edit artikel {{ $post->title }}" @if($editingPost?->id === $post->id) aria-current="true" @endif data-id="{{ $post->id }}"
                                data-title="{{ $post->title }}" data-excerpt="{{ $post->excerpt }}" data-content="{{ $post->content }}"
                                data-type="{{ $post->type }}" data-image="{{ $post->public_cover_url }}" data-status-label="{{ $post->published_at ? ($post->published_at->isFuture() ? 'Terjadwal' : 'Terbit') : 'Draf' }}" data-update-url="{{ route('management.post.update', $post) }}"><i class="fas fa-pen" aria-hidden="true"></i></a>
                            <form action="{{ route('management.post.destroy', $post->id) }}" method="POST"
                                data-confirm-title="Hapus artikel" data-confirm-message="Artikel ini akan dihapus permanen." data-confirm-label="Hapus" data-confirm-danger>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rf-btn-danger p-2" aria-label="Hapus artikel {{ $post->title }}" title="Hapus artikel {{ $post->title }}" data-action-tooltip="Hapus artikel {{ $post->title }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="py-6 text-center text-[#536173]">Belum ada artikel.</p>
                @endforelse
            </div>
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-600 border-b">
                        <tr>
                            <th class="py-2 pr-4">Judul</th>
                            <th class="py-2 pr-4">Jenis</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Tanggal</th>
                            <th class="py-2 pr-4">Gambar</th>
                            <th class="py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($posts as $post)
                            <tr data-editor-item="{{ $post->id }}" class="{{ $editingPost?->id === $post->id ? 'bg-blue-50' : '' }}">
                                <td class="py-2 pr-4">{{ $post->title }}</td>
                                <td class="py-2 pr-4">{{ $post->type === 'article' ? 'Artikel' : 'Refleksi' }}</td>
                                <td class="py-2 pr-4">{{ $post->published_at ? ($post->published_at->isFuture() ? 'Terjadwal' : 'Terbit') : 'Draf' }}</td>
                                <td class="py-2 pr-4">{{ $post->published_at?->locale('id')->translatedFormat('d F Y') ?? '—' }}</td>
                                <td class="py-2 pr-4">
                                    @if ($post->public_cover_url)
                                        <img src="{{ $post->public_cover_url }}" alt="" class="h-12 w-12 rounded object-cover">
                                    @else
                                        <span class="text-sm text-[#536173]">Tanpa gambar</span>
                                    @endif
                                </td>
                                <td class="py-2">
                                    <div class="flex gap-3">
                                        <a href="{{ route('management.post.index', ['edit' => $post->id]) }}#content-editor"
                                            data-editor-select class="rf-btn-secondary p-2" aria-label="Edit artikel {{ $post->title }}" title="Edit artikel {{ $post->title }}" data-action-tooltip="Edit artikel {{ $post->title }}" @if($editingPost?->id === $post->id) aria-current="true" @endif
                                            data-id="{{ $post->id }}" data-title="{{ $post->title }}"
                                            data-excerpt="{{ $post->excerpt }}" data-content="{{ $post->content }}"
                                            data-type="{{ $post->type }}" data-image="{{ $post->public_cover_url }}" data-status-label="{{ $post->published_at ? ($post->published_at->isFuture() ? 'Terjadwal' : 'Terbit') : 'Draf' }}"
                                            data-update-url="{{ route('management.post.update', $post) }}">
                                            <i class="fas fa-pen" aria-hidden="true"></i>
                                        </a>

                                        <form action="{{ route('management.post.destroy', $post->id) }}" method="POST"
                                            data-confirm-title="Hapus artikel" data-confirm-message="Artikel ini akan dihapus permanen." data-confirm-label="Hapus" data-confirm-danger>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rf-btn-danger p-2" aria-label="Hapus artikel {{ $post->title }}" title="Hapus artikel {{ $post->title }}" data-action-tooltip="Hapus artikel {{ $post->title }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-500">Belum ada artikel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
        </section>
    </div>
</div>

@endsection
