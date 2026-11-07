@extends('layouts.app')

@php
    $avatar = $user->avatar_url ?? ($user->photo_url ?? null);
    $badge = ['active' => 'green', 'pending' => 'yellow', 'suspended' => 'red'][$user->status] ?? 'blue';
@endphp

@section('page-content')
    @if ($errors->any())
        <div class="rf-card mb-4 bg-red-50" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-12">
        <section class="rf-card flex flex-wrap items-center gap-4 lg:col-span-12">
            @if ($avatar)
                <img class="h-16 w-16 rounded-xl object-cover"
                    src="{{ $avatar }}?v={{ optional($user->updated_at)->timestamp }}" alt="Foto profil {{ $user->name }}">
            @else
                <span class="inline-flex h-16 w-16 items-center justify-center rounded-xl bg-blueGray-200 text-blueGray-500" aria-hidden="true">
                    <i class="fas fa-user-circle text-3xl"></i>
                </span>
            @endif
            <div class="min-w-0">
                <h1 class="font-semibold">{{ $user->name }}</h1>
                <p class="text-sm text-slate-500">{{ $user->angkatan ?: 'Angkatan ?' }} • {{ optional($user->wilayah)->name ?: 'Wilayah ?' }}</p>
                <span class="rf-badge bg-{{ $badge }}-100 text-{{ $badge }}-800 mt-1 inline-block capitalize">{{ $user->status }}</span>
            </div>
        </section>

        <section class="rf-card min-w-0 lg:col-span-8" aria-labelledby="profile-info-heading">
            <h2 id="profile-info-heading" class="mb-4 font-semibold">Info Pribadi</h2>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="profile-photo" class="mb-1 block text-sm">Foto Profil</label>
                    <input id="profile-photo" type="file" name="photo" accept="image/*" class="w-full min-w-0 rounded-lg border border-blueGray-200 px-3 py-2">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="profile-name" class="mb-1 block text-sm">Nama Lengkap</label>
                        <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-email" class="mb-1 block text-sm">Email</label>
                        <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-phone" class="mb-1 block text-sm">No. WA</label>
                        <input id="profile-phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-tempat-lahir" class="mb-1 block text-sm">Tempat Lahir</label>
                        <input id="profile-tempat-lahir" type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-tanggal-lahir" class="mb-1 block text-sm">Tanggal Lahir</label>
                        <input id="profile-tanggal-lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}" autocomplete="bday" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-pekerjaan" class="mb-1 block text-sm">Pekerjaan</label>
                        <input id="profile-pekerjaan" type="text" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}" autocomplete="organization-title" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-pendidikan" class="mb-1 block text-sm">Pendidikan Terakhir</label>
                        <input id="profile-pendidikan" type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $user->pendidikan_terakhir) }}" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-kampus" class="mb-1 block text-sm">Kampus</label>
                        <input id="profile-kampus" type="text" name="kampus" value="{{ old('kampus', $user->kampus) }}" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-status-pernikahan" class="mb-1 block text-sm">Status Pernikahan</label>
                        <select id="profile-status-pernikahan" name="status_pernikahan" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            <option value="lajang" @selected(old('status_pernikahan', $user->status_pernikahan) == 'lajang')>Lajang</option>
                            <option value="menikah" @selected(old('status_pernikahan', $user->status_pernikahan) == 'menikah')>Menikah</option>
                            <option value="duda/janda" @selected(old('status_pernikahan', $user->status_pernikahan) == 'duda/janda')>Duda / Janda</option>
                        </select>
                    </div>
                    <div>
                        <label for="profile-ab" class="mb-1 block text-sm">AB</label>
                        <select id="profile-ab" name="ab" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            <option value="iya" @selected(old('ab', $user->ab) == 'iya')>Iya</option>
                            <option value="tidak" @selected(old('ab', $user->ab) == 'tidak')>Tidak</option>
                        </select>
                    </div>
                    <div>
                        <label for="profile-angkatan" class="mb-1 block text-sm">Angkatan</label>
                        <input id="profile-angkatan" type="text" name="angkatan" value="{{ old('angkatan', $user->angkatan) }}" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                    </div>
                    <div>
                        <label for="profile-wilayah" class="mb-1 block text-sm">Wilayah</label>
                        <select id="profile-wilayah" name="wilayah_id" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                            <option value="">Pilih...</option>
                            @foreach ($wilayah as $region)
                                <option value="{{ $region->id }}" @selected(old('wilayah_id', $user->wilayah_id) == $region->id)>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rf-btn"><i class="fas fa-save" aria-hidden="true"></i> Simpan Perubahan</button>
                </div>
            </form>
        </section>

        <section class="rf-card min-w-0 lg:col-span-4" aria-labelledby="profile-security-heading">
            <h2 id="profile-security-heading" class="mb-4 font-semibold">Keamanan</h2>
            <form method="POST" action="{{ route('profile.updatePassword') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="profile-current-password" class="mb-1 block text-sm">Password Saat Ini</label>
                    <input id="profile-current-password" type="password" name="current_password" autocomplete="current-password" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                </div>
                <div>
                    <label for="profile-password" class="mb-1 block text-sm">Password Baru</label>
                    <input id="profile-password" type="password" name="password" autocomplete="new-password" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                </div>
                <div>
                    <label for="profile-password-confirmation" class="mb-1 block text-sm">Ulangi Password Baru</label>
                    <input id="profile-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
                </div>
                <button type="submit" class="rf-btn w-full">Ganti Password</button>
            </form>
        </section>
    </div>
@endsection
