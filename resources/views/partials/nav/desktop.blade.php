<aside class="hidden md:flex md:flex-col md:w-60 md:border-r md:bg-white">
    <div class="p-4 font-semibold">Rausyan Fikr</div>

    <nav class="px-2 pb-4 space-y-1 text-sm">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50
              {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
            <i class="fas fa-tv text-base" aria-hidden="true"></i>
            <span>Dashboard</span>
        </a>

        {{-- Materi --}}
        <a href="{{ route('materi.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50
              {{ request()->routeIs('materi.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
            <i class="fas fa-list text-base" aria-hidden="true"></i>
            <span>Materi</span>
        </a>

        {{-- Laporan --}}
        <a href="{{ route('laporan.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50
              {{ request()->routeIs('laporan.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
            <i class="fas fa-file-alt text-base" aria-hidden="true"></i>
            <span>Laporan</span>
        </a>

        {{-- Profil --}}
        <a href="{{ route('profile.show') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50
              {{ request()->routeIs('profile.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
            <i class="fas fa-user text-base" aria-hidden="true"></i>
            <span>Profil</span>
        </a>
        @can('review-user')
            <a href="{{ route('verification.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50
            {{ request()->routeIs('verification.*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-700' }}">
                <i class="fas fa-user-check text-base" aria-hidden="true"></i>
                <span>Verifikasi</span>
            </a>
        @endcan

    </nav>
</aside>
