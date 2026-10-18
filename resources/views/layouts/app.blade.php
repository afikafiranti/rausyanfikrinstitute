<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Rausyan Fikr' }}</title>
    <link rel="icon" href="{{ asset('landingpage/images/logo/favicon.svg') }}" type="image/svg+xml">
    @if (!app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="rf-app">
    <a href="#konten-utama" class="sr-only focus:not-sr-only focus:fixed focus:top-0 focus:left-0 focus:z-[70] focus:bg-white focus:p-4">Langsung ke konten</a>

    {{-- SIDEBAR FIXED (struktur Notus) --}}
    @include('partials.nav.sidebar')

    {{-- WRAPPER KONTEN --}}
    <div class="relative min-h-screen min-w-0 md:ml-64">
        {{-- TOP NAV TRANSPARENT --}}
        @include('partials.nav.topnav')

        {{-- KONTEN HALAMAN --}}
        <main id="konten-utama" tabindex="-1" class="relative z-0 mx-auto w-full max-w-[1600px] px-4 py-6 md:px-8 md:py-8">
            @yield('page-header')
            @yield('page-content')
        </main>

        @yield('page-footer')
    </div>

    {{-- JS kecil untuk toggle, tetap tanpa lib tambahan --}}
    <script>
        window.toggleNavbar = id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
        };
        window.openDropdown = (e, id) => {
            e.preventDefault();
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
            e.currentTarget.setAttribute('aria-expanded', String(!el.classList.contains('hidden')));
        };
    </script>
    @include('partials.nav.drawer')

</body>

</html>
