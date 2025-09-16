@extends('layouts.app')

{{-- =============== DESKTOP (≥ md) =============== --}}
@section('content-desktop')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Profil</h1>
    <button form="formProfile" class="rf-btn">
      <i class="fas fa-save"></i> Simpan Perubahan
    </button>
  </div>

  @if (session('success'))
    <div class="rf-card mb-4 bg-green-50">{{ session('success') }}</div>
  @endif

  <div class="grid grid-cols-12 gap-4">
    <section class="col-span-12 rounded-xl bg-white p-4 shadow flex items-center gap-4">
      <img class="h-16 w-16 rounded-xl object-cover"
           src="{{ $user->photo_url ?: 'https://via.placeholder.com/160' }}" alt="avatar">
      <div class="flex-1">
        <div class="font-semibold">{{ $user->name }}</div>
        <div class="text-sm text-slate-500">
          {{ $user->angkatan ?: 'Angkatan ?' }} • {{ optional($user->wilayah)->name ?: 'Wilayah ?' }}
        </div>
        @php $c = ['active'=>'green','pending'=>'yellow','suspended'=>'red'][$user->status] ?? 'blue'; @endphp
        <span class="rf-badge bg-{{ $c }}-100 text-{{ $c }}-800 capitalize mt-1 inline-block">{{ $user->status }}</span>
      </div>
    </section>

    <section class="col-span-8 rounded-xl bg-white p-4 shadow">
      <h3 class="font-semibold mb-3">Info Pribadi</h3>
      <form id="formProfile" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
            class="grid grid-cols-2 gap-4">
        @csrf @method('PUT')

        <div>
          <label class="block text-sm mb-1">Nama Lengkap</label>
          <input type="text" name="name" value="{{ old('name',$user->name) }}" class="w-full rounded-lg" required>
          @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm mb-1">Email</label>
          <input type="email" name="email" value="{{ old('email',$user->email) }}" class="w-full rounded-lg" required>
          @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm mb-1">No. WA</label>
          <input type="text" name="phone" value="{{ old('phone',$user->phone) }}" class="w-full rounded-lg" placeholder="+62...">
          @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm mb-1">Pekerjaan</label>
          <input type="text" name="pekerjaan" value="{{ old('pekerjaan',$user->pekerjaan) }}" class="w-full rounded-lg">
          @error('pekerjaan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm mb-1">Angkatan</label>
          <input type="text" name="angkatan" value="{{ old('angkatan',$user->angkatan) }}" class="w-full rounded-lg">
          @error('angkatan') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm mb-1">Wilayah</label>
          <select name="wilayah_id" class="w-full rounded-lg">
            <option value="">Pilih...</option>
            @foreach($wilayah as $w)
              <option value="{{ $w->id }}" @selected(old('wilayah_id',$user->wilayah_id)==$w->id)>{{ $w->name }}</option>
            @endforeach
          </select>
          @error('wilayah_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="col-span-2">
          <label class="block text-sm mb-1">Foto</label>
          <input type="file" name="photo" class="w-full rounded-lg" accept="image/*">
          @error('photo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
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
@endsection

{{-- =============== MOBILE (< md) =============== --}}
@section('content-mobile')
  <h1 class="text-xl font-semibold">Profil</h1>

  @if (session('success'))
    <div class="rf-card mt-3 bg-green-50">{{ session('success') }}</div>
  @endif

  <div class="rf-section mt-3">
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3">
      @csrf @method('PUT')

      <div class="flex items-center gap-4">
        <img class="h-16 w-16 rounded-xl object-cover"
             src="{{ $user->photo_url ?: 'https://via.placeholder.com/160' }}" alt="avatar">
        <input type="file" name="photo" class="flex-1 rounded-lg" accept="image/*">
      </div>

      <input type="text" name="name" value="{{ old('name',$user->name) }}" class="w-full rounded-lg" placeholder="Nama" required>
      <input type="email" name="email" value="{{ old('email',$user->email) }}" class="w-full rounded-lg" placeholder="Email" required>
      <input type="text" name="phone" value="{{ old('phone',$user->phone) }}" class="w-full rounded-lg" placeholder="No. WA">
      <input type="text" name="pekerjaan" value="{{ old('pekerjaan',$user->pekerjaan) }}" class="w-full rounded-lg" placeholder="Pekerjaan">
      <input type="text" name="angkatan" value="{{ old('angkatan',$user->angkatan) }}" class="w-full rounded-lg" placeholder="Angkatan">

      <select name="wilayah_id" class="w-full rounded-lg">
        <option value="">Wilayah</option>
        @foreach($wilayah as $w)
          <option value="{{ $w->id }}" @selected(old('wilayah_id',$user->wilayah_id)==$w->id)>{{ $w->name }}</option>
        @endforeach
      </select>

      <button class="rf-btn w-full">Simpan</button>
    </form>

    <p class="text-xs text-slate-500 mt-2">
      Ubah Wilayah/Angkatan → status <b>pending</b> (menunggu verifikasi Koorda).
    </p>
  </div>
@endsection
