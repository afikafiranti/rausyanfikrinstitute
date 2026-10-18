@extends('layouts.app')

@section('page-content')
    <section class="mx-auto max-w-3xl min-w-0" aria-labelledby="reflection-form-heading">
        <a href="{{ isset($reflection) ? route('reflections.show', $reflection) : route('reflections.index') }}" class="rf-text-action">Kembali ke refleksi</a>
        <div class="rf-card mt-3">
            <h1 id="reflection-form-heading" class="rf-page-title">{{ isset($reflection) ? 'Edit draf refleksi' : 'Tulis refleksi' }}</h1>
            <p class="mt-2 text-muted">Simpan sebagai draf, lalu periksa sebelum mengirim. Draf hanya dapat Anda lihat dan tidak otomatis diterbitkan ke publik.</p>

            @if ($errors->any())
                <div role="alert" class="mt-5 rounded-lg bg-red-50 p-4 text-red-900">
                    <p class="font-semibold">Periksa kembali isian refleksi.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($reflection) ? route('reflections.update', $reflection) : route('reflections.store') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                @isset($reflection)
                    @method('PATCH')
                @endisset
                <div>
                    <label for="title" class="block font-semibold text-ink">Judul refleksi *</label>
                    <input id="title" name="title" value="{{ old('title', $reflection->title ?? '') }}" required maxlength="180" class="mt-2 w-full" @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
                    @error('title') <p id="title-error" class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="context" class="block font-semibold text-ink">Konteks *</label>
                    <select id="context" name="context" required class="mt-2 w-full" @error('context') aria-invalid="true" aria-describedby="context-error" @enderror>
                        <option value="">Pilih konteks</option>
                        @foreach (['materi' => 'Materi', 'kajian' => 'Kajian', 'lainnya' => 'Lainnya'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('context', $reflection->context ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('context') <p id="context-error" class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="activity_name" class="block font-semibold text-ink">Nama materi atau kegiatan <span class="font-normal text-muted">(opsional)</span></label>
                    <input id="activity_name" name="activity_name" value="{{ old('activity_name', $reflection->activity_name ?? '') }}" maxlength="180" class="mt-2 w-full" @error('activity_name') aria-invalid="true" aria-describedby="activity-name-error" @enderror>
                    @error('activity_name') <p id="activity-name-error" class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="body" class="block font-semibold text-ink">Isi refleksi *</label>
                    <textarea id="body" name="body" rows="10" required maxlength="20000" class="mt-2 w-full" @error('body') aria-invalid="true" aria-describedby="body-error" @enderror>{{ old('body', $reflection->body ?? '') }}</textarea>
                    @error('body') <p id="body-error" class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="rf-btn">Simpan draf</button>
            </form>
        </div>
    </section>
@endsection
