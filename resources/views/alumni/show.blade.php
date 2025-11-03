@extends('layouts.app')

@section('page-content')
<div class="max-w-5xl mx-auto bg-white shadow rounded-2xl overflow-hidden mt-6">
    {{-- Header dengan foto dan info utama --}}
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 p-6 bg-gradient-to-r from-slate-200 to-slate-100 text-slate-800">
        {{-- Foto Profil --}}
        <div class="flex-shrink-0">
            <img 
                src="{{ $alumni->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($alumni->name) . '&background=9CA3AF&color=fff&size=200' }}"
                alt="Foto {{ $alumni->name }}"
                class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md"
            >
        </div>

        {{-- Info dasar --}}
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-3xl font-semibold mb-1">{{ $alumni->name }}</h2>
            <p class="text-slate-500 text-sm mb-3">Angkatan {{ $alumni->angkatan ?? '-' }}</p>
            
            {{-- Status --}}
            @php
                $badgeColor = [
                    'active' => 'green',
                    'pending' => 'yellow',
                    'suspended' => 'red'
                ][$alumni->status] ?? 'blue';
            @endphp
            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-{{ $badgeColor }}-100 text-{{ $badgeColor }}-800 capitalize">
                {{ $alumni->status }}
            </span>
        </div>
    </div>

    {{-- Detail Data --}}
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div>
                <h4 class="text-sm text-slate-500">Email</h4>
                <p class="font-medium text-slate-800">{{ $alumni->email ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">No. Telepon</h4>
                <p class="font-medium text-slate-800">{{ $alumni->phone ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">Tempat, Tanggal Lahir</h4>
                <p class="font-medium text-slate-800">
                    {{ $alumni->tempat_lahir ?? '-' }}, 
                    {{ $alumni->tanggal_lahir ? \Carbon\Carbon::parse($alumni->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                </p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">Wilayah</h4>
                <p class="font-medium text-slate-800">{{ optional($alumni->wilayah)->name ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">Level</h4>
                <p class="font-medium text-slate-800">{{ optional($alumni->level)->description ?? '-' }}</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <h4 class="text-sm text-slate-500">Pekerjaan</h4>
                <p class="font-medium text-slate-800">{{ $alumni->pekerjaan ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">Pendidikan Terakhir</h4>
                <p class="font-medium text-slate-800">{{ $alumni->pendidikan_terakhir ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">Kampus</h4>
                <p class="font-medium text-slate-800">{{ $alumni->kampus ?? '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm text-slate-500">Status Pernikahan</h4>
                <p class="font-medium text-slate-800">{{ $alumni->status_pernikahan ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Tombol kembali --}}
    <div class="p-6 border-t text-center bg-slate-50">
        <a href="{{ route('alumni.index') }}"
           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg shadow-sm border border-slate-200 transition">
            ← Kembali ke Daftar Alumni
        </a>
    </div>
</div>
@endsection

