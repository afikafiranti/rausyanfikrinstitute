@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Lupa Password</h1>

    @if(session('status'))
      <div class="rf-card mb-4 bg-blue-50">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm mb-1">Email terdaftar</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg">
        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
      <button class="rf-btn w-full">Kirim Link Reset</button>
    </form>

    <p class="text-sm text-slate-600 mt-4">
      Kembali ke <a href="{{ route('login') }}" class="text-indigo-600">Masuk</a>
    </p>
  </div>
@endsection
