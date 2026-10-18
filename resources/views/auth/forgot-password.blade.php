@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Lupa Password</h1>
    <p class="text-sm text-slate-600 mb-4">Masukkan email akun Anda untuk meminta tautan penggantian password. Periksa juga folder spam setelah mengirim permintaan.</p>

    @if(session('status'))
      <div role="status" class="rf-card mb-4 bg-blue-50">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-3">
      @csrf
      <div>
        <label for="email" class="block text-sm mb-1">Email terdaftar</label>
        <input id="email" type="email" name="email" autocomplete="email" value="{{ old('email') }}" required class="w-full rounded-lg">
        @error('email') <p role="alert" class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
      <button class="rf-btn w-full">Kirim Link Reset</button>
    </form>

    <p class="text-sm text-slate-600 mt-4">
      Kembali ke <a href="{{ route('login') }}" class="text-brand">Masuk</a>
    </p>
  </div>
@endsection
