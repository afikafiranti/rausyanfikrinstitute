@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Masuk</h1>

    @if(session('status'))
      <div class="rf-card mb-4 bg-blue-50"> {{ session('status') }} </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg">
        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Password</label>
        <input type="password" name="password" required class="w-full rounded-lg">
        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="remember" class="rounded"> Ingat saya
        </label>
        <a href="{{ route('password.request') }}" class="text-indigo-600">Lupa password?</a>
      </div>

      <button class="rf-btn w-full">Masuk</button>
    </form>

    <p class="text-sm text-slate-600 mt-4">
      Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600">Daftar</a>
    </p>
  </div>
@endsection
