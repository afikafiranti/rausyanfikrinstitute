<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Rausyan Fikr' }}</title>
    @if (!app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="antialiased text-blueGray-700">

    {{-- SIDEBAR FIXED (struktur Notus) --}}
    @include('partials.nav.sidebar')

    {{-- WRAPPER KONTEN --}}
    <div class="relative md:ml-64 bg-blueGray-50 min-h-screen">
        {{-- TOP NAV TRANSPARENT --}}
        @include('partials.nav.topnav')

        {{-- HEADER GRADIENT --}}
        <div class="relative z-0 bg-red-700 md:pt-32 pb-32 pt-8 ">
            <div class="px-4 md:px-10 mx-auto w-full">
                @yield('page-header')
            </div>
        </div>

        {{-- KONTEN HALAMAN --}}
        <main class="relative z-0 px-4  md:px-10 mx-auto w-full -m-36">
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
        };
    </script>
    @include('partials.nav.drawer')

</body>

</html>
