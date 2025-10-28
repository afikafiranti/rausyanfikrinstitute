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
        <div class="relative">
          <input type="password" name="password" id="password" required
                 class="w-full rounded-lg pr-10">
          <button type="button" id="togglePassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">
            <!-- Mata default tertutup -->
            <svg id="iconEyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 0-1.091.174-2.14.5-3.125M6.6 6.6A9.969 9.969 0 0112 5c5.523 0 10 4.477 10 10 0 .828-.1 1.63-.29 2.394M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="2"/>
            </svg>
            <!-- Mata terbuka -->
            <svg id="iconEyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="flex items-center justify-between text-sm">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="remember" class="rounded"> Ingat saya
        </label>
        <a href="{{ route('password.request') }}" class="text-indigo-600">Lupa password?</a>
      </div>

      {{-- <button class="rf-btn w-full">Masuk</button> --}}
      <button type="submit" class="rf-btn w-full flex items-center justify-center text-center">Masuk</button>

    </form>
  </div>

  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const iconEyeOpen = document.getElementById('iconEyeOpen');
    const iconEyeClosed = document.getElementById('iconEyeClosed');

    togglePassword.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);

      // toggle icon
      iconEyeOpen.classList.toggle('hidden');
      iconEyeClosed.classList.toggle('hidden');
    });
  </script>
@endsection
