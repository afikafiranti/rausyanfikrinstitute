@extends('layouts.app')

@section('page-content')
@include('management.feedback')
@php
    $totalRows = $posts->count();
@endphp

<div class="hidden md:block">
    <div class="grid grid-cols-12 gap-4">
        {{-- ================= FORM INPUT BERITA ================= --}}
        <section class="col-span-4 rounded-xl bg-white p-4 shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Input Refleksi</h3>
                <span class="text-sm text-gray-400">Tambah</span>
            </div>

            <form action="{{ route('management.post.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Judul</label>
                    <input type="text" name="title" required value="{{ old('_editing_id') ? '' : old('title') }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Judul refleksi ..">
                </div>

                <div>
                    <label class="block text-sm mb-1">Ringkasan</label>
                    <textarea name="excerpt" rows="3"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Isi singkat refleksi ..">{{ old('_editing_id') ? '' : old('excerpt') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm mb-1">Isi Refleksi Lengkap</label>
                    <textarea name="content" rows="6"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Tulis isi refleksi lengkap di sini...">{{ old('_editing_id') ? '' : old('content') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm mb-1">Gambar JPG/PNG (opsional, maks. 2 MB)</label>
                    <input type="file" name="cover_url" accept="image/jpeg,image/png"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                </div>

                <button type="submit"
                    class="bg-indigo-600 text-white rounded-lg px-4 py-2 w-full font-semibold hover:bg-indigo-700 transition">
                    Simpan Refleksi
                </button>
            </form>
        </section>

        {{-- ================= DAFTAR BERITA ================= --}}
        <section class="col-span-8 rounded-xl bg-white p-4 shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Daftar Refleksi</h3>
                <div class="text-sm text-slate-500">{{ $totalRows }} item</div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-left text-slate-600 border-b">
                        <tr>
                            <th class="py-2 pr-4">Judul</th>
                            <th class="py-2 pr-4">Tanggal</th>
                            <th class="py-2 pr-4">Gambar</th>
                            <th class="py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($posts as $post)
                            <tr>
                                <td class="py-2 pr-4">{{ $post->title }}</td>
                                <td class="py-2 pr-4">{{ $post->published_at }}</td>
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
                                            data-update-url="{{ route('management.post.update', $post) }}">
                                            Edit
                                        </button>

                                        <form action="{{ route('management.post.destroy', $post->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus refleksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500">Belum ada refleksi </td>
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
<div id="editModal" class="fixed inset-0 hidden bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
        <h2 class="text-lg font-semibold mb-3">Edit Refleksi</h2>
        @if ($errors->any() && old('_editing_id'))
            @include('management.feedback')
        @endif
        <form id="editForm" action="{{ old('_editing_id') ? route('management.post.update', old('_editing_id')) : '#' }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            @method('PUT')

            <input type="hidden" id="editId" name="_editing_id" value="{{ old('_editing_id') }}">

            <div>
                <label class="block text-sm mb-1">Judul</label>
                <input type="text" name="title" id="editTitle" required value="{{ old('_editing_id') ? old('title') : '' }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
            </div>

            <div>
                <label class="block text-sm mb-1">Ringkasan</label>
                <textarea name="excerpt" id="editExcerpt" rows="3"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">{{ old('_editing_id') ? old('excerpt') : '' }}</textarea>
            </div>

            <div>
                <label class="block text-sm mb-1">Isi Refleksi Lengkap</label>
                <textarea name="content" id="editContent" rows="6"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">{{ old('_editing_id') ? old('content') : '' }}</textarea>
            </div>

            <div>
                <label class="block text-sm mb-1">Gambar (opsional)</label>
                <input type="file" name="cover_url" accept="image/jpeg,image/png"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
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
    function openEditModal(button) {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editForm');

        document.getElementById('editId').value = button.dataset.id;
        document.getElementById('editTitle').value = button.dataset.title;
        document.getElementById('editExcerpt').value = button.dataset.excerpt;
        document.getElementById('editContent').value = button.dataset.content;

        form.action = button.dataset.updateUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    document.querySelectorAll('.edit-post-btn').forEach(button => {
        button.addEventListener('click', () => openEditModal(button));
    });
    @if ($errors->any() && old('_editing_id'))
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    @endif
</script>
@endsection
