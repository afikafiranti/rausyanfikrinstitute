<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'Rausyan Fikr' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-slate-800">

  {{-- SHELL DESKTOP (≥ md): sidebar + topbar --}}
  <div class="hidden md:grid md:grid-cols-[240px_1fr] md:min-h-screen">
    @include('partials.nav.desktop')
    <div class="flex flex-col">
      <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
        <div class="px-6 py-3 flex items-center justify-between">
          <div class="font-semibold">Dashboard Nexus</div>
          <div class="text-sm text-slate-600">
            {{-- aksi user/notification --}}
          </div>
        </div>
      </header>
      <main class="px-6 py-6">
        @yield('content-desktop')
      </main>
    </div>
  </div>

  {{-- SHELL MOBILE (< md): topbar + bottom navbar --}}
  <div class="md:hidden min-h-screen pb-16">
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
      <div class="px-4 py-3 flex items-center justify-between">
        <div class="font-semibold">Rausyan Fikr</div>
        <div class="text-sm text-slate-600"></div>
      </div>
    </header>
    <main class="p-4">
      @yield('content-mobile')
    </main>
    @include('partials.nav.mobile')
  </div>

</body>
</html>
