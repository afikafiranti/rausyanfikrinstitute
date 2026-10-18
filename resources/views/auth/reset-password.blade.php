@extends('layouts.guest')

@section('content')
  <div class="rf-section">
    <h1 class="text-xl font-semibold mb-4">Password Baru</h1>

    @if ($errors->has('email') || $errors->has('token'))
      <div role="alert" class="rf-card mb-4 bg-red-50">
        @foreach (array_merge($errors->get('email'), $errors->get('token')) as $message)
          <p>{{ $message }}</p>
        @endforeach
        <a href="{{ route('password.request') }}" class="text-brand underline">Minta tautan reset baru</a>
      </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-3">
      @csrf
      <input type="hidden" name="token" value="{{ request()->route('token') }}">
      <input type="hidden" name="email" value="{{ request()->query('email') }}">

      <div>
        <label for="password" class="block text-sm mb-1">Password Baru</label>
        <input id="password" type="password" name="password" autocomplete="new-password" aria-describedby="password-help" required class="w-full rounded-lg">
        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="password_confirmation" class="block text-sm mb-1">Ulangi Password Baru</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required class="w-full rounded-lg">
      </div>

      <p id="password-help" class="text-sm text-slate-600">Gunakan minimal 8 karakter dengan huruf besar, huruf kecil, dan angka.</p>
      <button class="rf-btn w-full">Simpan Password</button>
    </form>
  </div>
@endsection
