@extends('layouts.app')

@section('page-content')
    <section class="mx-auto max-w-4xl min-w-0" aria-labelledby="review-list-heading">
        <h1 id="review-list-heading" class="rf-page-title">Tinjau refleksi anggota</h1>
        <p class="mt-2 text-muted">Refleksi yang dikirim anggota di wilayah yang dapat Anda kelola. Tanggapan hanya terlihat oleh penulis dan pengelola yang berwenang.</p>

        @if (session('success'))
            <p role="status" class="mt-5 rounded-lg bg-green-50 p-4 text-green-900">{{ session('success') }}</p>
        @endif

        <div class="rf-card mt-6">
            <ul class="divide-y divide-slate-200">
                @forelse ($reflections as $reflection)
                    <li class="min-w-0 py-5 first:pt-0 last:pb-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <a href="{{ route('reflections.review.show', $reflection) }}" class="rf-text-action block min-w-0 break-words text-base">{{ $reflection->title }}</a>
                            <span class="rf-badge {{ $reflection->status === 'reviewed' ? 'bg-green-100 text-green-900' : 'bg-blue-100 text-blue-900' }}">{{ $reflection->status === 'reviewed' ? 'Sudah ditanggapi' : 'Terkirim' }}</span>
                        </div>
                        <p class="mt-1 break-words text-sm text-muted">{{ $reflection->user?->name ?? 'Penulis tidak tersedia' }} · {{ $reflection->wilayah?->name ?? 'Wilayah tidak tersedia' }}@if ($reflection->submitted_at) · {{ $reflection->submitted_at->format('d/m/Y H:i') }}@endif</p>
                    </li>
                @empty
                    <li class="py-3 text-muted">Belum ada refleksi yang dikirim untuk ditinjau.</li>
                @endforelse
            </ul>
        </div>
        <div class="mt-5">{{ $reflections->links() }}</div>
    </section>
@endsection
