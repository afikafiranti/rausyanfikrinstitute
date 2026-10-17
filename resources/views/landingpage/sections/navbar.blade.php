<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <a href="{{ route('landing_page') }}" class="flex items-center gap-3 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-700">
            <img src="{{ asset('landingpage/images/logo/logo_rf.jpeg') }}" alt="" width="48" height="48" class="h-10 w-10 rounded object-contain sm:h-12 sm:w-12">
            <span class="max-w-32 text-sm font-semibold leading-snug sm:max-w-none sm:text-base">Rausyan Fikr Institute</span>
        </a>
        <nav aria-label="Navigasi utama" class="hidden items-center gap-6 text-sm font-medium md:flex">
            @include('landingpage.partials.navigation')
        </nav>
        <details class="relative md:hidden">
            <summary class="cursor-pointer rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold">Menu</summary>
            <nav aria-label="Navigasi seluler" class="absolute right-0 z-50 mt-2 flex w-56 flex-col gap-4 rounded-lg border border-slate-200 bg-white p-5 shadow-lg">
                @include('landingpage.partials.navigation')
            </nav>
        </details>
    </div>
</header>
