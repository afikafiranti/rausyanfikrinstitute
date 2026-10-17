@extends('layouts.app')

@section('page-content')
    <section class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow sm:p-8" aria-labelledby="report-heading">
        <a href="{{ route('laporan.index') }}" class="text-indigo-700 hover:underline">Kembali ke laporan</a>
        <h1 id="report-heading" class="mt-4 text-2xl font-semibold text-slate-900">{{ isset($report) ? 'Edit draf laporan kajian' : 'Buat draf laporan kajian' }}</h1>
        <p class="mt-3 text-slate-700">Simpan dan periksa draf sebelum mengirimnya dari halaman detail. Foto hanya dapat dilihat oleh pengguna yang berhak mengakses laporan.</p>
        @include('laporan.feedback')
        <form action="{{ isset($report) ? route('laporan.update', $report) : route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">
            @csrf
            @isset($report)
                @method('PUT')
            @endisset
            @if ($canChooseRegion)
                <div>
                    <label for="wilayah_id" class="block font-medium">Wilayah *</label>
                    <select id="wilayah_id" name="wilayah_id" required class="mt-2 w-full rounded-lg border-slate-300">
                        <option value="">Pilih wilayah</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" @selected((string) old('wilayah_id') === (string) $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <p class="rounded-lg bg-slate-50 p-3">Wilayah: {{ $regions->first()?->name ?? 'Wilayah belum ditentukan' }}</p>
            @endif
            <div>
                <label for="judul" class="block font-medium">Judul kajian *</label>
                <input id="judul" name="judul" value="{{ old('judul', $report->judul ?? '') }}" required maxlength="180" class="mt-2 w-full rounded-lg border-slate-300">
            </div>
            <div>
                <label for="tanggal" class="block font-medium">Tanggal kegiatan *</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', isset($report) ? $report->tanggal->format('Y-m-d') : '') }}" required min="1000-01-01" max="9999-12-31" class="mt-2 w-full rounded-lg border-slate-300">
            </div>
            @foreach (['tempat' => 'Tempat', 'pemateri' => 'Pemateri'] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="block font-medium">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $report->{$field} ?? '') }}" maxlength="150" class="mt-2 w-full rounded-lg border-slate-300">
                </div>
            @endforeach
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach (['peserta_l' => 'Peserta laki-laki', 'peserta_p' => 'Peserta perempuan'] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="block font-medium">{{ $label }} *</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="number" value="{{ old($field, $report->{$field} ?? 0) }}" required min="0" max="2147483647" step="1" class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-slate-600">Total peserta dihitung dari kedua jumlah peserta.</p>
            <div>
                <label for="durasi_menit" class="block font-medium">Durasi (menit)</label>
                <input id="durasi_menit" name="durasi_menit" type="number" value="{{ old('durasi_menit', $report->durasi_menit ?? '') }}" min="0" max="4294967295" step="1" class="mt-2 w-full rounded-lg border-slate-300">
            </div>
            <div>
                <label for="catatan" class="block font-medium">Catatan kegiatan</label>
                <textarea id="catatan" name="catatan" rows="5" maxlength="16383" class="mt-2 w-full rounded-lg border-slate-300">{{ old('catatan', $report->catatan ?? '') }}</textarea>
            </div>
            <div>
                <label for="foto" class="block font-medium">Foto kegiatan</label>
                <p id="foto-help" class="mt-1 text-sm text-slate-600">JPG atau PNG, maksimal 2 MB. Pilih ulang foto jika formulir perlu diperbaiki.</p>
                @if (isset($report) && $report->foto_url)
                    <p class="mt-1 text-sm text-slate-600">Foto lama dipertahankan jika tidak memilih foto baru.</p>
                @endif
                <input id="foto" name="foto" type="file" accept="image/jpeg,image/png" aria-describedby="foto-help" class="mt-2 block w-full text-sm">
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-3 font-medium text-white hover:bg-indigo-700">Simpan draf</button>
        </form>
    </section>
@endsection
