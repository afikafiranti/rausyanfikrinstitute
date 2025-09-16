@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Daftar</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-3">
      @csrf
      <div>
        <label class="block text-sm mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg">
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg">
        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-sm mb-1">Password</label>
          <input type="password" name="password" required class="w-full rounded-lg">
          @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm mb-1">Ulangi Password</label>
          <input type="password" name="password_confirmation" required class="w-full rounded-lg">
        </div>
      </div>

      <button class="rf-btn w-full">Buat Akun</button>
    </form>

    <p class="text-sm text-slate-600 mt-4">
      Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600">Masuk</a>
    </p>
  </div>
@endsection
