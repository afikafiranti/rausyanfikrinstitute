@extends('layouts.app')

{{-- DESKTOP: Dashboard Nexus --}}
@section('content-desktop')
  <div class="grid grid-cols-12 gap-4">
    {{-- Stat cards --}}
    <div class="col-span-3 rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Total Alumni</div>
      <div class="mt-1 text-2xl font-semibold">12.345</div>
    </div>
    <div class="col-span-3 rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Aktif</div>
      <div class="mt-1 text-2xl font-semibold">10.102</div>
    </div>
    <div class="col-span-3 rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Kajian/Bulan</div>
      <div class="mt-1 text-2xl font-semibold">87</div>
    </div>
    <div class="col-span-3 rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Rata2 Peserta</div>
      <div class="mt-1 text-2xl font-semibold">42</div>
    </div>

    {{-- Chart besar --}}
    <div class="col-span-8 rounded-xl bg-white p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Tren Kajian</h3>
        <a href="#" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-600 text-white">Lihat Detail</a>
      </div>
      <canvas id="chartKajianDesktop" class="w-full h-80"></canvas>
    </div>

    {{-- Tabel ringkas / aktivitas --}}
    <div class="col-span-4 rounded-xl bg-white p-4 shadow">
      <h3 class="font-semibold mb-3">Aktivitas Terbaru</h3>
      <ul class="space-y-2 text-sm">
        <li class="flex items-center justify-between">
          <span>Input Laporan Koorda Luwu</span><span class="text-slate-500">2 jam lalu</span>
        </li>
        <li class="flex items-center justify-between">
          <span>Pembaruan Materi #24</span><span class="text-slate-500">kemarin</span>
        </li>
      </ul>
    </div>
  </div>
@endsection

{{-- MOBILE: simpel + bottom navbar --}}
@section('content-mobile')
  <h1 class="text-xl font-semibold">Dashboard</h1>

  <div class="mt-4 grid grid-cols-2 gap-3">
    <div class="rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Total Alumni</div>
      <div class="mt-1 text-2xl font-semibold">12.345</div>
    </div>
    <div class="rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Aktif</div>
      <div class="mt-1 text-2xl font-semibold">10.102</div>
    </div>
    <div class="rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Kajian/Bulan</div>
      <div class="mt-1 text-2xl font-semibold">87</div>
    </div>
    <div class="rounded-xl bg-white p-4 shadow">
      <div class="text-xs text-slate-500">Rata2 Peserta</div>
      <div class="mt-1 text-2xl font-semibold">42</div>
    </div>
  </div>

  <div class="mt-6 rounded-xl bg-white p-4 shadow">
    <h3 class="font-semibold mb-3">Tren Kajian</h3>
    <canvas id="chartKajianMobile" class="w-full h-64"></canvas>
  </div>
@endsection
