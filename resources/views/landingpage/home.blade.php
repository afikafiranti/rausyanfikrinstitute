@extends('landingpage.layouts.landing')

@section('content')
    @include('landingpage.sections.jumbotron')
    @include('landingpage.sections.statistik')
    @include('landingpage.sections.tentang')
    @include('landingpage.sections.berita')
    @include('landingpage.sections.galeri')
    @include('landingpage.sections.agenda')
    @include('landingpage.sections.cta')
@endsection
