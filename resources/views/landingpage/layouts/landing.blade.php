<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Rausyan Fikr Institute' }}</title>
    <meta name="description" content="Artikel, galeri kegiatan, dan ruang alumni Rausyan Fikr Institute.">
    <link rel="icon" href="{{ asset('landingpage/images/logo/favicon.svg') }}" type="image/svg+xml">
    @if (! app()->environment('testing'))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <a href="#konten-utama" class="sr-only focus:not-sr-only focus:block focus:bg-white focus:p-4">Langsung ke konten</a>
    @include('landingpage.sections.navbar')
    <main id="konten-utama">
        @yield('content')
    </main>
    @include('landingpage.sections.footer')
</body>
</html>
