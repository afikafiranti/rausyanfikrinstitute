@extends('layouts.app')

@section('page-content')
    @php
        $canDeleteAlumniAccounts = auth()->user()?->hasAnyRole(['super_admin', 'admin']);
    @endphp

    <div class="rounded-xl bg-white p-6 shadow max-w-7xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Daftar Alumni</h2>

        <div class="mb-4">
            <button onclick="document.getElementById('modal-register-alumni').classList.remove('hidden')"
                class="rf-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Alumni
            </button>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <div class="font-semibold">Data alumni belum tersimpan.</div>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filter Section --}}
        <form method="GET" action="{{ route('alumni.index') }}" class="mb-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm text-slate-600">Cari</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Nama atau Email"
                    class="w-52 rounded-lg border-slate-300" />
            </div>

            <div>
                <label class="block text-sm text-slate-600">Angkatan</label>
                <select name="angkatan" class="rounded-lg border-slate-300">
                    <option value="">Semua</option>
                    @foreach ($angkatanList as $item)
                        <option value="{{ $item }}" @selected($angkatan == $item)>{{ $item }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-slate-600">Wilayah</label>
                <select name="wilayah_id" class="rounded-lg border-slate-300">
                    <option value="">Semua</option>
                    @foreach ($wilayah as $w)
                        <option value="{{ $w->id }}" @selected($wilayahId == $w->id)>{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm text-slate-600">Level</label>
                <select name="level_id" class="rounded-lg border-slate-300">
                    <option value="">Semua</option>
                    @foreach ($level as $l)
                        <option value="{{ $l->id }}" @selected($levelId == $l->id)>{{ $l->description }}</option>
                    @endforeach
                </select>
            </div>

            {{-- FILTER AB --}}
            <div>
                <label class="block text-sm text-slate-600">AB</label>
                <select name="ab" class="rounded-lg border-slate-300">
                    <option value="">Semua</option>
                    <option value="iya" @selected($ab == 'iya')>Iya</option>
                    <option value="tidak" @selected($ab == 'tidak')>Tidak</option>
                </select>
            </div>

            <button class="rf-btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-search mr-1"></i> Filter
            </button>

            @if ($hasFilters)
                <a href="{{ route('alumni.index') }}"
                    class="rf-btn border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg inline-flex items-center gap-2">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-600">
            <div>
                Menampilkan <span class="font-semibold text-slate-800">{{ $alumni->total() }}</span>
                dari <span class="font-semibold text-slate-800">{{ $totalAlumni }}</span> alumni.
            </div>

            @if ($hasFilters)
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-slate-500">Filter aktif:</span>
                    @foreach ($activeFilters as $filter)
                        <span class="rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                            {{ $filter['label'] }}: {{ $filter['value'] }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-slate-200 text-sm">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Angkatan</th>
                        <th class="px-4 py-3 text-left">Wilayah</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Level Materi</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($alumni as $item)
                        <tr class="border-t hover:bg-slate-50">

                            {{-- Nomor --}}
                            <td class="px-4 py-2">
                                {{ $alumni->firstItem() + $loop->index }}
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 py-2 font-medium">{{ $item->name }}</td>

                            {{-- Angkatan --}}
                            <td class="px-4 py-2">{{ $item->angkatan ?? '-' }}</td>

                            {{-- Wilayah --}}
                            <td class="px-4 py-2">
                                {{ optional($item->wilayah)->name ?? '-' }}
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-2">
                                @if ($item->roles->count())
                                    @foreach ($item->roles as $role)
                                        @switch($role->name)
                                            @case('super_admin')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-semibold bg-rose-200 text-rose-800 mr-1">
                                                    Super Admin
                                                </span>
                                            @break

                                            @case('admin')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-200 text-amber-800 mr-1">
                                                    Admin
                                                </span>
                                            @break

                                            @case('koorda')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-semibold bg-violet-200 text-violet-800 mr-1">
                                                    Koorda
                                                </span>
                                            @break

                                            @case('alumni')
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-semibold bg-sky-200 text-sky-800 mr-1">
                                                    Alumni
                                                </span>
                                            @break

                                            @default
                                                <span
                                                    class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-800 mr-1">
                                                    {{ $role->name }}
                                                </span>
                                        @endswitch
                                    @endforeach
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            {{-- Level Materi --}}
                            <td class="px-4 py-2">
                                <span class="rf-badge bg-slate-100 text-slate-700">
                                    {{ optional($item->level)->description ?? '-' }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-2 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    <a href="{{ route('alumni.show', $item->id) }}"
                                        class="rf-btn inline-flex h-8 w-8 items-center justify-center bg-indigo-600 p-0 text-sm text-white hover:bg-indigo-700 rounded-lg"
                                        title="Detail {{ $item->name }}" aria-label="Detail {{ $item->name }}">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if ($canDeleteAlumniAccounts && $item->hasAnyRole(['alumni', 'koorda']) && !$item->hasAnyRole(['super_admin', 'admin']))
                                        <a href="{{ route('alumni.edit', $item) }}"
                                            class="rf-btn inline-flex h-8 w-8 items-center justify-center bg-amber-500 p-0 text-sm text-white hover:bg-amber-600 rounded-lg"
                                            title="Edit {{ $item->name }}" aria-label="Edit {{ $item->name }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endif

                                    @if ($canDeleteAlumniAccounts && !$item->is(auth()->user()) && $item->hasAnyRole(['alumni', 'koorda']) && !$item->hasAnyRole(['super_admin', 'admin']))
                                        <form method="POST" action="{{ route('alumni.destroy', $item) }}"
                                            onsubmit="return confirm('Hapus akun {{ $item->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rf-btn inline-flex h-8 w-8 items-center justify-center bg-red-600 p-0 text-sm text-white hover:bg-red-700 rounded-lg"
                                                title="Hapus {{ $item->name }}" aria-label="Hapus {{ $item->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-slate-500">
                                    @if ($hasFilters)
                                        <div class="font-semibold text-slate-700">Tidak ada alumni yang cocok dengan filter.</div>
                                        <div class="mt-1">Coba ubah kata kunci atau reset filter untuk melihat semua data alumni.</div>
                                        <a href="{{ route('alumni.index') }}"
                                            class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                            <i class="fas fa-rotate-left"></i> Reset Filter
                                        </a>
                                    @else
                                        <div class="font-semibold text-slate-700">Belum ada data alumni.</div>
                                        <div class="mt-1">Gunakan tombol Tambah Alumni untuk membuat data pertama.</div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $alumni->links() }}
            </div>
        </div>

        {{-- Modal --}}
        <div id="modal-register-alumni"
            class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

            <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-lg relative">
                <button onclick="document.getElementById('modal-register-alumni').classList.add('hidden')"
                    class="absolute right-3 top-3 text-slate-500 hover:text-slate-700">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <h2 class="text-xl font-semibold mb-4">Tambah Alumni Baru</h2>

                <form method="POST" action="{{ route('alumni.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-sm mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-slate-300">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-slate-300">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm mb-1">Password</label>
                            <input type="password" name="password" required class="w-full rounded-lg border-slate-300">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Ulangi Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full rounded-lg border-slate-300">
                        </div>
                    </div>

                    <button class="rf-btn w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg">
                        Buat Akun
                    </button>
                </form>
            </div>
        </div>
    @endsection
