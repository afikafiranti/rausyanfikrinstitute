@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Daftar</h1>
    <p class="text-sm text-slate-600 mb-4">Setelah mendaftar, lengkapi profil dan wilayah. Akses materi tersedia setelah akun disetujui pengelola sesuai level.</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-3">
      @csrf
      <div>
        <label for="name" class="block text-sm mb-1">Nama</label>
        <input id="name" type="text" name="name" autocomplete="name" value="{{ old('name') }}" required class="w-full rounded-lg">
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="email" class="block text-sm mb-1">Email</label>
        <input id="email" type="email" name="email" autocomplete="email" value="{{ old('email') }}" required class="w-full rounded-lg">
        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label for="password" class="block text-sm mb-1">Password</label>
          <input id="password" type="password" name="password" autocomplete="new-password" aria-describedby="password-help" required class="w-full rounded-lg">
          @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label for="password_confirmation" class="block text-sm mb-1">Ulangi Password</label>
          <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required class="w-full rounded-lg">
        </div>
      </div>

      <p id="password-help" class="text-sm text-slate-600">Gunakan minimal 8 karakter dengan huruf besar, huruf kecil, dan angka.</p>
      <button class="rf-btn w-full">Buat Akun</button>
    </form>

    <p class="text-sm text-slate-600 mt-4">
      Sudah punya akun? <a href="{{ route('login') }}" class="text-indigo-600">Masuk</a>
    </p>
  </div>
@endsection
