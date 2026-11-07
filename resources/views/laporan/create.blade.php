@extends('layouts.app')

@section('page-content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('laporan.index') }}" class="rf-btn-secondary mb-4 px-3">Kembali</a>
        @include('laporan.partials.form')
    </div>
    @include('laporan.modal')
@endsection
