@extends('layouts.app')

@php
  function sort_link($col) {
    $isActive = request('sort','created_at') === $col;
    $dir = $isActive && request('dir','desc') === 'desc' ? 'asc' : 'desc';
    return request()->fullUrlWithQuery(['sort'=>$col,'dir'=>$dir]);
  }

  $q          = $q          ?? request('q');
  $angkatan   = $angkatan   ?? request('angkatan');
  $wilayahId  = $wilayahId  ?? request('wilayah_id');
  $levelId    = $levelId    ?? request('level_id');
  $perPage    = $perPage    ?? (int) request('per_page', 15);
@endphp

@section('page-content')

  {{-- ===================== DESKTOP (≥ md) ===================== --}}
  <div class="hidden md:block">
    <div class="flex items-center justify-between mb-4">
      <form method="GET" action="{{ route('alumni.index') }}" class="flex items-center gap-2">
        <input type="text" name="q" value="{{ $q }}" class="rounded-lg border px-3 py-2" placeholder="Cari nama/email...">
        <select name="angkatan" class="rounded-lg border px-3 py-2">
          <option value="">Angkatan</option>
          @foreach($angkatanList as $a)
            <option value="{{ $a }}" @selected($angkatan===$a)>{{ $a }}</option>
          @endforeach
        </select>
        <select name="wilayah_id" class="rounded-lg border px-3 py-2">
          <option value="">Wilayah</option>
          @foreach($wilayah as $w)
            <option value="{{ $w->id }}" @selected((string)$wilayahId===(string)$w->id)>{{ $w->name }}</option>
          @endforeach
        </select>
        <select name="level_id" class="rounded-lg border px-3 py-2">
          <option value="">Level</option>
          @foreach($level as $l)
            <option value="{{ $l->id }}" @selected((string)$levelId===(string)$l->id)>{{ $l->description }}</option>
          @endforeach
        </select>
        <select name="per_page" class="rounded-lg border px-3 py-2">
          @foreach([15,25,50,100] as $pp)
            <option value="{{ $pp }}" @selected((int)$perPage===$pp)>{{ $pp }}/hal</option>
          @endforeach
        </select>
        <button class="rf-btn"><i class="fas fa-search"></i> Filter</button>
      </form>
    </div>

    <div class="rounded-xl bg-white p-4 shadow">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="border-b text-slate-600">
            <tr>
              <th class="py-2 px-3 text-left"><a href="{{ sort_link('name') }}" class="hover:underline">Nama</a></th>
              <th class="py-2 px-3 text-left"><a href="{{ sort_link('wilayah') }}" class="hover:underline">Wilayah</a></th>
              <th class="py-2 px-3 text-left"><a href="{{ sort_link('pekerjaan') }}" class="hover:underline">Pekerjaan</a></th>
              <th class="py-2 px-3 text-left"><a href="{{ sort_link('level') }}" class="hover:underline">Level</a></th>
              <th class="py-2 px-3 text-left"><a href="{{ sort_link('status') }}" class="hover:underline">Status</a></th>
              <th class="py-2 px-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($alumni as $u)
              @php $badge = ['active'=>'green','pending'=>'yellow','suspended'=>'red'][$u->status] ?? 'blue'; @endphp
              <tr>
                <td class="py-2 px-3">{{ $u->name }}</td>
                <td class="py-2 px-3">{{ optional($u->wilayah)->name ?? '-' }}</td>
                <td class="py-2 px-3">{{ $u->pekerjaan ?? '-' }}</td>
                <td class="py-2 px-3">{{ optional($u->level)->description ?? '-' }}</td>
                <td class="py-2 px-3">
                  <span class="rf-badge bg-{{ $badge }}-100 text-{{ $badge }}-800 capitalize">{{ $u->status }}</span>
                </td>
                <td class="py-2 px-3 text-center">
                  <a href="{{ route('alumni.show', $u->id) }}" class="rf-btn-sm bg-blue-100 text-blue-700 hover:bg-blue-200">
                    <i class="fas fa-eye"></i> Detail
                  </a>
                </td>
              </tr>
            @empty
              <tr><td class="py-4 px-3 text-center text-slate-500" colspan="6">Tidak ada data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $alumni->withQueryString()->links() }}</div>
    </div>
  </div>

  {{-- ====================== MOBILE (< md) ====================== --}}
  <div class="md:hidden">
    <h2 class="text-xl font-semibold">Data Alumni</h2>

    <form method="GET" action="{{ route('alumni.index') }}" class="rf-card mt-3 space-y-2">
      <input type="text" name="q" value="{{ $q }}" class="w-full rounded-lg border px-3 py-2" placeholder="Cari nama/email...">
      <div class="grid grid-cols-2 gap-2">
        <select name="angkatan" class="rounded-lg border px-2 py-2">
          <option value="">Angkatan</option>
          @foreach($angkatanList as $a)
            <option value="{{ $a }}" @selected($angkatan===$a)>{{ $a }}</option>
          @endforeach
        </select>
        <select name="wilayah_id" class="rounded-lg border px-2 py-2">
          <option value="">Wilayah</option>
          @foreach($wilayah as $w)
            <option value="{{ $w->id }}" @selected((string)$wilayahId===(string)$w->id)>{{ $w->name }}</option>
          @endforeach
        </select>
        <select name="level_id" class="rounded-lg border px-2 py-2">
          <option value="">Level</option>
          @foreach($level as $l)
            <option value="{{ $l->id }}" @selected((string)$levelId===(string)$l->id)>{{ $l->description }}</option>
          @endforeach
        </select>
      </div>
      <button class="rf-btn w-full"><i class="fas fa-search"></i> Terapkan</button>
    </form>

    <div class="mt-3 space-y-3">
      @forelse($alumni as $u)
        @php $badge = ['active'=>'green','pending'=>'yellow','suspended'=>'red'][$u->status] ?? 'blue'; @endphp
        <div class="rf-card">
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium">{{ $u->name }}</div>
              <div class="text-xs text-slate-500">{{ optional($u->wilayah)->name ?? '-' }}</div>
              <div class="text-xs text-slate-500">{{ $u->pekerjaan ?? '-' }}</div>
            </div>
            <span class="rf-badge bg-{{ $badge }}-100 text-{{ $badge }}-800 capitalize">{{ $u->status }}</span>
          </div>
          <div class="mt-2 text-sm text-slate-600">
            Level: <b>{{ optional($u->level)->description ?? '-' }}</b>
          </div>
          <a href="{{ route('alumni.show', $u->id) }}" class="rf-btn-sm mt-2 w-full text-center bg-blue-100 text-blue-700 hover:bg-blue-200">
            <i class="fas fa-eye"></i> Detail
          </a>
        </div>
      @empty
        <div class="rf-card text-slate-500">Tidak ada data.</div>
      @endforelse
    </div>

    <div class="mt-4">{{ $alumni->withQueryString()->links() }}</div>
  </div>
@endsection
