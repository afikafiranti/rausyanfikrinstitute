@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Password Baru</h1>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-3">
      @csrf
      <input type="hidden" name="token" value="{{ request()->route('token') }}">
      <input type="hidden" name="email" value="{{ request()->query('email') }}">

      <div>
        <label class="block text-sm mb-1">Password Baru</label>
        <input type="password" name="password" required class="w-full rounded-lg">
        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Ulangi Password Baru</label>
        <input type="password" name="password_confirmation" required class="w-full rounded-lg">
      </div>

      <button class="rf-btn w-full">Simpan Password</button>
    </form>
  </div>
@endsection
