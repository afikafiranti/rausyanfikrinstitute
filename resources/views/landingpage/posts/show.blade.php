@extends('landingpage.layouts.landing', ['title' => $post->title.' | Rausyan Fikr Institute'])

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <a href="{{ route('public.posts.index') }}" class="font-semibold text-blue-800 underline underline-offset-4">Kembali ke refleksi</a>
        <h1 class="mt-8 break-words text-3xl font-bold leading-tight sm:text-4xl">{{ $post->title }}</h1>
        <time datetime="{{ $post->published_at->toAtomString() }}" class="mt-4 block text-sm text-slate-600">{{ $post->published_at->locale('id')->translatedFormat('d F Y') }}</time>
        @if ($post->public_cover_url)
            <img src="{{ $post->public_cover_url }}" alt="{{ $post->title }}" width="1200" height="675" class="mt-8 aspect-video w-full rounded-xl object-cover">
        @endif
        <div class="mt-8 whitespace-pre-wrap break-words text-lg leading-relaxed text-slate-800">{{ $post->content ?: $post->excerpt ?: 'Isi refleksi belum tersedia.' }}</div>
    </article>
@endsection
