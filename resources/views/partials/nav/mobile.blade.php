<nav class="md:hidden fixed bottom-0 inset-x-0 z-50 bg-white border-t">
  <div class="grid grid-cols-4 text-xs">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-500' }}">
      <i class="fas fa-home text-xl" aria-hidden="true"></i>
      <span>Beranda</span>
    </a>
    <a href="{{ route('materi.index') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('materi.*') ? 'text-indigo-600' : 'text-slate-500' }}">
      <i class="fas fa-list text-xl" aria-hidden="true"></i>
      <span>Materi</span>
    </a>
    <a href="{{ route('laporan.index') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('laporan.*') ? 'text-indigo-600' : 'text-slate-500' }}">
      <i class="fas fa-file-alt text-xl" aria-hidden="true"></i>
      <span>Laporan</span>
    </a>
    <a href="{{ route('profile.show') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-slate-500' }}">
      <i class="fas fa-user text-xl" aria-hidden="true"></i>
      <span>Profil</span>
    </a>
  </div>
</nav>
