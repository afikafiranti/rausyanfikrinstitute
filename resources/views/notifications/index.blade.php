@extends('layouts.app')

@php
  /** fallback jika controller tidak mengirim $unread */
  $unread = $unread ?? (auth()->check() ? auth()->user()->unreadNotifications()->count() : 0);
@endphp

@section('page-content')
  {{-- ===================== DESKTOP (≥ md) ===================== --}}
  <div class="hidden md:block">
    <div class="flex items-center justify-between mb-4">
      <div class="text-sm text-white/90"></div>
      <form method="POST" action="{{ route('notifications.readAll') }}">
        @csrf
        <button class="rf-btn" @disabled($unread===0)>
          <i class="fas fa-check-double"></i> Tandai semua terbaca
        </button>
      </form>
    </div>

    @if (session('success'))
      <div class="rf-card mb-4 bg-green-50">{{ session('success') }}</div>
    @endif

    <div class="rounded-xl bg-white p-4 shadow">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold">Daftar Notifikasi</h3>
        <div class="text-sm text-slate-500">Belum dibaca: {{ $unread }}</div>
      </div>

      <div class="divide-y">
        @forelse($notifications as $n)
          @php $data = $n->data; @endphp
          <div class="py-3 flex items-start justify-between {{ $n->read_at ? '' : 'bg-yellow-50/40' }}">
            <div class="pr-4">
              <div class="font-medium">{{ $data['title'] ?? 'Notifikasi' }}</div>
              <div class="text-sm text-slate-600">{{ $data['message'] ?? '' }}</div>
              <div class="text-xs text-slate-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              @if(($data['action_url'] ?? null))
                <a href="{{ $data['action_url'] }}" class="text-indigo-600 text-sm hover:underline">Buka</a>
              @endif
              @if(!$n->read_at)
                <form method="POST" action="{{ route('notifications.read',$n->id) }}">
                  @csrf
                  <button class="text-sm rounded-xl px-3 py-1.5 bg-emerald-600 text-white">Tandai dibaca</button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <div class="text-slate-500">Belum ada notifikasi.</div>
        @endforelse
      </div>

      <div class="mt-4">{{ $notifications->withQueryString()->links() }}</div>
    </div>
  </div>

  {{-- ====================== MOBILE (< md) ====================== --}}
  <div class="md:hidden">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Notifikasi</h1>
      <form method="POST" action="{{ route('notifications.readAll') }}">
        @csrf
        <button class="rounded-xl px-3 py-1.5 bg-emerald-600 text-white text-xs" @disabled($unread===0)>
          Tandai semua
        </button>
      </form>
    </div>

    @if (session('success'))
      <div class="rf-card mt-3 bg-green-50">{{ session('success') }}</div>
    @endif

    <div class="mt-3 space-y-3">
      @forelse($notifications as $n)
        @php $data = $n->data; @endphp
        <div class="rf-card {{ $n->read_at ? '' : 'ring-1 ring-yellow-200' }}">
          <div class="font-medium">{{ $data['title'] ?? 'Notifikasi' }}</div>
          <div class="text-sm text-slate-600">{{ $data['message'] ?? '' }}</div>
          <div class="text-xs text-slate-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>

          <div class="mt-2 flex items-center justify-between">
            @if(($data['action_url'] ?? null))
              <a href="{{ $data['action_url'] }}" class="text-indigo-600 text-sm">Buka</a>
            @else
              <span></span>
            @endif

            @if(!$n->read_at)
              <form method="POST" action="{{ route('notifications.read',$n->id) }}">
                @csrf
                <button class="rounded-xl px-3 py-1.5 bg-emerald-600 text-white text-xs">Tandai dibaca</button>
              </form>
            @endif
          </div>
        </div>
      @empty
        <div class="rf-card text-slate-500">Belum ada notifikasi.</div>
      @endforelse

      <div class="mt-4">{{ $notifications->withQueryString()->links() }}</div>
    </div>
  </div>
@endsection
