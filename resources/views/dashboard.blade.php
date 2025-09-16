@extends('layouts.app')
@section('content')
  <h1 class="text-xl font-semibold">Dashboard</h1>
  <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
    @foreach ([
      ['label'=>'Total Alumni','value'=>12345],
      ['label'=>'Aktif','value'=>10102],
      ['label'=>'Kajian/Bulan','value'=>87],
      ['label'=>'Rata-rata Peserta','value'=>42],
    ] as $c)
      <div class="rounded-xl bg-white p-4 shadow">
        <div class="text-xs text-slate-500">{{ $c['label'] }}</div>
        <div class="mt-1 text-2xl font-semibold">{{ $c['value'] }}</div>
      </div>
    @endforeach
  </div>

  <div class="mt-6 rounded-xl bg-white p-4 shadow">
    <h3 class="font-semibold mb-3">Tren Kajian per Bulan</h3>
    <canvas id="chartKajian" class="w-full h-64"></canvas>
  </div>
@endsection
