<!doctype html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'Rausyan Fikr' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 text-slate-800">

    {{-- SHELL DESKTOP (≥ md): sidebar + topbar --}}
    <div class="hidden md:grid md:grid-cols-[240px_1fr] md:min-h-screen">
        @include('partials.nav.desktop')
        <div class="flex flex-col">
            <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
                <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-8 w-8 rounded-xl bg-brand shadow-card"></span>
                        <span class="font-semibold">Rausyan Fikr</span>
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                            <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center">
                                <i class="fas fa-bell text-lg"></i>
                                @if ($unread > 0)
                                    <span
                                        class="absolute -top-1 -right-2 inline-flex items-center justify-center
                         h-5 min-w-[20px] text-[11px] px-1 rounded-full bg-red-600 text-white">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="px-6 py-6">
                <div class="hidden md:block">
                    @yield('content-desktop')
                </div>
            </main>
        </div>
    </div>

    {{-- SHELL MOBILE (< md): topbar + bottom navbar --}}
    <div class="md:hidden min-h-screen pb-16">
        <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 rounded-xl bg-brand shadow-card"></span>
                    <span class="font-semibold">Rausyan Fikr</span>
                </div>

                <div class="flex items-center gap-4">
                    @auth
                        @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                        <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center">
                            <i class="fas fa-bell text-lg"></i>
                            @if ($unread > 0)
                                <span
                                    class="absolute -top-1 -right-2 inline-flex items-center justify-center
                         h-5 min-w-[20px] text-[11px] px-1 rounded-full bg-red-600 text-white">
                                    {{ $unread }}
                                </span>
                            @endif
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="p-4">
            <div class="md:hidden">
                @yield('content-mobile')
            </div>
        </main>
        @include('partials.nav.mobile')
    </div>

</body>

</html>
