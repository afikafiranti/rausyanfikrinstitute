<nav
    class="relative z-10 w-full border-b border-slate-200 bg-white px-4 py-4 md:px-8">
    <div class="mx-auto flex w-full max-w-[1536px] items-center justify-between gap-3">

        {{-- Burger: mobile --}}
        <button type="button" aria-label="Buka navigasi" aria-controls="rf-mobile-drawer" aria-expanded="false" class="md:hidden p-3 -ml-2" onclick="rfToggleDrawer('rf-mobile-drawer', true)">
            <i class="fas fa-bars text-ink"></i>
        </button>

        {{-- Judul halaman dinamis --}}
        @php
            $r = Route::currentRouteName();
            $pageTitle = match (true) {
                $r === 'dashboard' => 'Dashboard',
                str_starts_with($r, 'reflections.review.') => 'Tinjau refleksi',
                str_starts_with($r, 'reflections.') => 'Refleksi saya',
                str_starts_with($r, 'materi.') => 'Materi',
                $r === 'laporan.review' => 'Review Laporan',
                str_starts_with($r, 'laporan.') => 'Laporan',
                str_starts_with($r, 'verification.') => 'Verifikasi',
                str_starts_with($r, 'alumni.') => 'Alumni',
                str_starts_with($r, 'notifications.') => 'Notifikasi',
                $r === 'profile.edit' => 'Profil',
                str_starts_with($r, 'management.post.') => 'Artikel & Refleksi',
                str_starts_with($r, 'management.galeri.') => 'Galeri',
                default => \Illuminate\Support\Str::headline(request()->segment(1)) ?: 'Dashboard',
            };
        @endphp
        <span class="min-w-0 flex-1 text-lg font-bold text-ink sm:text-xl">
            {{ $pageTitle }}
        </span>

        {{-- Avatar + badge + dropdown --}}
        @auth
            @php
                $unread = auth()->user()->unreadNotifications()->count();
                $user = $user ?? auth()->user();
                $avatar = $user->avatar_url ?? ($user->photo_url ?? null);
            @endphp
            <ul class="flex-col md:flex-row list-none items-center hidden md:flex">
                <li class="inline-block relative">
                    <button type="button" aria-label="Menu akun" aria-controls="user-dropdown" aria-expanded="false" class="text-blueGray-500 block" onclick="openDropdown(event,'user-dropdown')"
                        data-dropdown-trigger="user-dropdown">
                        <div class="items-center flex relative">
                            @if ($avatar)
                                <span
                                    class="w-12 h-12 inline-flex items-center justify-center rounded-full ring-2 ring-slate-200 overflow-hidden">
                                    <img alt="avatar" class="w-full h-full object-cover align-middle border-none"
                                        src="{{ $avatar }}?v={{ optional($user->updated_at)->timestamp }}"
                                        loading="lazy">
                                </span>
                            @else
                                <span
                                    class="w-12 h-12 inline-flex items-center justify-center rounded-full ring-2 ring-slate-200 bg-brand/10 text-brand">
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
                    </button>
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
                            <i class="fas fa-user mr-2"></i> Profil
                        </a>
                        <div class="h-0 my-2 border border-solid border-blueGray-100"></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full text-left text-sm py-2 px-4 text-blueGray-700">
                                <i class="fas fa-right-from-bracket mr-2"></i> Keluar
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        @endauth
    </div>
</nav>
