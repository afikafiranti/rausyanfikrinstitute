<footer class="bg-ink px-4 py-12 text-white sm:px-6">
    <div class="mx-auto flex max-w-6xl flex-col justify-between gap-8 sm:flex-row">
        <div><a href="{{ route('landing_page') }}" class="rounded-sm text-lg font-bold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Rausyan Fikr Institute</a><p class="mt-2 text-sm text-white/80">Artikel dan dokumentasi kajian.</p></div>
        <nav aria-label="Navigasi footer" class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm font-semibold">
            <a href="{{ route('public.articles.index') }}" class="inline-flex min-h-11 items-center rounded-sm hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">Artikel</a>
            <a href="{{ route('public.gallery.index') }}" class="inline-flex min-h-11 items-center rounded-sm hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">Galeri</a>
            <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center rounded-sm hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-white">Masuk</a>
        </nav>
    </div>
    <p class="mx-auto mt-8 max-w-6xl border-t border-white/20 pt-6 text-sm text-white/70">&copy; {{ now()->year }} Rausyan Fikr Institute</p>
</footer>
