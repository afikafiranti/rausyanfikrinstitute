@extends('layouts.app')
@section('page-content')
  @php
    // Data demo; ganti dengan $materis dari controller kalau sudah ada
    $items = collect([
      ['judul'=>'Pengantar Akidah','kategori'=>'Akidah','level'=>'Level 1','status'=>'Publik'],
      ['judul'=>'Fiqih Thaharah','kategori'=>'Fiqih','level'=>'Level 2','status'=>'Publik'],
      ['judul'=>'Adab Majelis','kategori'=>'Adab','level'=>'Level 1','status'=>'Draft'],
    ]);
    // jika pakai paginator/collection dari controller, pakai itu:
    if (isset($materis)) $items = $materis;
  @endphp

  {{-- ===================== DESKTOP (≥ md) ===================== --}}
  <div class="hidden md:block">
    <div class="grid grid-cols-12 gap-4">
      {{-- Sidebar Filter --}}
      <aside class="col-span-3 rounded-xl bg-white p-4 shadow">
        <h3 class="font-semibold mb-3">Filter</h3>
        <form action="{{ route('materi.index') }}" method="GET" class="space-y-3">
          <div>
            <label class="block text-sm mb-1">Pencarian</label>
            <input type="text" name="q" value="{{ request('q') }}"
                   class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
                   placeholder="judul/keyword..." autocomplete="off">
          </div>
          <div>
            <label class="block text-sm mb-1">Kategori</label>
            <select name="kategori" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
              @php $kat = request('kategori'); @endphp
              <option value="">Semua</option>
              @foreach (['Akidah','Fiqih','Adab'] as $k)
                <option value="{{ $k }}" {{ $kat===$k?'selected':'' }}>{{ $k }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Level</label>
            <select name="level" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
              @php $lvl = request('level'); @endphp
              <option value="">Semua</option>
              @foreach (['Level 1','Level 2','Level 3'] as $l)
                <option value="{{ $l }}" {{ $lvl===$l?'selected':'' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Status</label>
            @php $st = request('status'); @endphp
            <select name="status" class="w-full rounded-lg border border-blueGray-200 px-3 py-2">
              <option value="">Semua</option>
              @foreach (['Draft','Publik','Arsip'] as $s)
                <option value="{{ $s }}" {{ $st===$s?'selected':'' }}>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="flex gap-2">
            <button class="rf-btn w-full">Terapkan</button>
            <a href="{{ route('materi.index') }}" class="w-full text-center rounded-lg border px-3 py-2">Reset</a>
          </div>
        </form>
      </aside>

      {{-- Tabel Konten --}}
      <section class="col-span-9 rounded-xl bg-white p-4 shadow">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold">Daftar Materi</h3>
          @if(Route::has('materi.create'))
            <a href="{{ route('materi.create') }}" class="rf-btn"><i class="fas fa-plus mr-2"></i>Tambah Materi</a>
          @endif
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
              @forelse ($items as $row)
                @php
                  // dukung array demo atau model
                  $judul    = is_array($row) ? $row['judul']    : ($row->judul ?? $row->title ?? '-');
                  $kategori = is_array($row) ? $row['kategori'] : ($row->kategori->nama ?? $row->kategori ?? '-');
                  $level    = is_array($row) ? $row['level']    : ($row->level ?? '-');
                  $status   = is_array($row) ? $row['status']   : (\Illuminate\Support\Str::title($row->status ?? 'Draft'));
                  $color    = $status==='Publik'?'green':($status==='Draft'?'yellow':'blue');
                @endphp
                <tr>
                  <td class="py-2 pr-4">{{ $judul }}</td>
                  <td class="py-2 pr-4">{{ $kategori }}</td>
                  <td class="py-2 pr-4">{{ $level }}</td>
                  <td class="py-2 pr-4"><x-badge :color="$color">{{ $status }}</x-badge></td>
                  <td class="py-2">
                    <div class="flex gap-3">
                      @if(Route::has('materi.show') && isset($row->id))
                        <a href="{{ route('materi.show', $row->id) }}" class="text-indigo-600 hover:underline">Lihat</a>
                      @else
                        <a href="#" class="text-indigo-600 hover:underline">Lihat</a>
                      @endif
                      @if(Route::has('materi.edit') && isset($row->id))
                        <a href="{{ route('materi.edit', $row->id) }}" class="text-slate-600 hover:underline">Edit</a>
                      @else
                        <a href="#" class="text-slate-600 hover:underline">Edit</a>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-6 text-center text-slate-500">Belum ada materi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination jika tersedia --}}
        @if(method_exists($items,'links'))
          <div class="mt-4">{{ $items->withQueryString()->links() }}</div>
        @endif
      </section>
    </div>
  </div>

  {{-- ====================== MOBILE (< md) ====================== --}}
  <div class="md:hidden">
    <h1 class="text-xl font-semibold">Materi</h1>

    <form action="{{ route('materi.index') }}" method="GET" class="mt-3">
      <input type="text" name="q" value="{{ request('q') }}"
             class="w-full rounded-lg border border-blueGray-200 px-3 py-2"
             placeholder="Cari materi..." autocomplete="off">
    </form>

    <div class="mt-4 space-y-3">
      @forelse ($items as $m)
        @php
          $judul  = is_array($m) ? $m['judul']  : ($m->judul ?? $m->title ?? '-');
          $kat    = is_array($m) ? $m['kategori'] : ($m->kategori->nama ?? $m->kategori ?? '-');
          $lvl    = is_array($m) ? $m['level']  : ($m->level ?? '-');
          $status = is_array($m) ? $m['status'] : (\Illuminate\Support\Str::title($m->status ?? 'Draft'));
          $color  = $status==='Publik'?'green':'yellow';
        @endphp
        <div class="rf-card">
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium">{{ $judul }}</div>
              <div class="text-xs text-slate-500 mt-0.5">{{ $kat }} • {{ $lvl }}</div>
            </div>
            <x-badge :color="$color">{{ $status }}</x-badge>
          </div>
          <div class="mt-3 flex gap-3 text-sm">
            @if(Route::has('materi.show') && isset($m->id))
              <a href="{{ route('materi.show', $m->id) }}" class="text-indigo-600">Buka</a>
            @else
              <a href="#" class="text-indigo-600">Buka</a>
            @endif
            @if(Route::has('materi.edit') && isset($m->id))
              <a href="{{ route('materi.edit', $m->id) }}" class="text-slate-600">Edit</a>
            @else
              <a href="#" class="text-slate-600">Edit</a>
            @endif
          </div>
        </div>
      @empty
        <div class="rf-card text-slate-500">Belum ada materi.</div>
      @endforelse

      @if(method_exists($items,'links'))
        <div class="pt-2">{{ $items->withQueryString()->links() }}</div>
      @endif
    </div>
  </div>
@endsection
