@extends('landingpage.layouts.landing', ['title' => 'Galeri | Rausyan Fikr Institute'])

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-12 sm:px-6 sm:pt-16">
        <span class="mb-3 block h-1 w-10 bg-accent" aria-hidden="true"></span>
        <h1 class="rf-page-title text-4xl sm:text-5xl">Semua galeri</h1>
        <p class="mt-3 text-muted">Dokumentasi yang dibagikan oleh pengelola Rausyan Fikr.</p>
    </div>
    @include('landingpage.sections.galeri', ['showSectionHeader' => false, 'featuredGallery' => false])
    <div class="mx-auto max-w-6xl px-4 pb-16 sm:px-6">{{ $galleries->links() }}</div>
@endsection
