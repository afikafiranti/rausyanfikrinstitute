@extends('layouts.app')

@section('page-content')
    <div class="space-y-6">
        <header class="rounded-xl bg-ink px-5 py-7 text-white sm:px-8 sm:py-9">
            <p class="text-sm font-semibold text-slate-200">Beranda anggota</p>
            <h1 class="mt-3 break-words text-3xl font-bold leading-tight sm:text-4xl">Selamat datang, {{ $alumni->name }}</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-200 sm:text-base">
                Lihat status akun Anda dan lanjutkan kegiatan dari sini.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                @if ($canOpenMateri)
                    <a href="{{ route('materi.index') }}" class="rf-btn bg-white text-ink hover:bg-slate-100 focus-visible:outline-white">Mulai belajar</a>
                @else
                    <a href="{{ route('profile.edit') }}" class="rf-btn bg-white text-ink hover:bg-slate-100 focus-visible:outline-white">Lihat profil</a>
                @endif
            </div>
        </header>

        <div class="grid gap-4 lg:grid-cols-3">
            <section class="rf-card min-w-0 lg:col-span-2" aria-labelledby="status-akun">
                <h2 id="status-akun" class="text-lg font-semibold">Status akun</h2>
                @if ($alumni->status === \App\Models\User::STATUS_PENDING)
                    <p class="mt-4 font-semibold text-amber-800">Menunggu verifikasi</p>
                    <p class="mt-2 text-sm leading-6 text-muted">Data Anda sedang menunggu peninjauan. Periksa profil dan notifikasi untuk pembaruan status.</p>
                @elseif ($alumni->status === \App\Models\User::STATUS_SUSPENDED)
                    <p class="mt-4 font-semibold text-red-800">Akun ditangguhkan</p>
                    <p class="mt-2 text-sm leading-6 text-muted">Akses materi belum tersedia untuk akun ini. Periksa notifikasi atau hubungi pengelola.</p>
                @else
                    <p class="mt-4 font-semibold text-emerald-800">Akun aktif</p>
                    @if ($alumni->level_id)
                        <p class="mt-2 text-sm leading-6 text-muted">Anda dapat membuka materi sesuai level yang ditetapkan.</p>
                    @else
                        <p class="mt-2 text-sm leading-6 text-muted">Level materi belum ditetapkan. Periksa notifikasi untuk pembaruan dari pengelola.</p>
                    @endif
                @endif

                <dl class="mt-6 grid gap-4 border-t border-slate-200 pt-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-muted">Level materi</dt>
                        <dd class="mt-1 break-words font-semibold">{{ $alumni->level?->description ?: ($alumni->level?->name ?: 'Belum ditetapkan') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted">Wilayah</dt>
                        <dd class="mt-1 break-words font-semibold">{{ $alumni->wilayah?->name ?: 'Belum diisi' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rf-card min-w-0" aria-labelledby="profil-dasar">
                <h2 id="profil-dasar" class="text-lg font-semibold">Profil dasar</h2>
                @if ($missingProfile)
                    <p class="mt-4 font-semibold text-ink">Lengkapi data profil dasar</p>
                    <p class="mt-2 text-sm leading-6 text-muted">Belum diisi: {{ implode(', ', $missingProfile) }}. Data ini membantu pengelola mengenali akun Anda.</p>
                @else
                    <p class="mt-4 font-semibold text-emerald-800">Data profil dasar lengkap</p>
                    <p class="mt-2 text-sm leading-6 text-muted">Nomor telepon, angkatan, dan wilayah sudah terisi.</p>
                @endif
                <a href="{{ route('profile.edit') }}" class="rf-text-action mt-4">Buka profil <span aria-hidden="true">→</span></a>
            </section>
        </div>

        <section class="rf-card flex min-w-0 flex-col gap-4 border-l-4 border-l-accent sm:flex-row sm:items-center sm:justify-between" aria-labelledby="refleksi-pribadi">
            <div>
                <h2 id="refleksi-pribadi" class="text-lg font-semibold">Refleksi saya</h2>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-muted">Catat pelajaran dari materi atau kajian. Simpan untuk diri sendiri, atau kirim saat ingin mendapat tanggapan pengelola.</p>
            </div>
            <a href="{{ route('reflections.index') }}" class="rf-btn shrink-0 self-start sm:self-auto">Buka refleksi</a>
        </section>

        <section class="rf-card flex min-w-0 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" aria-labelledby="notifikasi-akun">
            <div>
                <h2 id="notifikasi-akun" class="text-lg font-semibold">Notifikasi</h2>
                <p class="mt-1 text-sm text-muted">
                    {{ $unreadCount ? $unreadCount.' belum dibaca' : 'Semua notifikasi sudah dibaca' }}
                </p>
            </div>
            <a href="{{ route('notifications.index') }}" class="rf-btn-secondary self-start sm:self-auto">Lihat notifikasi</a>
        </section>
    </div>
@endsection
