@extends('layouts.app')

{{-- ===================== DESKTOP (≥ md) ===================== --}}
@section('content-desktop')
  <h1 class="text-xl font-semibold mb-4">Materi</h1>

  <div class="grid grid-cols-12 gap-4">
    {{-- Sidebar Filter --}}
    <aside class="col-span-3 rounded-xl bg-white p-4 shadow">
      <h3 class="font-semibold mb-3">Filter</h3>
      <form action="#" method="GET" class="space-y-3">
        <div>
          <label class="block text-sm mb-1">Pencarian</label>
          <input type="text" name="q" class="w-full rounded-lg" placeholder="judul/keyword...">
        </div>
        <div>
          <label class="block text-sm mb-1">Kategori</label>
          <select name="kategori" class="w-full rounded-lg">
            <option value="">Semua</option>
            <option>Akidah</option>
            <option>Fiqih</option>
            <option>Adab</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Level</label>
          <select name="level" class="w-full rounded-lg">
            <option value="">Semua</option>
            <option>Level 1</option>
            <option>Level 2</option>
            <option>Level 3</option>
          </select>
        </div>
        <div>
          <label class="block text-sm mb-1">Status</label>
          <select name="status" class="w-full rounded-lg">
            <option value="">Semua</option>
            <option>Draft</option>
            <option>Publik</option>
            <option>Arsip</option>
          </select>
        </div>
        <button class="rf-btn w-full">Terapkan</button>
      </form>
    </aside>

    {{-- Tabel Konten --}}
    <section class="col-span-9 rounded-xl bg-white p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Daftar Materi</h3>
        <a href="#" class="rf-btn">Tambah Materi</a>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-left text-slate-600 border-b">
            <tr>
              <th class="py-2 pr-4">Judul</th>
              <th class="py-2 pr-4">Kategori</th>
              <th class="py-2 pr-4">Level</th>
              <th class="py-2 pr-4">Status</th>
              <th class="py-2">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @foreach([
              ['Judul'=>'Pengantar Akidah','Kategori'=>'Akidah','Level'=>'Level 1','Status'=>'Publik'],
              ['Judul'=>'Fiqih Thaharah','Kategori'=>'Fiqih','Level'=>'Level 2','Status'=>'Publik'],
              ['Judul'=>'Adab Majelis','Kategori'=>'Adab','Level'=>'Level 1','Status'=>'Draft'],
            ] as $row)
            <tr>
              <td class="py-2 pr-4">{{ $row['Judul'] }}</td>
              <td class="py-2 pr-4">{{ $row['Kategori'] }}</td>
              <td class="py-2 pr-4">{{ $row['Level'] }}</td>
              <td class="py-2 pr-4">
                @php $color = $row['Status']==='Publik'?'green':($row['Status']==='Draft'?'yellow':'blue'); @endphp
                <x-badge :color="$color">{{ $row['Status'] }}</x-badge>
              </td>
              <td class="py-2">
                <div class="flex gap-2">
                  <a href="#" class="text-indigo-600 hover:underline">Lihat</a>
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
  <h1 class="text-xl font-semibold">Materi</h1>

  <div class="mt-3">
    <input type="text" class="w-full rounded-lg" placeholder="Cari materi...">
  </div>

  <div class="mt-4 space-y-3">
    @foreach([
      ['Judul'=>'Pengantar Akidah','Kategori'=>'Akidah','Level'=>'L1','Status'=>'Publik'],
      ['Judul'=>'Fiqih Thaharah','Kategori'=>'Fiqih','Level'=>'L2','Status'=>'Publik'],
      ['Judul'=>'Adab Majelis','Kategori'=>'Adab','Level'=>'L1','Status'=>'Draft'],
    ] as $m)
      <div class="rf-card">
        <div class="flex items-start justify-between">
          <div>
            <div class="font-medium">{{ $m['Judul'] }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $m['Kategori'] }} • {{ $m['Level'] }}</div>
          </div>
          @php $color = $m['Status']==='Publik'?'green':'yellow'; @endphp
          <x-badge :color="$color">{{ $m['Status'] }}</x-badge>
        </div>
        <div class="mt-3 flex gap-3 text-sm">
          <a href="#" class="text-indigo-600">Buka</a>
          <a href="#" class="text-slate-600">Edit</a>
        </div>
      </div>
    @endforeach
  </div>
@endsection
