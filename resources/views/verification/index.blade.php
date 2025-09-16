@extends('layouts.app')
@section('page-content')
  @if (session('success'))
    <div class="rf-card mb-4 bg-green-50">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="rf-card mb-4 bg-red-50">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- ===================== DESKTOP (≥ md) ===================== --}}
  <div class="hidden md:block">
    <div class="rounded-xl bg-white p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Akun Pending</h3>
        <div class="text-sm text-slate-500">Total: {{ $pending->total() }}</div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="border-b text-slate-600">
            <tr>
              <th class="py-2 pr-4">Nama</th>
              <th class="py-2 pr-4">Email</th>
              <th class="py-2 pr-4">Wilayah</th>
              <th class="py-2 pr-4">Level Saat Ini</th>
              <th class="py-2 pr-4">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($pending as $u)
              <tr>
                <td class="py-2 pr-4">{{ $u->name }}</td>
                <td class="py-2 pr-4">{{ $u->email }}</td>
                <td class="py-2 pr-4">{{ optional($u->wilayah)->name ?: '-' }}</td>
                <td class="py-2 pr-4">{{ optional($u->level)->name ?: '-' }}</td>
                <td class="py-2">
                  <div class="flex items-center gap-2 flex-wrap">
                    {{-- Approve --}}
                    <form method="POST" action="{{ route('verification.approve',$u) }}" class="flex items-center gap-2">
                      @csrf
                      <select name="level_id" class="rounded-lg border px-2 py-1">
                        @foreach($levels as $lvl)
                          <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                        @endforeach
                      </select>
                      <input type="text" name="note" class="rounded-lg border px-2 py-1" placeholder="Catatan (opsional)">
                      <button class="rf-btn"><i class="fas fa-check"></i> Approve</button>
                    </form>

                    {{-- Reject --}}
                    <form method="POST" action="{{ route('verification.reject',$u) }}" class="flex items-center gap-2">
                      @csrf
                      <input type="text" name="reason" class="rounded-lg border px-2 py-1" placeholder="Alasan tolak" required>
                      <button class="inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-red-600 text-white hover:opacity-90">
                        <i class="fas fa-times"></i> Reject
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td class="py-4 text-center text-slate-500" colspan="5">Tidak ada akun pending.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $pending->withQueryString()->links() }}</div>
    </div>
  </div>

  {{-- ====================== MOBILE (< md) ====================== --}}
  <div class="md:hidden">
    <div class="mt-3 space-y-3">
      @forelse($pending as $u)
        <div class="rf-card">
          <div class="flex items-start justify-between">
            <div>
              <div class="font-medium">{{ $u->name }}</div>
              <div class="text-xs text-slate-500">{{ $u->email }} • {{ optional($u->wilayah)->name ?: '-' }}</div>
            </div>
            <span class="rf-badge bg-yellow-100 text-yellow-800 capitalize">pending</span>
          </div>

          {{-- Approve --}}
          <form method="POST" action="{{ route('verification.approve',$u) }}" class="mt-3 space-y-2">
            @csrf
            <select name="level_id" class="w-full rounded-lg border px-3 py-2">
              @foreach($levels as $lvl)
                <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
              @endforeach
            </select>
            <input type="text" name="note" class="w-full rounded-lg border px-3 py-2" placeholder="Catatan (opsional)">
            <button class="rf-btn w-full"><i class="fas fa-check"></i> Approve</button>
          </form>

          {{-- Reject --}}
          <form method="POST" action="{{ route('verification.reject',$u) }}" class="mt-2 space-y-2">
            @csrf
            <input type="text" name="reason" class="w-full rounded-lg border px-3 py-2" placeholder="Alasan tolak" required>
            <button class="w-full rounded-xl px-4 py-2 bg-red-600 text-white">Reject</button>
          </form>
        </div>
      @empty
        <div class="rf-card text-slate-500">Tidak ada akun pending.</div>
      @endforelse

      <div class="mt-4">{{ $pending->withQueryString()->links() }}</div>
    </div>
  </div>
@endsection
