@extends('layouts.app')

@section('page-content')
    @php
        $totalRows = $galleries->count();
        $editingId = $editingGallery?->id;
        $restoring = $errors->any() && (string) old('_editing_id') === (string) ($editingId ?? '');
        $panelOpen = $editingGallery || $errors->any();
    @endphp
    <div>
        <a href="#content-editor" class="rf-btn mb-4 lg:hidden" data-editor-panel-toggle aria-controls="content-editor" aria-expanded="true">{{ $panelOpen ? 'Tutup' : 'Buat' }}</a>

        <div class="grid gap-4 lg:grid-cols-12">
            <section id="content-editor" data-content-editor data-editor-label="galeri"
                data-create-url="{{ route('management.galeri.store') }}" data-initial-id="{{ $editingId ?? '' }}"
                data-editor-current-image="{{ $editingGallery?->public_image_url ?? '' }}"
                data-has-errors="{{ $restoring ? 'true' : 'false' }}"
                class="rf-card min-w-0 {{ $panelOpen ? 'order-1' : 'order-2' }} lg:order-1 lg:col-span-4 lg:block">
                <h2 class="mb-3 text-lg font-semibold" data-editor-heading tabindex="-1">{{ $editingGallery ? 'Edit galeri' : 'Tambah galeri' }}</h2>
                <div data-editor-errors>
                    @include('management.feedback')
                </div>

                <form data-editor-form action="{{ $editingGallery ? route('management.galeri.update', $editingGallery) : route('management.galeri.store') }}"
                    method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="_editing_id" value="{{ $editingId ?? '' }}">
                    <input type="hidden" name="_method" value="PUT" {{ $editingGallery ? '' : 'disabled' }}>

                    <div>
                        <label for="galleryTitle" class="mb-1 block text-sm">Judul</label>
                        <input type="text" name="title" id="galleryTitle" required
                            value="{{ $restoring ? old('title') : ($editingGallery?->title ?? '') }}"
                            class="min-h-11 w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="Judul foto galeri"
                            @if ($restoring && $errors->has('title')) aria-invalid="true" aria-describedby="galleryTitleError" @endif>
                        <p id="galleryTitleError" data-editor-error="title" class="mt-1 text-sm text-red-700{{ $restoring && $errors->has('title') ? '' : ' hidden' }}">{{ $restoring ? $errors->first('title') : '' }}</p>
                    </div>

                    <div>
                        <label for="galleryCaption" class="mb-1 block text-sm">Caption</label>
                        <textarea name="caption" id="galleryCaption" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2"
                            placeholder="Isi Caption Foto..." @if ($restoring && $errors->has('caption')) aria-invalid="true" aria-describedby="galleryCaptionError" @endif>{{ $restoring ? old('caption') : ($editingGallery?->caption ?? '') }}</textarea>
                        <p id="galleryCaptionError" data-editor-error="caption" class="mt-1 text-sm text-red-700{{ $restoring && $errors->has('caption') ? '' : ' hidden' }}">{{ $restoring ? $errors->first('caption') : '' }}</p>
                    </div>

                    <div>
                        <label for="galleryImage" class="mb-1 block text-sm">Gambar JPG/PNG (maks. 2 MB)</label>
                        <input type="file" name="image" id="galleryImage" accept="image/jpeg,image/png" {{ $editingGallery ? '' : 'required' }}
                            class="min-w-0 w-full rounded-lg border border-slate-300 px-3 py-2"
                            aria-describedby="galleryImageHelp{{ $restoring && $errors->has('image') ? ' galleryImageError' : '' }}"
                            @if ($restoring && $errors->has('image')) aria-invalid="true" @endif>
                        <p id="galleryImageHelp" data-editor-image-help class="mt-1 text-sm text-slate-600">{{ $editingGallery ? 'Pilih gambar baru jika ingin mengganti gambar.' : 'Gambar wajib dipilih.' }}{{ $restoring ? ' Pilih lagi berkas gambar setelah validasi.' : '' }}</p>
                        <p id="galleryImageError" data-editor-error="image" class="mt-1 text-sm text-red-700{{ $restoring && $errors->has('image') ? '' : ' hidden' }}">{{ $restoring ? $errors->first('image') : '' }}</p>
                        <img data-editor-preview @if ($editingGallery?->public_image_url) src="{{ $editingGallery->public_image_url }}" @endif alt="Pratinjau gambar galeri"
                            class="mt-2 h-40 w-full rounded-lg object-cover{{ $editingGallery?->public_image_url ? '' : ' hidden' }}">
                        <p data-editor-no-image class="mt-2 rounded-lg bg-slate-100 p-4 text-center text-sm text-[#536173]{{ $editingGallery?->public_image_url ? ' hidden' : '' }}">Tanpa gambar</p>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('management.galeri.index') }}" data-editor-cancel class="rf-btn-secondary{{ $editingGallery ? '' : ' hidden' }}">Batal</a>
                        <button type="submit" class="rf-btn">Simpan</button>
                    </div>
                </form>
            </section>

            <section class="rf-card min-w-0 {{ $panelOpen ? 'order-2' : 'order-1' }} lg:order-2 lg:col-span-8">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Galeri</h2>
                    <div class="text-sm text-slate-500">{{ $totalRows }} item</div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                    @foreach ($galleries as $gallery)
                        <article data-editor-item="{{ $gallery->id }}" class="min-w-0 overflow-hidden rounded-lg border border-slate-200 {{ (string) $editingId === (string) $gallery->id ? 'bg-blue-50' : 'bg-white' }}">
                            @if ($gallery->public_image_url)
                                <img src="{{ $gallery->public_image_url }}" alt="{{ $gallery->title }}" class="h-48 w-full object-cover">
                            @else
                                <div class="flex h-48 w-full items-center justify-center bg-slate-100 text-sm text-[#536173]" role="img" aria-label="Tanpa gambar">Tanpa gambar</div>
                            @endif
                            <h3 class="mt-2 break-words px-4 text-lg font-semibold">{{ $gallery->title }}</h3>
                            <p class="break-words px-4 text-sm text-[#536173]">{{ Str::limit($gallery->caption, 80) }}</p>
                            <div class="mt-3 flex flex-wrap justify-between gap-2 border-t border-slate-200 p-4">
                                <a href="{{ route('management.galeri.index', ['edit' => $gallery->id]) }}#content-editor" data-editor-select
                                    data-id="{{ $gallery->id }}" data-title="{{ $gallery->title }}" data-caption="{{ $gallery->caption }}"
                                    data-image="{{ $gallery->public_image_url }}" data-update-url="{{ route('management.galeri.update', $gallery) }}"
                                    class="rf-btn-secondary p-2" aria-label="Edit galeri {{ $gallery->title }}" title="Edit galeri {{ $gallery->title }}"
                                    data-action-tooltip="Edit galeri {{ $gallery->title }}" @if ((string) $editingId === (string) $gallery->id) aria-current="true" @endif>
                                    <i class="fas fa-pen" aria-hidden="true"></i>
                                </a>
                                <form action="{{ route('management.galeri.destroy', $gallery->id) }}" method="POST"
                                    data-confirm-title="Hapus galeri" data-confirm-message="Galeri ini akan dihapus permanen." data-confirm-label="Hapus" data-confirm-danger>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rf-btn-danger p-2" aria-label="Hapus galeri {{ $gallery->title }}" title="Hapus galeri {{ $gallery->title }}" data-action-tooltip="Hapus galeri {{ $gallery->title }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
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
    </div>
@endsection
