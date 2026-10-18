<header class="relative z-20 border-b border-ink/10 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ route('landing_page') }}" class="flex items-center gap-3 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">
            <img src="{{ asset('landingpage/images/logo/logo_rf.jpeg') }}" alt="" width="48" height="48" class="h-11 w-11 rounded object-contain sm:h-12 sm:w-12">
            <span class="max-w-32 text-sm font-extrabold leading-snug tracking-tight text-ink sm:max-w-none sm:text-base">Rausyan Fikr Institute</span>
        </a>
        <nav aria-label="Navigasi utama" class="hidden items-center gap-4 text-sm md:flex">
            @include('landingpage.partials.navigation')
        </nav>
        <details class="relative md:hidden">
            <summary class="flex min-h-11 cursor-pointer items-center rounded-lg border border-ink/25 px-4 py-2 text-sm font-semibold text-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">Menu</summary>
            <nav aria-label="Navigasi seluler" class="absolute right-0 z-50 mt-2 flex w-56 flex-col gap-2 rounded-xl border border-ink/15 bg-white p-4 shadow-xl">
                @include('landingpage.partials.navigation')
            </nav>
        </details>
    </div>
</header>
