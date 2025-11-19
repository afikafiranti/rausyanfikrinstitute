@php
    $link = fn($active) => $active
        ? 'text-xs uppercase py-3 font-bold block text-red-600 hover:text-red-600'
        : 'text-xs uppercase py-3 font-bold block text-blueGray-700 hover:text-blueGray-500';
@endphp

<nav
    class="hidden md:block md:left-0 md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden
            shadow-xl bg-white relative md:w-64 z-10 py-4 px-6">
    <div
        class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto">

        {{-- Burger mobile --}}
        <button
            class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
            type="button" onclick="toggleNavbar('example-collapse-sidebar')">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}"
            class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0">
            Rausyan Fikr
        </a>

        {{-- Collapsible --}}
        <div id="example-collapse-sidebar"
            class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40
                overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded hidden">

            <hr class="my-4 md:min-w-full" />
            <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4">Navigasi</h6>

            <ul class="md:flex-col md:min-w-full flex flex-col list-none">

                {{-- Dashboard: semua --}}
                <li class="items-center">
                    <a href="{{ route('dashboard') }}" class="{{ $link(request()->routeIs('dashboard')) }}">
                        <i class="fas fa-tv mr-2 text-sm opacity-75"></i> Dashboard
                    </a>
                </li>

                {{-- Materi --}}
                <li class="items-center">
                    <a href="{{ route('materi.index') }}" class="{{ $link(request()->routeIs('materi.*')) }}">
                        <i class="fas fa-book-open mr-2 text-sm text-blueGray-300"></i> Materi
                    </a>
                </li>

                {{-- Laporan --}}
                <li class="items-center">
                    <a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.index')) }}">
                        <i class="fas fa-file-lines mr-2 text-sm text-blueGray-300"></i> Laporan
                    </a>
                </li>

                {{-- Review Laporan --}}
                @can('review-report')
                    <li class="items-center">
                        <a href="{{ route('laporan.review') }}" class="{{ $link(request()->routeIs('laporan.review')) }}">
                            <i class="fas fa-clipboard-check mr-2 text-sm text-blueGray-300"></i> Review Laporan
                        </a>
                    </li>
                @endcan

                {{-- Verifikasi --}}
                @can('review-user')
                    <li class="items-center">
                        <a href="{{ route('verification.index') }}"
                            class="{{ $link(request()->routeIs('verification.*')) }}">
                            <i class="fas fa-user-check mr-2 text-sm text-blueGray-300"></i> Verifikasi
                        </a>
                    </li>
                @endcan

                {{-- Alumni --}}
                @can('view-alumni')
                    <li class="items-center">
                        <a href="{{ route('alumni.index') }}" class="{{ $link(request()->routeIs('alumni.*')) }}">
                            <i class="fas fa-user-graduate mr-2 text-sm text-blueGray-300"></i> Alumni
                        </a>
                    </li>
                @endcan

                {{-- Profile --}}
                @auth
                    <li class="items-center">
                        <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.edit')) }}">
                            <i class="fas fa-user-circle mr-2 text-sm text-blueGray-300"></i> Profile
                        </a>
                    </li>
                @endauth

                {{-- ================== Manajemen Web ================== --}}
                @can('manage-content')
                    <hr class="my-4 md:min-w-full" />
                    <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4">Manajemen Web
                    </h6>

                    <li class="items-center">
                        <a href="{{ route('management.post.index') }}"
                            class="{{ $link(request()->routeIs('management.post.*')) }}">
                            <i class="fas fa-newspaper mr-2 text-sm text-blueGray-300"></i> Berita
                        </a>
                    </li>

                    <li class="items-center">
                        <a href="{{ route('management.galeri.index') }}"
                            class="{{ $link(request()->routeIs('management.galeri.*')) }}">
                            <i class="fas fa-images mr-2 text-sm text-blueGray-300"></i> Galeri
                        </a>
                    </li>
                @endcan


            </ul>
        </div>
    </div>
</nav>
