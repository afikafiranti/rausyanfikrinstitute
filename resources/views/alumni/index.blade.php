@extends('layouts.app')

@section('page-content')
    <div class="rounded-xl bg-white p-6 shadow max-w-7xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Daftar Alumni</h2>

        <div class="mb-4">
            <button onclick="document.getElementById('modal-register-alumni').classList.remove('hidden')"
                class="rf-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Alumni
            </button>
        </div>



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

            <button class="rf-btn bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
        </form>

        {{-- Table Section --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border border-slate-200 text-sm">
                <thead class="bg-slate-100 text-slate-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Angkatan</th>
                        <th class="px-4 py-3 text-left">Wilayah</th>
                        <th class="px-4 py-3 text-left">Level</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($alumni as $item)
                        <tr class="border-t hover:bg-slate-50">
                            {{-- Nomor urut global berdasarkan pagination --}}
                            <td class="px-4 py-2">
                                {{ $alumni->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                            <td class="px-4 py-2">{{ $item->email }}</td>
                            <td class="px-4 py-2">{{ $item->angkatan ?? '-' }}</td>
                            <td class="px-4 py-2">{{ optional($item->wilayah)->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ optional($item->level)->description ?? '-' }}</td>

                            <td class="px-4 py-2">
                                @php
                                    $badge =
                                        [
                                            'active' => 'green',
                                            'pending' => 'yellow',
                                            'suspended' => 'red',
                                        ][$item->status] ?? 'blue';
                                @endphp
                                <span class="rf-badge bg-{{ $badge }}-100 text-{{ $badge }}-800 capitalize">
                                    {{ $item->status }}
                                </span>
                            </td>

                            <td class="px-4 py-2 text-center">
                                <a href="{{ route('alumni.show', $item->id) }}"
                                    class="rf-btn text-sm px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-slate-500">
                                Belum ada data alumni.
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

    <!-- Modal Register Alumni -->
    <div id="modal-register-alumni"
        class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm flex items-center justify-center z-50">

        <div class="bg-white rounded-xl w-full max-w-md p-6 shadow-lg relative">

            <!-- Close Button -->
            <button onclick="document.getElementById('modal-register-alumni').classList.add('hidden')"
                class="absolute right-3 top-3 text-slate-500 hover:text-slate-700">
                <i class="fas fa-times text-xl"></i>
            </button>

            <h2 class="text-xl font-semibold mb-4">Tambah Alumni Baru</h2>

            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-sm mb-1">Nama</label>
                    <input type="text" name="name" required class="w-full rounded-lg border-slate-300">
                </div>

                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" name="email" required class="w-full rounded-lg border-slate-300">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1">Password</label>
                        <input type="password" name="password" required class="w-full rounded-lg border-slate-300">
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
