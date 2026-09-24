@extends('layouts.app')

@section('page-content')
    @php
        $roleNames = $alumni->roles
            ->pluck('name')
            ->map(fn ($role) => str_replace('_', ' ', $role))
            ->implode(', ');
    @endphp

    @if (session('success'))
        <div class="mx-auto mb-4 max-w-5xl rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-auto mb-4 max-w-5xl rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-5xl mx-auto bg-white shadow rounded-2xl overflow-hidden mt-6">
        {{-- Header dengan foto dan info utama --}}
        <div
            class="flex flex-col md:flex-row items-center md:items-start gap-6 p-6 bg-gradient-to-r from-slate-200 to-slate-100 text-slate-800">
            {{-- Foto Profil --}}
            <div class="flex-shrink-0">
                <img src="{{ $alumni->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($alumni->name) . '&background=9CA3AF&color=fff&size=200' }}"
                    alt="Foto {{ $alumni->name }}"
                    class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md">
            </div>

            {{-- Info dasar --}}
            <div class="flex-1 text-center md:text-left">
                <h2 class="text-3xl font-semibold mb-1">{{ $alumni->name }}</h2>
                <p class="text-slate-500 text-sm mb-3">Angkatan {{ $alumni->angkatan ?? '-' }}</p>

                {{-- Status --}}
                @php
                    $badgeColor =
                        [
                            'active' => 'green',
                            'pending' => 'yellow',
                            'suspended' => 'red',
                        ][$alumni->status] ?? 'blue';
                @endphp
                <span
                    class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-800 capitalize">
                    {{ $alumni->status }}
                </span>

                @if ($canEditProfile)
                    <div class="mt-4">
                        <a href="{{ route('alumni.edit', $alumni) }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            <i class="fas fa-pen"></i> Edit Data
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Detail Data --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm text-slate-500">Email</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->email ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">No. Telepon</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->phone ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Tempat, Tanggal Lahir</h4>
                    <p class="font-medium text-slate-800">
                        {{ $alumni->tempat_lahir ?? '-' }},
                        {{ $alumni->tanggal_lahir ? \Carbon\Carbon::parse($alumni->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Wilayah</h4>
                    <p class="font-medium text-slate-800">{{ optional($alumni->wilayah)->name ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Role Akun</h4>
                    <p class="font-medium text-slate-800">{{ $roleNames ?: '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Level Materi</h4>
                    <p class="font-medium text-slate-800">{{ optional($alumni->level)->description ?? '-' }}</p>

                    @if ($canUpdateLevel)
                        <form method="POST" action="{{ route('alumni.level.update', $alumni) }}"
                            class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="level_id" class="block text-sm text-slate-500">Ubah Level Materi</label>
                                <select id="level_id" name="level_id" class="mt-1 w-full rounded-lg border-slate-300 sm:w-48">
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}" @selected((int) old('level_id', $alumni->level_id) === $level->id)>
                                            {{ $level->description ?? $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h4 class="text-sm text-slate-500">Pekerjaan</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->pekerjaan ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Pendidikan Terakhir</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->pendidikan_terakhir ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Kampus</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->kampus ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Status Pernikahan</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->status_pernikahan ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm text-slate-500">Status AB</h4>
                    <p class="font-medium text-slate-800">{{ $alumni->ab ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Tombol kembali --}}
        <div class="p-6 border-t text-center bg-slate-50">
            <a href="{{ route('alumni.index') }}"
                class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg shadow-sm border border-slate-200 transition">
                ← Kembali ke Daftar Alumni
            </a>
        </div>
    </div>
@endsection
