<nav class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4">
  <div class="w-full mx-auto items-center flex justify-between md:flex-nowrap flex-wrap md:px-10 px-4">
    <a class="text-white text-sm uppercase hidden lg:inline-block font-semibold" href="{{ route('dashboard') }}">Dashboard</a>

    {{-- Search (desktop) --}}
    <form class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
      <div class="relative flex w-full flex-wrap items-stretch">
        <span class="z-10 h-full leading-snug font-normal absolute text-center text-white/80 bg-transparent rounded text-base items-center justify-center w-8 pl-3 py-3">
          <i class="fas fa-search"></i>
        </span>
        <input type="text" placeholder="Search here..." class="border-0 px-3 py-3 placeholder-white/80 text-white relative bg-white/20 rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-10">
      </div>
    </form>

    {{-- Avatar (desktop) --}}
    <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
      @auth
      <details class="relative">
        <summary class="list-none cursor-pointer block">
          <div class="items-center flex">
            <span class="w-12 h-12 inline-flex items-center justify-center rounded-full">
              <img alt="avatar" class="w-12 h-12 rounded-full border-none shadow-lg"
                   src="{{ auth()->user()->photo_url ?? asset('images/avatar-default.png') }}">
            </span>
          </div>
        </summary>
        <div class="absolute right-0 mt-2 bg-white text-base z-50 py-2 list-none text-left rounded shadow-lg min-w-48 ring-1 ring-slate-900/5">
          <a href="{{ route('profile.show') }}" class="text-sm py-2 px-4 block text-slate-700 hover:bg-slate-50">
            <i class="fas fa-user mr-2"></i> Profile
          </a>
          <div class="h-0 my-2 border border-solid border-slate-100"></div>
          <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="text-sm py-2 px-4 w-full text-left text-red-600 hover:bg-slate-50">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
          </form>
        </div>
      </details>
      @endauth
    </ul>
  </div>
</nav>
