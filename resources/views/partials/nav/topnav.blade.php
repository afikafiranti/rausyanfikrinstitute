<nav
    class="absolute top-0 left-0 w-full z-10 bg-transparent md:flex-row md:flex-nowrap md:justify-start flex items-center p-4">
    <div class="w-full mx-auto items-center flex justify-between md:flex-nowrap flex-wrap md:px-10 px-4">

        {{-- Burger: mobile --}}
        <button class="md:hidden p-2 -ml-2" onclick="rfToggleDrawer('rf-mobile-drawer', true)">
            <i class="fas fa-bars text-white"></i>
        </button>

        {{-- Judul halaman dinamis --}}
        @php
            $r = Route::currentRouteName();
            $pageTitle = match (true) {
                $r === 'dashboard' => 'Dashboard',
                str_starts_with($r, 'materi.') => 'Materi',
                $r === 'laporan.review' => 'Review Laporan',
                str_starts_with($r, 'laporan.') => 'Laporan',
                str_starts_with($r, 'verification.') => 'Verifikasi',
                str_starts_with($r, 'alumni.') => 'Alumni',
                str_starts_with($r, 'notifications.') => 'Notifikasi',
                $r === 'profile.edit' => 'Profil',
                default => \Illuminate\Support\Str::headline(request()->segment(1)) ?: 'Dashboard',
            };
        @endphp
        <span class="text-white text-lg uppercase hidden lg:inline-block font-semibold">
            {{ $pageTitle }}
        </span>

        {{-- Search opsional --}}
        <form class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
            <div class="relative flex w-full flex-wrap items-stretch">
                <span
                    class="z-10 h-full leading-snug font-normal absolute text-center text-blueGray-300 bg-transparent rounded text-base
                     items-center justify-center w-8 pl-3 py-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" placeholder="Search here..."
                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow outline-none
                      focus:outline-none focus:ring w-full pl-10" />
            </div>
        </form>

        {{-- Avatar + badge + dropdown --}}
        @auth
            @php
                $unread = auth()->user()->unreadNotifications()->count();
                $user = $user ?? auth()->user();
                $avatar = $user->avatar_url ?? ($user->photo_url ?? null);
            @endphp
            <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
                <li class="inline-block relative">
                    <a href="#" class="text-blueGray-500 block" onclick="openDropdown(event,'user-dropdown')"
                        data-dropdown-trigger="user-dropdown">
                        <div class="items-center flex relative">
                            @if ($avatar)
                                <span
                                    class="w-12 h-12 inline-flex items-center justify-center rounded-full ring-2 ring-white overflow-hidden">
                                    <img alt="avatar" class="w-full h-full object-cover align-middle border-none"
                                        src="{{ $avatar }}?v={{ optional($user->updated_at)->timestamp }}"
                                        alt="avatar" loading="lazy">
                                </span>
                            @else
                                <span
                                    class="w-12 h-12 inline-flex items-center justify-center rounded-full ring-2 ring-white bg-blueGray-200 text-blueGray-500">
                                    <i class="fas fa-user-circle text-2xl"></i>
                                </span>
                            @endif

                            @if ($unread > 0)
                                <span
                                    class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 min-w-[20px] px-1 text-[11px] rounded-full bg-red-600 text-white">
                                    {{ $unread > 99 ? '99+' : $unread }}
                                </span>
                            @endif
                        </div>
                    </a>
                    <div id="user-dropdown"
                        class="hidden absolute right-0 mt-2 bg-white text-base z-50 py-2 list-none text-left rounded shadow-lg min-w-48">
                        <a href="{{ route('notifications.index') }}"
                            class="text-sm py-2 px-4 block w-full text-blueGray-700">
                            <i class="fas fa-bell mr-2"></i> Notifikasi
                            @if ($unread > 0)
                                <span
                                    class="ml-2 inline-flex text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700">{{ $unread }}</span>
                            @endif
                        </a>
                        <a href="{{ route('profile.edit') }}" class="text-sm py-2 px-4 block w-full text-blueGray-700">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <div class="h-0 my-2 border border-solid border-blueGray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full text-left text-sm py-2 px-4 text-blueGray-700">
                                <i class="fas fa-right-from-bracket mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        @endauth
    </div>
</nav>
