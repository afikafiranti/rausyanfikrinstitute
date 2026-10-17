<a href="{{ route('landing_page') }}#tentang" class="hover:text-blue-800">Tentang</a>
<a href="{{ route('public.posts.index') }}" class="hover:text-blue-800">Refleksi</a>
<a href="{{ route('public.gallery.index') }}" class="hover:text-blue-800">Galeri</a>
@auth
    <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-900 px-4 py-3 text-white hover:bg-blue-800">Area anggota</a>
@else
    <a href="{{ route('login') }}" class="rounded-lg bg-blue-900 px-4 py-3 text-white hover:bg-blue-800">Masuk</a>
@endauth
