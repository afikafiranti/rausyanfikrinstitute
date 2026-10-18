@extends('layouts.app')

@section('page-content')
    @include('management.feedback')
    @php
        $totalRows = $galleries->count();
    @endphp
    <div>
        <div class="grid gap-4 lg:grid-cols-12">
            <section class="rf-card min-w-0 lg:col-span-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold">Tambah foto galeri</h2>
                </div>

                <form action="{{ route('management.galeri.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-3">
                    @csrf
                    <div>
                        <label for="createGalleryTitle" class="block text-sm mb-1">Judul</label>
                        <input type="text" name="title" id="createGalleryTitle" required value="{{ old('_editing_id') ? '' : old('title') }}"
                            class="w-full min-h-11 rounded-lg border border-slate-300 px-3 py-2" placeholder="Judul foto galeri">
                    </div>

                    <div>
                        <label for="createGalleryCaption" class="block text-sm mb-1">Caption</label>
                        <textarea name="caption" id="createGalleryCaption" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2"
                            placeholder="Isi Caption Foto...">{{ old('_editing_id') ? '' : old('caption') }}</textarea>
                    </div>

                    <div>
                        <label for="createGalleryImage" class="block text-sm mb-1">Gambar JPG/PNG (maks. 2 MB)</label>
                        <input type="file" name="image" id="createGalleryImage" accept="image/jpeg,image/png" required
                            class="min-w-0 w-full rounded-lg border border-slate-300 px-3 py-2">
                    </div>

                    <button type="submit"
                        class="rf-btn w-full justify-center">
                        Tambah Galeri
                    </button>
                </form>
            </section>
            <section class="rf-card min-w-0 lg:col-span-8">
                {{-- Grid Galeri --}}
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-semibold">Galeri</h2>
                    <div class="text-sm text-slate-500">{{ $totalRows }} item</div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                    @foreach ($galleries as $gallery)
                        <article class="min-w-0 overflow-hidden rounded-lg border border-slate-200 bg-white">
                            @if ($gallery->public_image_url)
                                <img src="{{ $gallery->public_image_url }}" alt="{{ $gallery->title }}" class="h-48 w-full object-cover">
                            @else
                                <div class="flex h-48 w-full items-center justify-center bg-slate-100 text-sm text-[#536173]" role="img" aria-label="Tanpa gambar">Tanpa gambar</div>
                            @endif
                            <h3 class="mt-2 break-words text-lg font-semibold px-4">{{ $gallery->title }}</h3>
                            <p class="break-words text-sm text-[#536173] px-4">{{ Str::limit($gallery->caption, 80) }}</p>
                            <div class="mt-3 flex flex-wrap justify-between gap-2 border-t border-slate-200 p-4">
                                <button data-id="{{ $gallery->id }}" data-title="{{ $gallery->title }}"
                                    data-caption="{{ $gallery->caption }}"
                                    data-image="{{ $gallery->public_image_url }}"
                                    data-update-url="{{ route('management.galeri.update', $gallery) }}"
                                    type="button" class="edit-btn rf-btn-secondary">
                                    Edit
                                </button>
                                <form action="{{ route('management.galeri.destroy', $gallery->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus galeri ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rf-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $galleries->links() }}
                </div>
            </section>
        </div>

        {{-- ================= Modal Edit ================= --}}
        <div id="editModal" role="dialog" aria-modal="true" aria-labelledby="editGalleryHeading" aria-hidden="true" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md max-h-full overflow-y-auto p-6 relative">
                <h2 id="editGalleryHeading" class="text-xl font-semibold mb-4">Edit Galeri</h2>
                @if ($errors->any() && old('_editing_id'))
                    @include('management.feedback')
                @endif
                <form id="editForm" action="{{ old('_editing_id') ? route('management.galeri.update', old('_editing_id')) : '#' }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_editing_id" id="editId" value="{{ old('_editing_id') }}">
                    <div class="mb-3">
                        <label for="editTitle" class="block text-sm font-medium text-gray-700">Judul</label>
                        <input type="text" name="title" id="editTitle" required value="{{ old('_editing_id') ? old('title') : '' }}" class="w-full min-h-11 rounded-lg border border-slate-300 px-3 py-2">
                    </div>

                    <div class="mb-3">
                        <label for="editCaption" class="block text-sm font-medium text-gray-700">Caption</label>
                        <textarea name="caption" id="editCaption" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('_editing_id') ? old('caption') : '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editGalleryImage" class="block text-sm font-medium text-gray-700">Ganti Gambar (opsional)</label>
                        <input type="file" name="image" id="editGalleryImage" accept="image/jpeg,image/png" class="min-w-0 w-full rounded-lg border border-slate-300 px-3 py-2">
                        <img id="previewImage" alt="Pratinjau gambar galeri" class="mt-2 hidden h-40 w-full rounded-lg object-cover">
                        <p id="previewUnavailable" class="mt-2 hidden rounded-lg bg-slate-100 p-4 text-center text-sm text-[#536173]">Tanpa gambar</p>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" id="closeModal"
                            class="rf-btn-secondary">Batal</button>
                        <button type="submit"
                            class="rf-btn">Simpan</button>
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
                const previewUnavailable = document.getElementById('previewUnavailable');
                let editTrigger;

                function closeEditModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modal.setAttribute('aria-hidden', 'true');
                    editTrigger?.focus();
                }

                editBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.getElementById('editId').value = btn.dataset.id;
                        titleField.value = btn.dataset.title;
                        captionField.value = btn.dataset.caption;
                        if (btn.dataset.image) {
                            previewImage.src = btn.dataset.image;
                            previewImage.classList.remove('hidden');
                            previewUnavailable.classList.add('hidden');
                        } else {
                            previewImage.removeAttribute('src');
                            previewImage.classList.add('hidden');
                            previewUnavailable.classList.remove('hidden');
                        }
                        editForm.action = btn.dataset.updateUrl;
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                        editTrigger = btn;
                        titleField.focus();
                    });
                });

                closeModal.addEventListener('click', closeEditModal);
                modal.addEventListener('keydown', event => {
                    if (event.key === 'Escape') closeEditModal();
                    if (event.key !== 'Tab') return;
                    const focusable = [...modal.querySelectorAll('input:not([type="hidden"]), textarea, button')];
                    if (event.shiftKey && document.activeElement === focusable[0]) {
                        event.preventDefault();
                        focusable.at(-1).focus();
                    } else if (!event.shiftKey && document.activeElement === focusable.at(-1)) {
                        event.preventDefault();
                        focusable[0].focus();
                    }
                });
                @if ($errors->any() && old('_editing_id'))
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    modal.setAttribute('aria-hidden', 'false');
                    titleField.focus();
                @endif
            });
        </script>
    </div>
    @endsection
