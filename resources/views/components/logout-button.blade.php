@props(['label' => 'Keluar', 'class' => 'inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-red-600 text-white hover:opacity-90'])

<form method="POST" action="{{ route('logout') }}">
  @csrf
  <button type="submit" class="{{ $class }}">
    <i class="fas fa-sign-out-alt"></i> <span>{{ $label }}</span>
  </button>
</form>
