@extends('layouts.app')

@php
    $avatar = $user->avatar_url ?? ($user->photo_url ?? null);
    $badge = ['active' => 'green', 'pending' => 'yellow', 'suspended' => 'red'][$user->status] ?? 'blue';
@endphp

@section('page-content')

    @if (session('success'))
        <div class="rf-card mb-4 bg-green-50">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="rf-card mb-4 bg-red-50">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =============== DESKTOP (≥ md) =============== --}}
    <div class="hidden md:block">


        <div class="grid grid-cols-12 gap-4">
            <section class="col-span-12 rounded-xl bg-white p-4 shadow flex items-center gap-4">
                @if ($avatar)
                    <img class="h-16 w-16 rounded-xl object-cover"
                        src="{{ $avatar }}?v={{ optional($user->updated_at)->timestamp }}" alt="avatar">
                @else
                    <span
                        class="h-16 w-16 rounded-xl bg-blueGray-200 text-blueGray-500 inline-flex items-center justify-center">
                        <i class="fas fa-user-circle text-3xl"></i>
                    </span>
                @endif
                <div class="flex-1">
                    <div class="font-semibold">{{ $user->name }}</div>
                    <div class="text-sm text-slate-500">
                        {{ $user->angkatan ?: 'Angkatan ?' }} • {{ optional($user->wilayah)->name ?: 'Wilayah ?' }}
                    </div>
                    <span
                        class="rf-badge bg-{{ $badge }}-100 text-{{ $badge }}-800 capitalize mt-1 inline-block">{{ $user->status }}</span>
                </div>
            </section>

            <section class="col-span-8 rounded-xl bg-white p-4 shadow">
                <h3 class="font-semibold mb-3">Info Pribadi</h3>
                <form id="formProfile" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                    class="grid grid-cols-2 gap-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" required>
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" required>
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">No. WA</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="+62...">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        @error('pekerjaan')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Angkatan</label>
                        <input type="text" name="angkatan" value="{{ old('angkatan', $user->angkatan) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        @error('angkatan')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Wilayah</label>
                        <select name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            @foreach ($wilayah as $w)
                                <option value="{{ $w->id }}" @selected(old('wilayah_id', $user->wilayah_id) == $w->id)>{{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('wilayah_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1">
                        <label class="block text-sm mb-1">Foto</label>
                        <input type="file" name="photo" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                            accept="image/*">
                        @error('photo')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-1 flex justify-end pt-6">
                        <button form="formProfile" class="rf-btn">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>

            <section class="col-span-4 rounded-xl bg-white p-4 shadow">
                <h3 class="font-semibold mb-3">Status Akun</h3>
                <p class="text-sm text-slate-600">
                    Mengubah <b>Wilayah</b> atau <b>Angkatan</b> akan mengubah status menjadi
                    <span class="rf-badge bg-yellow-100 text-yellow-800">pending</span> sampai diverifikasi Koorda.
                </p>
            </section>
        </div>
    </div>

    {{-- =============== MOBILE (< md) =============== --}}
    <div class="md:hidden">
        <h2 class="text-xl font-semibold">Profil</h2>

        <div class="rf-section mt-3">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf @method('PUT')

                <div class="flex items-center gap-4">
                    @if ($avatar)
                        <img class="h-16 w-16 rounded-xl object-cover"
                            src="{{ $avatar }}?v={{ optional($user->updated_at)->timestamp }}" alt="avatar">
                    @else
                        <span
                            class="h-16 w-16 rounded-xl bg-blueGray-200 text-blueGray-500 inline-flex items-center justify-center">
                            <i class="fas fa-user-circle text-3xl"></i>
                        </span>
                    @endif
                    <input type="file" name="photo" class="flex-1 rounded-lg border border-blueGray-200 px-3 py-2"
                        accept="image/*">
                </div>

                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Nama" required>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Email" required>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="No. WA">
                <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Pekerjaan">
                <input type="text" name="angkatan" value="{{ old('angkatan', $user->angkatan) }}"
                    class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Angkatan">

                <select name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    <option value="">Wilayah</option>
                    @foreach ($wilayah as $w)
                        <option value="{{ $w->id }}" @selected(old('wilayah_id', $user->wilayah_id) == $w->id)>{{ $w->name }}</option>
                    @endforeach
                </select>

                <button class="rf-btn w-full">Simpan</button>
            </form>

            <p class="text-xs text-slate-500 mt-2">
                Ubah Wilayah/Angkatan → status <b>pending</b> (menunggu verifikasi Koorda).
            </p>
        </div>
    </div>
@endsection
