<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Rausyan Fikr — Auth' }}</title>
    <link rel="icon" href="{{ asset('landingpage/images/logo/favicon.svg') }}" type="image/svg+xml">
    @if (!app()->environment('testing'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

</head>

<body class="rf-auth min-h-screen border-t-4 border-accent">
    <header class="py-6 sm:py-8">
        <a href="{{ route('landing_page') }}" class="mx-auto flex max-w-md items-center gap-3 px-4 text-ink">
            <img src="{{ asset('landingpage/images/logo/logo_rf.jpeg') }}" alt="" width="44" height="44" class="h-11 w-11 rounded object-contain">
            <span class="text-lg font-bold leading-tight">Rausyan Fikr <span class="block text-sm font-medium text-muted">Institute</span></span>
        </a>
    </header>

    <main class="mx-auto max-w-md px-4 pb-12">
        @yield('content')
    </main>
</body>

</html>
