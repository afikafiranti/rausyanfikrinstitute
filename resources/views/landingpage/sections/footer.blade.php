<footer class="bg-slate-950 px-4 py-10 text-slate-200 sm:px-6">
    <div class="mx-auto flex max-w-6xl flex-col justify-between gap-6 sm:flex-row">
        <div><a href="{{ route('landing_page') }}" class="font-semibold text-white">Rausyan Fikr Institute</a><p class="mt-2 text-sm text-slate-300">Artikel dan dokumentasi kajian.</p></div>
        <nav aria-label="Navigasi footer" class="flex flex-wrap gap-5 text-sm">
            <a href="{{ route('public.articles.index') }}" class="hover:underline">Artikel</a>
            <a href="{{ route('public.gallery.index') }}" class="hover:underline">Galeri</a>
            <a href="{{ route('login') }}" class="hover:underline">Masuk</a>
        </nav>
    </div>
    <p class="mx-auto mt-8 max-w-6xl text-sm text-slate-400">&copy; {{ now()->year }} Rausyan Fikr Institute</p>
</footer>
