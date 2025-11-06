@extends('landingpage.layouts.landing')

@section('content')
  @include('landingpage.sections.navbar')
  @include('landingpage.sections.jumbotron')
  @include('landingpage.sections.statistik')
  @include('landingpage.sections.tentang')
  @include('landingpage.sections.agenda')
  @include('landingpage.sections.cta')
  @include('landingpage.sections.tim')
  @include('landingpage.sections.berita')
  @include('landingpage.sections.galeri')
  @include('landingpage.sections.footer')
@endsection
