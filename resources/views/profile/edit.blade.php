@extends('layouts.app')

@php
    $avatar = $user->avatar_url ?? ($user->photo_url ?? null);
    $badge = ['active' => 'green', 'pending' => 'yellow', 'suspended' => 'red'][$user->status] ?? 'blue';
    $pwRoute = route('profile.updatePassword');
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

    {{-- DESKTOP --}}
    <div class="hidden md:block">
        <div class="grid grid-cols-12 gap-4">

            {{-- Header Profil --}}
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
                        class="rf-badge bg-{{ $badge }}-100 text-{{ $badge }}-800 capitalize mt-1 inline-block">
                        {{ $user->status }}
                    </span>
                </div>
                <label for="desktop-photo" class="rf-btn cursor-pointer"><i class="fas fa-image"></i> Ubah Foto</label>
            </section>

            {{-- Info Pribadi --}}
            <section class="col-span-8 rounded-xl bg-white p-4 shadow">
                <h3 class="font-semibold mb-3">Info Pribadi</h3>
                <form id="formProfile" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                    class="grid grid-cols-2 gap-4">
                    @csrf @method('PATCH')

                    {{-- Foto --}}
                    <div class="col-span-2 hidden">
                        <input id="desktop-photo" type="file" name="photo" accept="image/*"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="desktop-name" class="block text-sm mb-1">Nama Lengkap</label>
                        <input id="desktop-name" type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" required>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="desktop-email" class="block text-sm mb-1">Email</label>
                        <input id="desktop-email" type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" required>
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label for="desktop-phone" class="block text-sm mb-1">No. WA</label>
                        <input id="desktop-phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="+62...">
                    </div>

                    {{-- Tempat Lahir --}}
                    <div class="grid grid-cols-2 gap-2 ">
                        <div>
                            <label for="desktop-tempat-lahir" class="block text-sm mb-1">Tempat Lahir</label>
                            <input id="desktop-tempat-lahir" type="text" name="tempat_lahir"
                                value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                                class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        </div>
                        <div>
                            <label for="desktop-tanggal-lahir" class="block text-sm mb-1">Tanggal Lahir</label>
                            <input id="desktop-tanggal-lahir" type="date" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}"
                                class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        </div>
                    </div>

                    {{-- Pekerjaan --}}
                    <div>
                        <label for="desktop-pekerjaan" class="block text-sm mb-1">Pekerjaan</label>
                        <input id="desktop-pekerjaan" type="text" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>

                    {{-- Pendidikan --}}
                    <div>
                        <label for="desktop-pendidikan-terakhir" class="block text-sm mb-1">Pendidikan Terakhir</label>
                        <input id="desktop-pendidikan-terakhir" type="text" name="pendidikan_terakhir"
                            value="{{ old('pendidikan_terakhir', $user->pendidikan_terakhir) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>

                    {{-- Kampus --}}
                    <div>
                        <label for="desktop-kampus" class="block text-sm mb-1">Kampus</label>
                        <input id="desktop-kampus" type="text" name="kampus" value="{{ old('kampus', $user->kampus) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>

                    {{-- Status Pernikahan --}}
                    <div>
                        <label for="desktop-status-pernikahan" class="block text-sm mb-1">Status Pernikahan</label>
                        <select id="desktop-status-pernikahan" name="status_pernikahan" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            <option value="lajang" @selected(old('status_pernikahan', $user->status_pernikahan) == 'lajang')>Lajang</option>
                            <option value="menikah" @selected(old('status_pernikahan', $user->status_pernikahan) == 'menikah')>Menikah</option>
                            <option value="duda/janda" @selected(old('status_pernikahan', $user->status_pernikahan) == 'duda/janda')>Duda / Janda</option>
                        </select>
                    </div>

                    {{-- AB --}}
                    <div>
                        <label for="desktop-ab" class="block text-sm mb-1">AB</label>
                        <select id="desktop-ab" name="ab" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            <option value="iya" @selected(old('ab', $user->ab) == 'iya')>Iya</option>
                            <option value="tidak" @selected(old('ab', $user->ab) == 'tidak')>Tidak</option>
                        </select>
                    </div>

                    {{-- Angkatan --}}
                    <div>
                        <label for="desktop-angkatan" class="block text-sm mb-1">Angkatan</label>
                        <input id="desktop-angkatan" type="text" name="angkatan" value="{{ old('angkatan', $user->angkatan) }}"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>

                    {{-- Wilayah --}}
                    <div>
                        <label for="desktop-wilayah" class="block text-sm mb-1">Wilayah</label>
                        <select id="desktop-wilayah" name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            @foreach ($wilayah as $w)
                                <option value="{{ $w->id }}" @selected(old('wilayah_id', $user->wilayah_id) == $w->id)>
                                    {{ $w->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-span-2 flex justify-end pt-6">
                        <button form="formProfile" class="rf-btn">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </section>

            {{-- Keamanan Akun --}}
            <section class="col-span-4 rounded-xl bg-white p-4 shadow">
                <h3 class="font-semibold mb-3">Keamanan</h3>
                <form method="POST" action="{{ $pwRoute }}" class="space-y-3">
                    @csrf
                    @if ($pwRoute !== '#')
                        @method('PUT')
                    @endif
                    <div>
                        <label for="desktop-current-password" class="block text-sm mb-1">Password Saat Ini</label>
                        <input id="desktop-current-password" type="password" name="current_password"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                            autocomplete="current-password">
                    </div>
                    <div>
                        <label for="desktop-password" class="block text-sm mb-1">Password Baru</label>
                        <input id="desktop-password" type="password" name="password"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" autocomplete="new-password">
                    </div>
                    <div>
                        <label for="desktop-password-confirmation" class="block text-sm mb-1">Ulangi Password Baru</label>
                        <input id="desktop-password-confirmation" type="password" name="password_confirmation"
                            class="w-full rounded-lg border border-blueGray-200 px-3 py-2" autocomplete="new-password">
                    </div>
                    <button class="rf-btn w-full">Ganti Password</button>
                </form>
            </section>

        </div>
    </div>

    {{-- MOBILE --}}
    <div class="md:hidden">
        <h2 class="text-xl font-semibold">Profil</h2>

        <div class="rf-section mt-3">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                class="space-y-3">
                @csrf @method('PATCH')

                <div class="flex items-center gap-4">
                    @if ($avatar)
                        <img class="h-16 w-16 shrink-0 rounded-xl object-cover"
                            src="{{ $avatar }}?v={{ optional($user->updated_at)->timestamp }}" alt="avatar">
                    @else
                        <span
                            class="h-16 w-16 shrink-0 rounded-xl bg-blueGray-200 text-blueGray-500 inline-flex items-center justify-center">
                            <i class="fas fa-user-circle text-3xl"></i>
                        </span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <label for="mobile-photo" class="block text-sm mb-1">Foto Profil</label>
                        <input id="mobile-photo" type="file" name="photo"
                            class="w-full min-w-0 rounded-lg border border-blueGray-200 px-3 py-2" accept="image/*">
                    </div>
                </div>

                <div>
                    <label for="mobile-name" class="block text-sm mb-1">Nama Lengkap</label>
                    <input id="mobile-name" type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Nama">
                </div>
                <div>
                    <label for="mobile-email" class="block text-sm mb-1">Email</label>
                    <input id="mobile-email" type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Email">
                </div>
                <div>
                    <label for="mobile-phone" class="block text-sm mb-1">No. WA</label>
                    <input id="mobile-phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="No. WA">
                </div>
                <div>
                    <label for="mobile-tempat-lahir" class="block text-sm mb-1">Tempat Lahir</label>
                    <input id="mobile-tempat-lahir" type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Tempat Lahir">
                </div>
                <div>
                    <label for="mobile-tanggal-lahir" class="block text-sm mb-1">Tanggal Lahir</label>
                    <input id="mobile-tanggal-lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                </div>
                <div>
                    <label for="mobile-pekerjaan" class="block text-sm mb-1">Pekerjaan</label>
                    <input id="mobile-pekerjaan" type="text" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Pekerjaan">
                </div>
                <div>
                    <label for="mobile-pendidikan-terakhir" class="block text-sm mb-1">Pendidikan Terakhir</label>
                    <input id="mobile-pendidikan-terakhir" type="text" name="pendidikan_terakhir"
                        value="{{ old('pendidikan_terakhir', $user->pendidikan_terakhir) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Pendidikan">
                </div>
                <div>
                    <label for="mobile-kampus" class="block text-sm mb-1">Kampus</label>
                    <input id="mobile-kampus" type="text" name="kampus" value="{{ old('kampus', $user->kampus) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Kampus">
                </div>
                <div>
                    <label for="mobile-status-pernikahan" class="block text-sm mb-1">Status Pernikahan</label>
                    <select id="mobile-status-pernikahan" name="status_pernikahan" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        <option value="">Status Pernikahan</option>
                        <option value="lajang" @selected(old('status_pernikahan', $user->status_pernikahan) == 'lajang')>Lajang</option>
                        <option value="menikah" @selected(old('status_pernikahan', $user->status_pernikahan) == 'menikah')>Menikah</option>
                        <option value="duda/janda" @selected(old('status_pernikahan', $user->status_pernikahan) == 'duda/janda')>Duda / Janda</option>
                    </select>
                </div>

                {{-- AB MOBILE --}}
                <div>
                    <label for="mobile-ab" class="block text-sm mb-1">AB</label>
                    <select id="mobile-ab" name="ab" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        <option value="">AB</option>
                        <option value="iya" @selected(old('ab', $user->ab) == 'iya')>Iya</option>
                        <option value="tidak" @selected(old('ab', $user->ab) == 'tidak')>Tidak</option>
                    </select>
                </div>

                <div>
                    <label for="mobile-angkatan" class="block text-sm mb-1">Angkatan</label>
                    <input id="mobile-angkatan" type="text" name="angkatan" value="{{ old('angkatan', $user->angkatan) }}"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Angkatan">
                </div>

                <div>
                    <label for="mobile-wilayah" class="block text-sm mb-1">Wilayah</label>
                    <select id="mobile-wilayah" name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                        <option value="">Wilayah</option>
                        @foreach ($wilayah as $w)
                            <option value="{{ $w->id }}" @selected(old('wilayah_id', $user->wilayah_id) == $w->id)>
                                {{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="rf-btn w-full">Simpan</button>
            </form>
        </div>

        {{-- Form Keamanan --}}
        <div class="rf-section mt-4">
            <h3 class="font-semibold mb-3">Keamanan</h3>
            <form method="POST" action="{{ $pwRoute }}" class="space-y-3">
                @csrf
                @if ($pwRoute !== '#')
                    @method('PUT')
                @endif
                <div>
                    <label for="mobile-current-password" class="block text-sm mb-1">Password Saat Ini</label>
                    <input id="mobile-current-password" type="password" name="current_password"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Password Saat Ini">
                </div>
                <div>
                    <label for="mobile-password" class="block text-sm mb-1">Password Baru</label>
                    <input id="mobile-password" type="password" name="password" class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                        placeholder="Password Baru">
                </div>
                <div>
                    <label for="mobile-password-confirmation" class="block text-sm mb-1">Ulangi Password Baru</label>
                    <input id="mobile-password-confirmation" type="password" name="password_confirmation"
                        class="w-full rounded-lg border border-blueGray-200 px-3 py-2" placeholder="Ulangi Password Baru">
                </div>
                <button class="rf-btn w-full">Ganti Password</button>
            </form>
        </div>
    </div>
@endsection
