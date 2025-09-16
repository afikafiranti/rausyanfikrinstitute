<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'Rausyan Fikr' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-full bg-gray-50 text-slate-800">
  <!-- Topbar sederhana -->
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex h-8 w-8 rounded-xl bg-indigo-600 shadow"></span>
        <span class="font-semibold">Rausyan Fikr</span>
      </div>
      <div class="text-sm text-slate-600">
        {{-- user / notif --}}
      </div>
    </div>
  </header>

  <main class="pb-16">
    <div class="max-w-7xl mx-auto p-4">
      @yield('content')
    </div>
  </main>

  <!-- Navbar bawah (mobile-first) -->
  <nav class="fixed bottom-0 inset-x-0 z-50 bg-white border-t">
    <div class="grid grid-cols-4 text-xs">
      <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-500' }}">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M3 10.5 12 3l9 7.5V21H3z"/></svg>
        <span>Beranda</span>
      </a>
      <a href="{{ route('materi.index') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('materi.*') ? 'text-indigo-600' : 'text-slate-500' }}">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M4 6h16M4 12h16M4 18h10"/></svg>
        <span>Materi</span>
      </a>
      <a href="{{ route('laporan.index') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('laporan.*') ? 'text-indigo-600' : 'text-slate-500' }}">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M7 7h10M7 12h10M7 17h6"/></svg>
        <span>Laporan</span>
      </a>
      <a href="{{ route('profile.show') }}" class="flex flex-col items-center py-2 {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-slate-500' }}">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.5" d="M16 7a4 4 0 1 1-8 0M4 20a8 8 0 1 1 16 0"/></svg>
        <span>Profil</span>
      </a>
    </div>
  </nav>
</body>
</html>
