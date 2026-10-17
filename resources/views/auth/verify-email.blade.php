@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-2">Verifikasi Email</h1>
    <p class="text-sm text-slate-600">Verifikasi alamat email melalui tautan di inbox Anda. Anda dapat meminta tautan baru di bawah ini.</p>

    @if (session('status') == 'verification-link-sent')
      <div role="status" class="rf-card mt-3 bg-green-50">Email verifikasi baru telah dikirim.</div>
    @elseif (session('status') == 'verification-link-failed')
      <div role="alert" class="rf-card mt-3 bg-red-50">Email verifikasi belum dapat dikirim. Coba kirim ulang nanti. Akun Anda tetap tersimpan.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
      @csrf
      <button class="rf-btn w-full">Kirim Ulang Email Verifikasi</button>
    </form>

    <p class="mt-3 text-sm"><a href="{{ route('profile.edit') }}" class="text-indigo-600 underline">Lanjutkan melengkapi profil</a></p>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
      @csrf
      <button class="w-full text-sm text-slate-600 underline">Keluar</button>
    </form>
  </div>
@endsection
