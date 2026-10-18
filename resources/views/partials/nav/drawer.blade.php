@php
    $link = fn($active) => $active
        ? 'rf-nav-link rf-nav-active'
        : 'rf-nav-link rf-nav-idle';
@endphp

<div id="rf-mobile-drawer" role="dialog" aria-modal="true" aria-label="Navigasi" aria-hidden="true" class="fixed inset-0 z-[60] hidden">
    {{-- backdrop --}}
    <div class="absolute inset-0 bg-black/30" onclick="rfToggleDrawer('rf-mobile-drawer', false)"></div>

    {{-- panel --}}
    <aside class="absolute left-0 top-0 h-full w-72 max-w-full bg-white shadow-xl overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <span class="font-bold uppercase">Navigasi</span>
            <button type="button" aria-label="Tutup navigasi" class="p-3" onclick="rfToggleDrawer('rf-mobile-drawer', false)"><i
                    class="fas fa-times"></i></button>
        </div>

        <nav class="space-y-1 p-3">
            <a href="{{ route('dashboard') }}" class="{{ $link(request()->routeIs('dashboard')) }}">
                <i class="fas fa-tv w-5 text-center"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('materi.index') }}" class="{{ $link(request()->routeIs('materi.*')) }}">
                <i class="fas fa-book-open w-5 text-center"></i><span>Materi</span>
            </a>
            @if (auth()->user()->hasAnyRole(['alumni', 'koorda', 'admin', 'super_admin']))
                <a href="{{ route('reflections.index') }}" class="{{ $link(request()->routeIs('reflections.*') && ! request()->routeIs('reflections.review.*')) }}">
                    <i class="fas fa-pen w-5 text-center" aria-hidden="true"></i><span>Refleksi saya</span>
                </a>
            @endif
            @can('review-report')
                <a href="{{ route('reflections.review.index') }}" class="{{ $link(request()->routeIs('reflections.review.*')) }}">
                    <i class="fas fa-comment w-5 text-center" aria-hidden="true"></i><span>Tinjau refleksi</span>
                </a>
            @endcan
            @can('review-report')
            <a href="{{ route('laporan.index') }}" class="{{ $link(request()->routeIs('laporan.index')) }}">
                <i class="fas fa-file-lines w-5 text-center"></i><span>Laporan</span>
            </a>
            @endcan
            @can('review-report')
                <a href="{{ route('laporan.review') }}" class="{{ $link(request()->routeIs('laporan.review')) }}">
                    <i class="fas fa-clipboard-check w-5 text-center"></i><span>Review Laporan</span>
                </a>
            @endcan
            @can('review-user')
                <a href="{{ route('verification.index') }}" class="{{ $link(request()->routeIs('verification.*')) }}">
                    <i class="fas fa-user-check w-5 text-center"></i><span>Verifikasi</span>
                </a>
            @endcan
            @can('view-alumni')
                <a href="{{ route('alumni.index') }}" class="{{ $link(request()->routeIs('alumni.*')) }}">
                    <i class="fas fa-user-graduate w-5 text-center"></i><span>Alumni</span>
                </a>
            @endcan
            @can('manage-content')
                @if (auth()->user()->hasRole('super_admin'))
                    <a href="{{ route('management.post.index') }}" class="{{ $link(request()->routeIs('management.post.*')) }}">
                        <i class="fas fa-newspaper w-5 text-center"></i><span>Artikel &amp; Refleksi</span>
                    </a>
                    <a href="{{ route('management.galeri.index') }}" class="{{ $link(request()->routeIs('management.galeri.*')) }}">
                        <i class="fas fa-images w-5 text-center"></i><span>Galeri</span>
                    </a>
                @endif
            @endcan
            @auth
                <a href="{{ route('profile.edit') }}" class="{{ $link(request()->routeIs('profile.edit')) }}">
                    <i class="fas fa-user-circle w-5 text-center"></i><span>Profil</span>
                </a>
                <a href="{{ route('notifications.index') }}" class="{{ $link(request()->routeIs('notifications.*')) }}">
                    <i class="fas fa-bell w-5 text-center"></i><span>Notifikasi</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="px-4 py-3">
                    @csrf
                    <button class="rf-nav-link rf-nav-idle w-full" type="submit">
                        <i class="fas fa-right-from-bracket w-5 text-center"></i><span>Keluar</span>
                    </button>
                </form>
            @endauth
        </nav>
    </aside>
</div>
