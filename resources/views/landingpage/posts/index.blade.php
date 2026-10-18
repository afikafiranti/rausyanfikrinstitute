@extends('landingpage.layouts.landing', ['title' => ($isArticle ? 'Artikel' : 'Refleksi').' | Rausyan Fikr Institute'])

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-12 sm:px-6 sm:pt-16">
        <span class="mb-3 block h-1 w-10 bg-accent" aria-hidden="true"></span>
        <h1 class="rf-page-title text-4xl sm:text-5xl">Semua {{ $isArticle ? 'artikel' : 'refleksi' }}</h1>
        <p class="mt-3 text-muted">Tulisan dan catatan yang telah diterbitkan oleh pengelola.</p>
    </div>
    @include('landingpage.sections.berita', ['showSectionHeader' => false, 'featured' => false])
    <div class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">{{ $posts->links() }}</div>
@endsection
