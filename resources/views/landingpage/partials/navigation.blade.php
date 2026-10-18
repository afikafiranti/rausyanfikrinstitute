<a href="{{ route('public.articles.index') }}" @if (request()->routeIs('public.articles.*')) aria-current="page" @endif class="inline-flex min-h-11 items-center rounded-md px-2 font-semibold transition-colors hover:text-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand {{ request()->routeIs('public.articles.*') ? 'text-brand' : 'text-ink' }}">Artikel</a>
<a href="{{ route('public.gallery.index') }}" @if (request()->routeIs('public.gallery.*')) aria-current="page" @endif class="inline-flex min-h-11 items-center rounded-md px-2 font-semibold transition-colors hover:text-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand {{ request()->routeIs('public.gallery.*') ? 'text-brand' : 'text-ink' }}">Galeri</a>
@auth
    <a href="{{ route('dashboard') }}" class="rf-btn justify-center">Area anggota</a>
@else
    <a href="{{ route('login') }}" class="rf-btn justify-center">Masuk</a>
@endauth
