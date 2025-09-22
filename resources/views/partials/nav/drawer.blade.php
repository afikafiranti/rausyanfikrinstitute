@php
  $link = fn($active) => $active
      ? 'flex items-center gap-3 px-4 py-3 text-pink-600 font-semibold'
      : 'flex items-center gap-3 px-4 py-3 text-blueGray-700 hover:text-blueGray-900';
@endphp

<div id="rf-mobile-drawer" class="fixed inset-0 z-[60] hidden">
  {{-- backdrop --}}
  <div class="absolute inset-0 bg-black/30" onclick="rfToggleDrawer('rf-mobile-drawer', false)"></div>

  {{-- panel --}}
  <aside class="absolute left-0 top-0 h-full w-72 bg-white shadow-xl overflow-y-auto">
    <div class="flex items-center justify-between p-4 border-b">
      <span class="font-bold uppercase">Navigasi</span>
      <button class="p-2" onclick="rfToggleDrawer('rf-mobile-drawer', false)"><i class="fas fa-times"></i></button>
    </div>

    <nav class="py-2">
      <a href="{{ route('dashboard') }}" class="{{ $link(request()->routeIs('dashboard')) }}">
        <i class="fas fa-tv w-5 text-center"></i><span>Dashboard</span>
      </a>
      <a href="{{ route('materi.index') }}" class="{{ $link(request()->routeIs('materi.*')) }}">
        <i class="fas fa-book-open w-5 text-center"></i><span>Materi</span>
      </a>
      <a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.index')) }}">
        <i class="fas fa-file-lines w-5 text-center"></i><span>Laporan</span>
      </a>
      @can('review-report')
      <a href="{{ route('laporan.review') }}" class="{{ $link(request()->routeIs('laporan.review')) }}">
        <i class="fas fa-clipboard-check w-5 text-center"></i><span>Review Laporan</span>
      </a>
      @endcan
      @can('review-user')
      <a href="{{ route('verification.index') }}" class="{{ $link(request()->routeIs('verification.*')) }}">
        <i class="fas fa-user-check w-5 text-center"></i><span>Verifikasi</span>
      </a>
      @endcan
      @can('view-alumni')
      <a href="{{ route('alumni.index') }}" class="{{ $link(request()->routeIs('alumni.*')) }}">
        <i class="fas fa-user-graduate w-5 text-center"></i><span>Alumni</span>
      </a>
      @endcan
      @auth
      <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.edit')) }}">
        <i class="fas fa-user-circle w-5 text-center"></i><span>Profile</span>
      </a>
      <a href="{{ route('notifications.index') }}" class="{{ $link(request()->routeIs('notifications.*')) }}">
        <i class="fas fa-bell w-5 text-center"></i><span>Notifikasi</span>
      </a>
      <form method="POST" action="{{ route('logout') }}" class="px-4 py-3">
        @csrf
        <button class="flex items-center gap-3 text-blueGray-700 hover:text-blueGray-900" type="submit">
          <i class="fas fa-right-from-bracket w-5 text-center"></i><span>Logout</span>
        </button>
      </form>
      @endauth
    </nav>
  </aside>
</div>
