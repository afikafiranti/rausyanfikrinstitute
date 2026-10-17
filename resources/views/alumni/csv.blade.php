@extends('layouts.app')

@section('page-content')
<div class="mx-auto max-w-4xl space-y-6 p-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-semibold text-slate-900">Impor dan ekspor alumni</h1>
        <a href="{{ route('alumni.index') }}" class="text-indigo-700 underline">Kembali ke daftar alumni</a>
    </div>

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="text-lg font-semibold">Impor Excel</h2>
        <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm text-slate-700">
            <li><a href="{{ route('alumni.excel.template') }}" class="font-medium text-indigo-700 underline">Unduh template Excel (.xlsx)</a>.</li>
            <li>Isi data pada sheet Alumni tanpa mengganti header. Kolom telepon memakai format teks agar angka 0 di awal tetap tersimpan.</li>
            <li>Unggah berkas yang sudah diisi. Hasil dan baris yang gagal akan ditampilkan di halaman ini.</li>
        </ol>
        <p class="mt-3 text-sm text-slate-700">Maksimal 200 baris data dan ukuran berkas 1 MB. Akun baru berstatus pending dan perlu menyetel kata sandi melalui fitur lupa kata sandi. Impor hanya menambah akun baru; email atau telepon yang sudah digunakan akan ditolak.</p>
        <p class="mt-3 text-sm font-medium text-slate-800">Urutan kolom wajib:</p>
        <code class="mt-1 block overflow-x-auto rounded bg-slate-100 p-3 text-sm text-slate-900">name,email,wilayah,phone,angkatan,pekerjaan</code>
        <p class="mt-2 text-sm text-slate-700">Isi wilayah dengan nama yang terdaftar, misalnya Makassar. Nama, email, dan wilayah wajib diisi. Koorda boleh membiarkan wilayah kosong untuk menggunakan wilayah akunnya. Kolom role, status, level, dan password tidak didukung.</p>
        <p class="mt-2 text-sm text-slate-600">Template lama dengan kolom wilayah_id tetap diterima. Nama wilayah yang tidak ditemukan atau memiliki lebih dari satu kecocokan akan dilaporkan sebagai baris gagal.</p>
        <details class="mt-3 text-sm text-slate-700">
            <summary class="cursor-pointer font-medium">Lihat nama wilayah yang dapat digunakan</summary>
            <div class="mt-2 max-h-56 overflow-auto rounded border border-slate-200">
                <table class="w-full text-left">
                    <thead><tr><th scope="col" class="px-3 py-2">Nama wilayah</th></tr></thead>
                    <tbody>
                        @foreach ($wilayah as $region)
                            <tr class="border-t border-slate-200"><td class="px-3 py-2">{{ $region->name }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
        <form method="POST" action="{{ route('alumni.csv.import', request()->query()) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <label for="alumni_csv_file" class="block text-sm font-medium text-slate-800">Berkas Excel alumni (.xlsx)</label>
            <input id="alumni_csv_file" name="file" type="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,.csv,text/csv" required aria-describedby="alumni-file-help" class="block w-full rounded border border-slate-300 p-2 text-sm focus:border-indigo-600 focus:ring-indigo-600">
            <p id="alumni-file-help" class="text-sm text-slate-600">Gunakan template .xlsx. CSV UTF-8 juga tetap diterima; format Excel lama .xls tidak didukung. Header rusak membatalkan berkas, dan formula tidak dihitung. Pada CSV, nomor baris mengikuti urutan record termasuk header.</p>
            @error('file')<p role="alert" class="text-sm text-red-700">{{ $message }}</p>@enderror
            <button type="submit" class="rf-btn bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Impor alumni</button>
        </form>
    </section>

    @if ($imported !== null)
        <section class="rounded-xl bg-white p-5 shadow" aria-live="polite">
            <h2 class="font-semibold text-slate-900">Hasil impor</h2>
            <p class="mt-2 text-green-800">{{ $imported }} alumni berhasil diimpor.</p>
            @if ($failures)
                <p class="mt-3 font-medium text-red-800">{{ count($failures) }} baris gagal:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-800">
                    @foreach ($failures as $failure)
                        <li>Baris {{ $failure['line'] }}: {{ implode(' ', $failure['messages']) }}</li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif

    <section class="rounded-xl bg-white p-5 shadow">
        <h2 class="text-lg font-semibold">Ekspor Excel</h2>
        <p class="mt-2 text-sm text-slate-700">Unduh semua hasil sesuai wilayah dan filter dari daftar alumni, tanpa batas halaman. Kolom wilayah berisi nama wilayah. Telepon dan nilai lainnya disimpan sebagai teks.</p>
        <div class="mt-4 flex flex-wrap items-center gap-4">
            <a href="{{ route('alumni.excel.export', request()->query()) }}" class="rf-btn inline-block bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Unduh Excel (.xlsx)</a>
            <a href="{{ route('alumni.csv.export', request()->query()) }}" class="text-sm text-indigo-700 underline">Unduh CSV</a>
        </div>
    </section>
</div>
@endsection
