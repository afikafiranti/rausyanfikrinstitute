@extends('landingpage.layouts.landing', ['title' => 'Refleksi | Rausyan Fikr Institute'])

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-10 sm:px-6"><h1 class="text-3xl font-bold">Semua refleksi</h1></div>
    @include('landingpage.sections.berita')
    <div class="mx-auto max-w-6xl px-4 pb-12 sm:px-6">{{ $posts->links() }}</div>
@endsection
