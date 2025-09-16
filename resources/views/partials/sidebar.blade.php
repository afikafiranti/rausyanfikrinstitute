<nav class="md:left-0 md:block md:fixed md:top-0 md:bottom-0 md:overflow-y-auto md:flex-row md:flex-nowrap md:overflow-hidden shadow-xl bg-white flex flex-wrap items-center justify-between relative md:w-64 z-10 py-4 px-6">
  <div class="md:flex-col md:items-stretch md:min-h-full md:flex-nowrap px-0 flex flex-wrap items-center justify-between w-full mx-auto">

    {{-- Hamburger mobile --}}
    <button class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
            type="button" onclick="toggleNavbar('example-collapse-sidebar')">
      <i class="fas fa-bars"></i>
    </button>

    {{-- Brand --}}
    <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
       href="{{ route('dashboard') }}">
      {{ config('app.name', 'RausyanFikr') }}
    </a>

    {{-- Mobile: bell & avatar di header sidebar --}}
    <ul class="md:hidden items-center flex flex-wrap list-none">
      <li class="inline-block relative">
        <details class="relative">
          <summary class="list-none text-blueGray-500 block py-1 px-3 cursor-pointer">
            <i class="fas fa-bell"></i>
          </summary>
          <div class="absolute right-0 mt-2 bg-white text-base z-50 py-2 list-none text-left rounded shadow-lg min-w-48">
            <span class="text-sm py-2 px-4 block text-blueGray-700">No notifications</span>
          </div>
        </details>
      </li>
      <li class="inline-block relative">
        <details class="relative">
          <summary class="list-none cursor-pointer block">
            <div class="items-center flex">
              <span class="w-12 h-12 inline-flex items-center justify-center rounded-full">
                <img alt="avatar" class="w-12 h-12 rounded-full align-middle border-none shadow-lg"
                     src="{{ auth()->user()->photo_url ?? asset('images/avatar-default.png') }}">
              </span>
            </div>
          </summary>
          <div class="absolute right-0 mt-2 bg-white text-base z-50 py-2 list-none text-left rounded shadow-lg min-w-48">
            <a href="{{ route('profile.show') }}" class="text-sm py-2 px-4 block text-blueGray-700">
              <i class="fas fa-user mr-2"></i> Profile
            </a>
            <div class="h-0 my-2 border border-solid border-blueGray-100"></div>
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button class="text-sm py-2 px-4 w-full text-left text-red-600">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </button>
            </form>
          </div>
        </details>
      </li>
    </ul>

    {{-- Isi sidebar (collapsible di mobile) --}}
    <div class="md:flex md:flex-col md:items-stretch md:opacity-100 md:relative md:mt-4 md:shadow-none shadow absolute top-0 left-0 right-0 z-40 overflow-y-auto overflow-x-hidden h-auto items-center flex-1 rounded hidden"
         id="example-collapse-sidebar">

      {{-- Header kecil saat mobile --}}
      <div class="md:min-w-full md:hidden block pb-4 mb-4 border-b border-solid border-blueGray-200">
        <div class="flex flex-wrap">
          <div class="w-6/12">
            <a class="md:block text-left md:pb-2 text-blueGray-600 mr-0 inline-block whitespace-nowrap text-sm uppercase font-bold p-4 px-0"
               href="{{ route('dashboard') }}">{{ config('app.name', 'RausyanFikr') }}</a>
          </div>
          <div class="w-6/12 flex justify-end">
            <button type="button" class="cursor-pointer text-black opacity-50 md:hidden px-3 py-1 text-xl leading-none bg-transparent rounded border border-solid border-transparent"
                    onclick="toggleNavbar('example-collapse-sidebar')">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      {{-- Search mobile --}}
      <form class="mt-6 mb-4 md:hidden">
        <div class="mb-3 pt-0">
          <input type="text" placeholder="Search" class="border-0 px-3 py-2 h-12 border border-solid border-blueGray-500 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-base leading-snug shadow-none outline-none focus:outline-none w-full font-normal">
        </div>
      </form>

      <hr class="my-4 md:min-w-full">

      {{-- Menu utama --}}
      <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4">Menu</h6>
      <ul class="md:flex-col md:min-w-full flex flex-col list-none">
        <li class="items-center">
          <a href="{{ route('dashboard') }}"
             class="text-xs uppercase py-3 font-bold block {{ request()->routeIs('dashboard') ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }}">
            <i class="fas fa-tv mr-2 text-sm {{ request()->routeIs('dashboard') ? 'text-pink-500' : 'text-blueGray-300' }}"></i>
            Dashboard
          </a>
        </li>
        <li class="items-center">
          <a href="{{ route('materi.index') }}"
             class="text-xs uppercase py-3 font-bold block {{ request()->routeIs('materi.*') ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }}">
            <i class="fas fa-book mr-2 text-sm {{ request()->routeIs('materi.*') ? 'text-pink-500' : 'text-blueGray-300' }}"></i>
            Materi
          </a>
        </li>
        <li class="items-center">
          <a href="{{ route('laporan.index') }}"
             class="text-xs uppercase py-3 font-bold block {{ request()->routeIs('laporan.*') ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }}">
            <i class="fas fa-file-alt mr-2 text-sm {{ request()->routeIs('laporan.*') ? 'text-pink-500' : 'text-blueGray-300' }}"></i>
            Laporan
          </a>
        </li>
        @can('view-alumni')
        <li class="items-center">
          <a href="{{ route('alumni.index') }}"
             class="text-xs uppercase py-3 font-bold block {{ request()->routeIs('alumni.*') ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }}">
            <i class="fas fa-users mr-2 text-sm {{ request()->routeIs('alumni.*') ? 'text-pink-500' : 'text-blueGray-300' }}"></i>
            Alumni
          </a>
        </li>
        @endcan
        @can('review-report')
        <li class="items-center">
          <a href="{{ route('laporan.review') }}"
             class="text-xs uppercase py-3 font-bold block {{ request()->routeIs('laporan.review') ? 'text-pink-500 hover:text-pink-600' : 'text-blueGray-700 hover:text-blueGray-500' }}">
            <i class="fas fa-check-circle mr-2 text-sm {{ request()->routeIs('laporan.review') ? 'text-pink-500' : 'text-blueGray-300' }}"></i>
            Review Laporan
          </a>
        </li>
        @endcan
      </ul>

      <hr class="my-4 md:min-w-full">

      {{-- Auth --}}
      <h6 class="md:min-w-full text-blueGray-500 text-xs uppercase font-bold block pt-1 pb-4">Akun</h6>
      <ul class="md:flex-col md:min-w-full flex flex-col list-none md:mb-4">
        <li class="items-center">
          <a href="{{ route('profile.show') }}" class="text-blueGray-700 hover:text-blueGray-500 text-xs uppercase py-3 font-bold block">
            <i class="fas fa-user mr-2 text-blueGray-300 text-sm"></i> Profile
          </a>
        </li>
        <li class="items-center">
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="text-blueGray-700 hover:text-blueGray-500 text-xs uppercase py-3 font-bold block w-full text-left">
              <i class="fas fa-sign-out-alt mr-2 text-blueGray-300 text-sm"></i> Logout
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
