@extends('landingpage.layouts.landing', ['title' => $post->title.' | Rausyan Fikr Institute'])

@section('content')
    <article class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
        <a href="{{ route($isArticle ? 'public.articles.index' : 'public.posts.index') }}" class="rf-text-action">&larr; Kembali ke {{ $isArticle ? 'artikel' : 'refleksi' }}</a>
        <header class="mt-10 border-l-4 border-accent pl-5 sm:pl-7">
            <p class="text-sm font-bold uppercase tracking-widest text-muted">{{ $isArticle ? 'Artikel' : 'Refleksi' }}</p>
            <h1 class="mt-3 break-words text-4xl font-extrabold leading-tight tracking-tight text-ink sm:text-5xl">{{ $post->title }}</h1>
            <time datetime="{{ $post->published_at->toAtomString() }}" class="mt-5 block text-sm text-muted">{{ $post->published_at->locale('id')->translatedFormat('d F Y') }}</time>
        </header>
        @if ($post->public_cover_url)
            <img src="{{ $post->public_cover_url }}" alt="{{ $post->title }}" width="1200" height="675" class="mt-10 aspect-video w-full rounded-xl object-cover">
        @endif
        <div class="mx-auto mt-10 max-w-[65ch] whitespace-pre-wrap break-words text-[1.0625rem] leading-[1.75] text-ink sm:text-lg">{{ $post->content ?: $post->excerpt ?: ($isArticle ? 'Isi artikel belum tersedia.' : 'Isi refleksi belum tersedia.') }}</div>
    </article>
@endsection
