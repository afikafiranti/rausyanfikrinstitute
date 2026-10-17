@extends('landingpage.layouts.landing', ['title' => 'Galeri | Rausyan Fikr Institute'])

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-10 sm:px-6"><h1 class="text-3xl font-bold">Semua galeri</h1></div>
    @include('landingpage.sections.galeri')
    <div class="mx-auto max-w-6xl px-4 pb-12 sm:px-6">{{ $galleries->links() }}</div>
@endsection
