@extends('layouts.app')

@section('page-content')
    <article class="mx-auto max-w-3xl min-w-0" aria-labelledby="reflection-heading">
        <a href="{{ $reviewMode ? route('reflections.review.index') : route('reflections.index') }}" class="rf-text-action">{{ $reviewMode ? 'Kembali ke antrean' : 'Kembali ke refleksi saya' }}</a>
        <div class="rf-card mt-3 min-w-0">
            @if (session('success'))
                <p role="status" class="mb-5 rounded-lg bg-green-50 p-4 text-green-900">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-lg bg-red-50 p-4 text-red-900">
                    <p class="font-semibold">Periksa kembali tanggapan Anda.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <span class="rf-badge {{ $reflection->status === 'reviewed' ? 'bg-green-100 text-green-900' : ($reflection->status === 'submitted' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-700') }}">{{ ['draft' => 'Draf', 'submitted' => 'Terkirim', 'reviewed' => 'Sudah ditanggapi'][$reflection->status] ?? $reflection->status }}</span>
            <h1 id="reflection-heading" class="rf-page-title mt-4 break-words">{{ $reflection->title }}</h1>
            <dl class="mt-6 grid min-w-0 gap-4 border-y border-slate-200 py-5 sm:grid-cols-2">
                <div class="min-w-0"><dt class="text-sm text-muted">Konteks</dt><dd class="mt-1 break-words font-medium text-ink">{{ ['materi' => 'Materi', 'kajian' => 'Kajian', 'lainnya' => 'Lainnya'][$reflection->context] ?? $reflection->context }}</dd></div>
                @if ($reflection->activity_name)
                    <div class="min-w-0"><dt class="text-sm text-muted">Materi atau kegiatan</dt><dd class="mt-1 break-words font-medium text-ink">{{ $reflection->activity_name }}</dd></div>
                @endif
                @if ($reviewMode)
                    <div class="min-w-0"><dt class="text-sm text-muted">Penulis</dt><dd class="mt-1 break-words font-medium text-ink">{{ $reflection->user?->name ?? 'Tidak tersedia' }}</dd></div>
                    <div class="min-w-0"><dt class="text-sm text-muted">Wilayah</dt><dd class="mt-1 break-words font-medium text-ink">{{ $reflection->wilayah?->name ?? 'Tidak tersedia' }}</dd></div>
                @endif
                @if ($reflection->submitted_at)
                    <div><dt class="text-sm text-muted">Dikirim</dt><dd class="mt-1 font-medium text-ink">{{ $reflection->submitted_at->format('d/m/Y H:i') }}</dd></div>
                @endif
            </dl>
            <h2 class="mt-6 text-lg font-semibold text-ink">Isi refleksi</h2>
            <p class="mt-3 whitespace-pre-wrap break-words text-ink">{{ $reflection->body }}</p>

            @if (!$reviewMode && $reflection->status === 'draft' && $reflection->user_id === auth()->id())
                <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('reflections.edit', $reflection) }}" class="rf-btn-secondary">Edit draf</a>
                    <form action="{{ route('reflections.destroy', $reflection) }}" method="POST" onsubmit="return confirm('Hapus draf refleksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rf-btn-danger">Hapus draf</button>
                    </form>
                </div>
                @if (auth()->user()->status === 'active' && auth()->user()->wilayah_id)
                    <form action="{{ route('reflections.submit', $reflection) }}" method="POST" class="mt-6 rounded-lg border border-slate-200 p-4">
                        @csrf
                        <p class="text-sm text-muted">Setelah dikirim, refleksi tidak dapat diedit. Koorda wilayah kiriman serta admin dan super admin dapat membaca dan memberi tanggapan. Refleksi tidak otomatis diterbitkan ke publik.</p>
                        <button type="submit" class="rf-btn mt-4">Kirim untuk ditanggapi</button>
                    </form>
                @else
                    <p class="mt-6 rounded-lg bg-slate-50 p-4 text-muted">Refleksi dapat dikirim setelah akun aktif dan wilayah ditetapkan. Draf tetap tersimpan untuk Anda.</p>
                @endif
            @elseif (!$reviewMode && $reflection->status !== 'draft')
                <p class="mt-6 text-sm text-muted">Refleksi yang sudah dikirim hanya dapat dibaca.</p>
            @endif

            @if ($reflection->status === 'reviewed')
                <section class="mt-8 rounded-lg border border-slate-200 bg-slate-50 p-4" aria-labelledby="feedback-heading">
                    <h2 id="feedback-heading" class="text-lg font-semibold text-ink">Tanggapan pengelola</h2>
                    <p class="mt-1 break-words text-sm text-muted">{{ $reflection->reviewer?->name ?? 'Pengelola' }}@if ($reflection->reviewed_at) · {{ $reflection->reviewed_at->format('d/m/Y H:i') }}@endif</p>
                    <p class="mt-4 whitespace-pre-wrap break-words text-ink">{{ $reflection->feedback }}</p>
                </section>
            @endif

            @if ($reviewMode && $reflection->status === 'submitted')
                <section class="mt-8 border-t border-slate-200 pt-6" aria-labelledby="review-heading">
                    <h2 id="review-heading" class="text-lg font-semibold text-ink">Beri tanggapan</h2>
                    <p class="mt-2 text-sm text-muted">Tanggapan akan terlihat oleh penulis. Isi refleksi tidak dapat diubah oleh pengelola.</p>
                    <form action="{{ route('reflections.review.update', $reflection) }}" method="POST" class="mt-5">
                        @csrf
                        @method('PATCH')
                        <label for="feedback" class="block font-semibold text-ink">Tanggapan *</label>
                        <textarea id="feedback" name="feedback" rows="6" required maxlength="5000" class="mt-2 w-full" @error('feedback') aria-invalid="true" aria-describedby="feedback-error" @enderror>{{ old('feedback') }}</textarea>
                        @error('feedback') <p id="feedback-error" class="mt-1 text-sm text-red-800">{{ $message }}</p> @enderror
                        <button type="submit" class="rf-btn mt-4">Simpan tanggapan</button>
                    </form>
                </section>
            @endif
        </div>
    </article>
@endsection
