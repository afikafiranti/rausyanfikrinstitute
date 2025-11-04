@extends('layouts.app')

@section('page-content')
    @php
        $totalRows = $galleries->count();
    @endphp
    <div class="hidden md:block">
        <div class="grid grid-cols-12 gap-4">
            <section class="col-span-4 rounded-xl bg-white p-4 shadow">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Input Galeri</h3>
                    <span class="text-sm text-gray-400">Tambah</span>
                </div>

                <form action="{{ route('management.galeri.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1">Judul</label>
                        <input type="text" name="title" required
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Judul berita...">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Caption</label>
                        <textarea name="caption" rows="3" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                            placeholder="Isi Caption Foto..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Gambar (opsional)</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>

                    <button type="submit"
                        class="bg-indigo-600 text-white rounded-lg px-4 py-2 w-full font-semibold hover:bg-indigo-700 transition">
                        Tambah Galeri
                    </button>
                </form>
            </section>
            <section class="col-span-8 rounded-xl bg-white p-4 shadow">
                {{-- Grid Galeri --}}
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold">Galeri</h3>
                    <div class="text-sm text-slate-500">{{ $totalRows }} item</div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach ($galleries as $gallery)
                        <div class="bg-white rounded-lg shadow ">
                            @php
                                        $imgSrc = Str::startsWith($gallery->image_url, ['http', 'https'])
                                            ? $gallery->image_url
                                            : ($gallery->image_url ? asset('storage/'.$gallery->image_url) : asset('assets/img/default-img.jpg'));
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="{{ $gallery->title }}"  class="w-full h-48 object-cover rounded">
                               
                            <h3 class="mt-2 text-lg font-semibold px-4">{{ $gallery->title }}</h3>
                            <p class="text-sm text-gray-600 px-4">{{ Str::limit($gallery->caption, 80) }}</p>
                            <div class="flex justify-between mt-3 p-4 ">
                                <button data-id="{{ $gallery->id }}" data-title="{{ $gallery->title }}"
                                    data-caption="{{ $gallery->caption }}"
                                    data-image="{{ asset('storage/' . $gallery->image_url) }}"
                                    class="edit-btn text-blue-500 hover:underline">
                                    Edit
                                </button>
                                <form action="{{ route('management.galeri.destroy', $gallery->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus galeri ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $galleries->links() }}
                </div>
        </div>
        </section>

        {{-- ================= Modal Edit ================= --}}
        <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
                <h2 class="text-xl font-semibold mb-4">Edit Galeri</h2>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Judul</label>
                        <input type="text" name="title" id="editTitle" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Caption</label>
                        <textarea name="caption" id="editCaption" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Ganti Gambar (opsional)</label>
                        <input type="file" name="image" class="w-full border rounded px-3 py-2">
                        <img id="previewImage" src="" alt="Preview" class="mt-2 w-full h-40 object-cover rounded">
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" id="closeModal"
                            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-emerald-500 text-white rounded hover:bg-emerald-600">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= Script Modal ================= --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('editModal');
                const editBtns = document.querySelectorAll('.edit-btn');
                const closeModal = document.getElementById('closeModal');
                const editForm = document.getElementById('editForm');
                const titleField = document.getElementById('editTitle');
                const captionField = document.getElementById('editCaption');
                const previewImage = document.getElementById('previewImage');

                editBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.dataset.id;
                        titleField.value = btn.dataset.title;
                        captionField.value = btn.dataset.caption;
                        previewImage.src = btn.dataset.image;
                        editForm.action = `/management/galeri/${id}`;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                });

                closeModal.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                });
            });
        </script>
    @endsection
