@extends('layouts.app')

@section('page-content')
@include('management.feedback')
@php
    $totalRows = $posts->count();
@endphp

<div>
    <div class="grid gap-4 lg:grid-cols-12">
        {{-- ================= FORM INPUT BERITA ================= --}}
        <section class="min-w-0 rounded-xl bg-white p-4 shadow lg:col-span-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Input Artikel &amp; Refleksi</h3>
                <span class="text-sm text-gray-400">Tambah</span>
            </div>

            <form action="{{ route('management.post.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label for="createTitle" class="block text-sm mb-1">Judul</label>
                    <input type="text" name="title" id="createTitle" required value="{{ old('_editing_id') ? '' : old('title') }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Judul tulisan">
                </div>

                <div>
                    <label for="createType" class="block text-sm mb-1">Jenis tulisan</label>
                    <select name="type" id="createType" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        <option value="reflection" @selected(old('_editing_id') ? false : old('type', 'reflection') === 'reflection')>Refleksi</option>
                        <option value="article" @selected(! old('_editing_id') && old('type') === 'article')>Artikel</option>
                    </select>
                </div>

                <div>
                    <label for="createStatus" class="block text-sm mb-1">Status publikasi</label>
                    <select name="publication_status" id="createStatus" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        <option value="published" @selected(old('_editing_id') ? false : old('publication_status', 'published') === 'published')>Terbit</option>
                        <option value="draft" @selected(! old('_editing_id') && old('publication_status') === 'draft')>Draf</option>
                    </select>
                </div>

                <div>
                    <label for="createExcerpt" class="block text-sm mb-1">Ringkasan</label>
                    <textarea name="excerpt" id="createExcerpt" rows="3"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Isi singkat tulisan">{{ old('_editing_id') ? '' : old('excerpt') }}</textarea>
                </div>

                <div>
                    <label for="createContent" class="block text-sm mb-1">Isi lengkap</label>
                    <textarea name="content" id="createContent" rows="6"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Tulis isi lengkap di sini...">{{ old('_editing_id') ? '' : old('content') }}</textarea>
                </div>

                <div>
                    <label for="createCover" class="block text-sm mb-1">Gambar JPG/PNG (opsional, maks. 2 MB)</label>
                    <input type="file" name="cover_url" id="createCover" accept="image/jpeg,image/png"
                        class="min-w-0 w-full rounded-lg border border-blueGray-200 px-3 py-2">
                </div>

                <button type="submit"
                    class="bg-indigo-600 text-white rounded-lg px-4 py-2 w-full font-semibold hover:bg-indigo-700 transition">
                    Simpan tulisan
                </button>
            </form>
        </section>

        {{-- ================= DAFTAR BERITA ================= --}}
        <section class="min-w-0 rounded-xl bg-white p-4 shadow lg:col-span-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Daftar Artikel &amp; Refleksi</h3>
                <div class="text-sm text-slate-500">{{ $totalRows }} item</div>
            </div>

            <div class="overflow-x-auto">
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
                            <tr>
                                <td class="py-2 pr-4">{{ $post->title }}</td>
                                <td class="py-2 pr-4">{{ $post->type === 'article' ? 'Artikel' : 'Refleksi' }}</td>
                                <td class="py-2 pr-4">{{ $post->published_at ? ($post->published_at->isFuture() ? 'Terjadwal' : 'Terbit') : 'Draf' }}</td>
                                <td class="py-2 pr-4">{{ $post->published_at?->locale('id')->translatedFormat('d F Y') ?? '—' }}</td>
                                <td class="py-2 pr-4">
                                    @php
                                        $imgSrc = Str::startsWith($post->cover_url, ['http', 'https'])
                                            ? $post->cover_url
                                            : ($post->cover_url ? asset('storage/'.$post->cover_url) : asset('assets/img/default-img.jpg'));
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $post->title }}" class="w-12 h-12 object-cover rounded">
                                </td>
                                <td class="py-2">
                                    <div class="flex gap-3">
                                        <button
                                            class="edit-post-btn text-indigo-600 hover:underline"
                                            data-id="{{ $post->id }}" data-title="{{ $post->title }}"
                                            data-excerpt="{{ $post->excerpt }}" data-content="{{ $post->content }}"
                                            data-type="{{ $post->type }}"
                                            data-update-url="{{ route('management.post.update', $post) }}">
                                            Edit
                                        </button>

                                        <form action="{{ route('management.post.destroy', $post->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus tulisan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-slate-500">Belum ada artikel atau refleksi.</td>
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

{{-- ===================== MODAL EDIT ===================== --}}
<div id="editModal" role="dialog" aria-modal="true" aria-labelledby="editPostHeading" aria-hidden="true" class="fixed inset-0 hidden bg-black bg-opacity-50 items-center justify-center z-50 p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg max-h-full overflow-y-auto">
        <h2 id="editPostHeading" class="text-lg font-semibold mb-3">Edit Artikel &amp; Refleksi</h2>
        @if ($errors->any() && old('_editing_id'))
            @include('management.feedback')
        @endif
        <form id="editForm" action="{{ old('_editing_id') ? route('management.post.update', old('_editing_id')) : '#' }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')

            <input type="hidden" id="editId" name="_editing_id" value="{{ old('_editing_id') }}">

            <div>
                <label for="editTitle" class="block text-sm mb-1">Judul</label>
                <input type="text" name="title" id="editTitle" required value="{{ old('_editing_id') ? old('title') : '' }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
            </div>

            <div>
                <label for="editType" class="block text-sm mb-1">Jenis tulisan</label>
                <select name="type" id="editType" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    <option value="reflection" @selected(old('_editing_id') && old('type', 'reflection') === 'reflection')>Refleksi</option>
                    <option value="article" @selected(old('_editing_id') && old('type') === 'article')>Artikel</option>
                </select>
            </div>

            <div>
                <label for="editStatus" class="block text-sm mb-1">Status publikasi</label>
                <select name="publication_status" id="editStatus" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    <option value="" @selected(! old('_editing_id') || old('publication_status') === null || old('publication_status') === '')>Pertahankan status saat ini</option>
                    <option value="published" @selected(old('_editing_id') && old('publication_status') === 'published')>Terbit</option>
                    <option value="draft" @selected(old('_editing_id') && old('publication_status') === 'draft')>Draf</option>
                </select>
            </div>

            <div>
                <label for="editExcerpt" class="block text-sm mb-1">Ringkasan</label>
                <textarea name="excerpt" id="editExcerpt" rows="3"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">{{ old('_editing_id') ? old('excerpt') : '' }}</textarea>
            </div>

            <div>
                <label for="editContent" class="block text-sm mb-1">Isi lengkap</label>
                <textarea name="content" id="editContent" rows="6"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">{{ old('_editing_id') ? old('content') : '' }}</textarea>
            </div>

            <div>
                <label for="editCover" class="block text-sm mb-1">Gambar JPG/PNG (opsional, maks. 2 MB)</label>
                <input type="file" name="cover_url" id="editCover" accept="image/jpeg,image/png"
                    class="min-w-0 w-full rounded-lg border border-blueGray-200 px-3 py-2">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== SCRIPT ===================== --}}
<script>
    let editPostTrigger;

    function openEditModal(button) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        document.getElementById('editId').value = button.dataset.id;
        document.getElementById('editTitle').value = button.dataset.title;
        document.getElementById('editExcerpt').value = button.dataset.excerpt;
        document.getElementById('editContent').value = button.dataset.content;
        document.getElementById('editType').value = button.dataset.type;
        document.getElementById('editStatus').value = '';

        form.action = button.dataset.updateUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        editPostTrigger = button;
        document.getElementById('editTitle').focus();
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        editPostTrigger?.focus();
    }

    document.querySelectorAll('.edit-post-btn').forEach(button => {
        button.addEventListener('click', () => openEditModal(button));
    });
    document.getElementById('editModal').addEventListener('keydown', event => {
        if (event.key === 'Escape') closeEditModal();
        if (event.key !== 'Tab') return;
        const focusable = [...document.querySelectorAll('#editModal input:not([type="hidden"]), #editModal select, #editModal textarea, #editModal button')];
        if (event.shiftKey && document.activeElement === focusable[0]) {
            event.preventDefault();
            focusable.at(-1).focus();
        } else if (!event.shiftKey && document.activeElement === focusable.at(-1)) {
            event.preventDefault();
            focusable[0].focus();
        }
    });
    @if ($errors->any() && old('_editing_id'))
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
        document.getElementById('editModal').setAttribute('aria-hidden', 'false');
        document.getElementById('editTitle').focus();
    @endif
</script>
@endsection
