@extends('layouts.app')

{{-- ===================== DESKTOP (≥ md) ===================== --}}
@section('content-desktop')
  <h1 class="text-xl font-semibold mb-4">Laporan</h1>

  <div class="grid grid-cols-12 gap-4">
    {{-- Form Input Laporan --}}
    <section class="col-span-4 rounded-xl bg-white p-4 shadow">
      <h3 class="font-semibold mb-3">Input Laporan</h3>
      <form action="#" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
          <label class="block text-sm mb-1">Tanggal</label>
          <input type="date" name="tanggal" class="w-full rounded-lg">
        </div>
        <div>
          <label class="block text-sm mb-1">Wilayah</label>
          <select name="wilayah_id" class="w-full rounded-lg">
            <option value="">Pilih...</option>
            <option value="1">Luwu</option>
            <option value="2">Luwu Timur</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Judul Kegiatan</label>
          <input type="text" name="judul" class="w-full rounded-lg" placeholder="Nama kegiatan...">
        </div>
        <div>
          <label class="block text-sm mb-1">Ringkasan</label>
          <textarea name="ringkasan" rows="4" class="w-full rounded-lg" placeholder="Uraian singkat..."></textarea>
        </div>
        <div>
          <label class="block text-sm mb-1">Lampiran (opsional)</label>
          <input type="file" name="lampiran" class="w-full rounded-lg">
        </div>
        <button class="rf-btn w-full">Simpan Laporan</button>
      </form>
    </section>

    {{-- Riwayat Laporan --}}
    <section class="col-span-8 rounded-xl bg-white p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Riwayat Terbaru</h3>
        <div class="text-sm text-slate-500">3 item</div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-left text-slate-600 border-b">
            <tr>
              <th class="py-2 pr-4">Tanggal</th>
              <th class="py-2 pr-4">Wilayah</th>
              <th class="py-2 pr-4">Judul</th>
              <th class="py-2 pr-4">Status</th>
              <th class="py-2">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach([
              ['tgl'=>'2025-09-10','wilayah'=>'Luwu','judul'=>'Kajian Rutin Masjid Al-...','status'=>'Menunggu Review'],
              ['tgl'=>'2025-09-08','wilayah'=>'Luwu Timur','judul'=>'Koordinasi Koorda','status'=>'Disetujui'],
              ['tgl'=>'2025-09-05','wilayah'=>'Luwu','judul'=>'Pengumpulan Donasi','status'=>'Ditolak'],
            ] as $r)
              @php
                $color = $r['status']==='Disetujui'?'green':($r['status']==='Menunggu Review'?'yellow':'red');
              @endphp
              <tr>
                <td class="py-2 pr-4">{{ $r['tgl'] }}</td>
                <td class="py-2 pr-4">{{ $r['wilayah'] }}</td>
                <td class="py-2 pr-4">{{ $r['judul'] }}</td>
                <td class="py-2 pr-4"><x-badge :color="$color">{{ $r['status'] }}</x-badge></td>
                <td class="py-2">
                  <div class="flex gap-2">
                    <a href="#" class="text-indigo-600 hover:underline">Detail</a>
                    <a href="#" class="text-slate-600 hover:underline">Edit</a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection

{{-- ====================== MOBILE (< md) ====================== --}}
@section('content-mobile')
  <h1 class="text-xl font-semibold">Laporan</h1>

  {{-- Form ringkas --}}
  <div class="rf-section">
    <h3 class="font-semibold mb-3">Input Laporan</h3>
    <form action="#" method="POST" enctype="multipart/form-data" class="space-y-3">
      @csrf
      <input type="date" class="w-full rounded-lg">
      <select class="w-full rounded-lg">
        <option>Wilayah</option><option>Luwu</option><option>Luwu Timur</option>
      </select>
      <input type="text" class="w-full rounded-lg" placeholder="Judul kegiatan...">
      <textarea rows="3" class="w-full rounded-lg" placeholder="Ringkasan..."></textarea>
      <input type="file" class="w-full rounded-lg">
      <button class="rf-btn w-full">Simpan</button>
    </form>
  </div>

  {{-- Riwayat ringkas --}}
  <div class="mt-4 space-y-3">
    @foreach([
      ['tgl'=>'10 Sep','wilayah'=>'Luwu','judul'=>'Kajian Rutin','status'=>'Menunggu Review'],
      ['tgl'=>'08 Sep','wilayah'=>'Luwu Timur','judul'=>'Koordinasi','status'=>'Disetujui'],
    ] as $r)
      @php $color = $r['status']==='Disetujui'?'green':'yellow'; @endphp
      <div class="rf-card">
        <div class="flex items-center justify-between">
          <div>
            <div class="font-medium">{{ $r['judul'] }}</div>
            <div class="text-xs text-slate-500">{{ $r['tgl'] }} • {{ $r['wilayah'] }}</div>
          </div>
          <x-badge :color="$color">{{ $r['status'] }}</x-badge>
        </div>
        <div class="mt-3 flex gap-3 text-sm">
          <a href="#" class="text-indigo-600">Detail</a>
          <a href="#" class="text-slate-600">Edit</a>
        </div>
      </div>
    @endforeach
  </div>
@endsection
