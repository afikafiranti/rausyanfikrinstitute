@extends('layouts.app')

@section('page-content')
    <section class="mx-auto max-w-4xl min-w-0" aria-labelledby="reflections-heading">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 id="reflections-heading" class="rf-page-title">Refleksi saya</h1>
                <p class="mt-2 text-muted">Tulis pengalaman belajar Anda. Draf hanya dapat Anda lihat. Setelah dikirim, koorda wilayah kiriman serta admin dan super admin dapat membaca dan menanggapinya. Refleksi ini tidak otomatis diterbitkan ke publik.</p>
            </div>
            <a href="{{ route('reflections.create') }}" class="rf-btn">Tulis refleksi</a>
        </div>

        @if (session('success'))
            <p role="status" class="mt-5 rounded-lg bg-green-50 p-4 text-green-900">{{ session('success') }}</p>
        @endif

        <div class="rf-card mt-6">
            <ul class="divide-y divide-slate-200">
                @forelse ($reflections as $reflection)
                    <li class="min-w-0 py-5 first:pt-0 last:pb-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <a href="{{ route('reflections.show', $reflection) }}" class="rf-text-action block min-w-0 break-words text-base">{{ $reflection->title }}</a>
                            <span class="rf-badge {{ $reflection->status === 'reviewed' ? 'bg-green-100 text-green-900' : ($reflection->status === 'submitted' ? 'bg-blue-100 text-blue-900' : 'bg-slate-100 text-slate-700') }}">{{ ['draft' => 'Draf', 'submitted' => 'Terkirim', 'reviewed' => 'Sudah ditanggapi'][$reflection->status] ?? $reflection->status }}</span>
                        </div>
                        <p class="mt-1 break-words text-sm text-muted">{{ ['materi' => 'Materi', 'kajian' => 'Kajian', 'lainnya' => 'Lainnya'][$reflection->context] ?? $reflection->context }}@if ($reflection->activity_name) · {{ $reflection->activity_name }}@endif</p>
                    </li>
                @empty
                    <li class="py-3 text-muted">Belum ada refleksi. Mulai dengan menyimpan draf pertama Anda.</li>
                @endforelse
            </ul>
        </div>
        <div class="mt-5">{{ $reflections->links() }}</div>
    </section>
@endsection
