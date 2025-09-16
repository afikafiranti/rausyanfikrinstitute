@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-2">Verifikasi Email</h1>
    <p class="text-sm text-slate-600">Link verifikasi telah dikirim ke email Anda.</p>

    @if (session('status') == 'verification-link-sent')
      <div class="rf-card mt-3 bg-green-50">Email verifikasi baru telah dikirim.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
      @csrf
      <button class="rf-btn w-full">Kirim Ulang Email Verifikasi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
      @csrf
      <button class="w-full text-sm text-slate-600 underline">Keluar</button>
    </form>
  </div>
@endsection
