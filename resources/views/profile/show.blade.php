@extends('layouts.app')

{{-- ===================== DESKTOP (≥ md) ===================== --}}
@section('content-desktop')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Profil</h1>
    <a href="#" class="rf-btn">Ubah Foto</a>
  </div>

  <div class="grid grid-cols-12 gap-4">
    {{-- Header profil --}}
    <section class="col-span-12 rounded-xl bg-white p-4 shadow flex items-center gap-4">
      <img class="h-16 w-16 rounded-xl object-cover" src="https://via.placeholder.com/160" alt="avatar">
      <div>
        <div class="font-semibold">Nama Pengguna</div>
        <div class="text-sm text-slate-500">Angkatan 2020 • Luwu</div>
        <div class="mt-1"><x-badge color="green">Active</x-badge></div>
      </div>
    </section>

    {{-- Info Pribadi --}}
    <section class="col-span-8 rounded-xl bg-white p-4 shadow">
      <h3 class="font-semibold mb-3">Info Pribadi</h3>
      <form action="#" method="POST" class="grid grid-cols-2 gap-4">
        @csrf
        <div>
          <label class="block text-sm mb-1">Nama Lengkap</label>
          <input type="text" class="w-full rounded-lg" value="Nama Pengguna">
        </div>
        <div>
          <label class="block text-sm mb-1">Email</label>
          <input type="email" class="w-full rounded-lg" value="user@example.com">
        </div>
        <div>
          <label class="block text-sm mb-1">No. HP</label>
          <input type="text" class="w-full rounded-lg" placeholder="+62...">
        </div>
        <div>
          <label class="block text-sm mb-1">Wilayah</label>
          <select class="w-full rounded-lg">
            <option>Luwu</option><option>Luwu Timur</option>
          </select>
        </div>
        <div class="col-span-2">
          <button class="rf-btn">Simpan Perubahan</button>
        </div>
      </form>
    </section>

    {{-- Keamanan Akun --}}
    <section class="col-span-4 rounded-xl bg-white p-4 shadow">
      <h3 class="font-semibold mb-3">Keamanan</h3>
      <form action="#" method="POST" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm mb-1">Password Saat Ini</label>
          <input type="password" class="w-full rounded-lg">
        </div>
        <div>
          <label class="block text-sm mb-1">Password Baru</label>
          <input type="password" class="w-full rounded-lg">
        </div>
        <div>
          <label class="block text-sm mb-1">Ulangi Password Baru</label>
          <input type="password" class="w-full rounded-lg">
        </div>
        <button class="rf-btn w-full">Ganti Password</button>
      </form>
    </section>
  </div>
@endsection

{{-- ====================== MOBILE (< md) ====================== --}}
@section('content-mobile')
  <h1 class="text-xl font-semibold">Profil</h1>

  <div class="rf-section">
    <div class="flex items-center gap-4">
      <img class="h-16 w-16 rounded-xl object-cover" src="https://via.placeholder.com/160" alt="avatar">
      <div>
        <div class="font-semibold">Nama Pengguna</div>
        <div class="text-sm text-slate-500">Angkatan 2020 • Luwu</div>
        <div class="mt-1"><x-badge color="green">Active</x-badge></div>
      </div>
    </div>
  </div>

  <div class="rf-section mt-4">
    <h3 class="font-semibold mb-3">Info Pribadi</h3>
    <form action="#" method="POST" class="space-y-3">
      @csrf
      <input type="text" class="w-full rounded-lg" placeholder="Nama Lengkap">
      <input type="email" class="w-full rounded-lg" placeholder="Email">
      <input type="text" class="w-full rounded-lg" placeholder="No. HP">
      <select class="w-full rounded-lg"><option>Wilayah</option><option>Luwu</option></select>
      <button class="rf-btn w-full">Simpan</button>
    </form>
  </div>

  <div class="rf-section mt-4">
    <h3 class="font-semibold mb-3">Keamanan</h3>
    <form action="#" method="POST" class="space-y-3">
      @csrf
      <input type="password" class="w-full rounded-lg" placeholder="Password Saat Ini">
      <input type="password" class="w-full rounded-lg" placeholder="Password Baru">
      <input type="password" class="w-full rounded-lg" placeholder="Ulangi Password Baru">
      <button class="rf-btn w-full">Ganti Password</button>
    </form>
  </div>
@endsection
