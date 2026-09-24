@extends('layouts.app')

@section('page-content')
    <div class="mx-auto max-w-5xl rounded-xl bg-white p-6 shadow">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <a href="{{ route('materi.index') }}" class="text-sm text-indigo-600 hover:underline">
                    &larr; Kembali ke Materi
                </a>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $playlist->title }}</h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ optional($playlist->level)->description ?? optional($playlist->level)->name ?? 'Tanpa level' }}
                </p>
            </div>

            <x-badge :color="$playlist->is_active ? 'green' : 'yellow'">
                {{ $playlist->is_active ? 'Publik' : 'Draft' }}
            </x-badge>
        </div>

        @if ($playlist->description)
            <p class="mb-6 text-sm leading-6 text-slate-600">{{ $playlist->description }}</p>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b text-left text-slate-600">
                    <tr>
                        <th class="py-2 pr-4">No</th>
                        <th class="py-2 pr-4">Judul Video</th>
                        <th class="py-2 pr-4">YouTube ID</th>
                        <th class="py-2">Durasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($playlist->videos as $video)
                        <tr>
                            <td class="py-2 pr-4">{{ $loop->iteration }}</td>
                            <td class="py-2 pr-4 font-medium text-slate-800">{{ $video->title }}</td>
                            <td class="py-2 pr-4">{{ $video->youtube_video_id }}</td>
                            <td class="py-2">{{ $video->duration_sec ? gmdate('H:i:s', $video->duration_sec) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500">
                                Belum ada video pada playlist ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
