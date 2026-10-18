@extends('layouts.app')

@section('page-content')
    <article class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow sm:p-8">
        <a href="{{ route('laporan.index') }}" class="text-indigo-700 hover:underline">Kembali ke laporan</a>
        @include('laporan.feedback')
        <h1 class="mt-5 break-words text-2xl font-semibold text-slate-900">{{ $report->judul }}</h1>
        <p class="mt-3 font-medium text-indigo-700">@include('laporan.status', ['status' => $report->status])</p>
        <dl class="mt-6 grid gap-5 sm:grid-cols-2">
            @foreach (['Tanggal kegiatan' => $report->tanggal->format('d/m/Y'), 'Wilayah' => $report->wilayah?->name ?? 'Wilayah belum ditentukan', 'Pelapor' => $report->user?->name, 'Tempat' => $report->tempat, 'Pemateri' => $report->pemateri, 'Durasi (menit)' => $report->durasi_menit, 'Peserta laki-laki' => $report->peserta_l, 'Peserta perempuan' => $report->peserta_p, 'Total peserta' => $report->total_peserta] as $label => $value)
                <div><dt class="text-sm text-slate-600">{{ $label }}</dt><dd class="mt-1 break-words font-medium text-slate-900">{{ $value ?? 'Belum diisi' }}</dd></div>
            @endforeach
        </dl>
        <h2 class="mt-6 font-semibold text-slate-900">Catatan kegiatan</h2>
        <p class="mt-2 whitespace-pre-line break-words text-slate-700">{{ $report->catatan ?? 'Belum diisi' }}</p>
        @if ($report->foto_url)
            <h2 class="mt-6 font-semibold text-slate-900">Foto kegiatan</h2>
            <img src="{{ route('laporan.photo', $report) }}" alt="Dokumentasi kegiatan {{ $report->judul }}" class="mt-3 h-auto max-w-full rounded-lg" loading="lazy">
        @endif
        @if ($report->status === 'draft' && $report->user_id === auth()->id())
            <a href="{{ route('laporan.edit', $report) }}" class="mt-6 inline-block rounded-lg border border-indigo-600 px-5 py-3 font-medium text-indigo-700 hover:bg-indigo-50">Edit draf</a>
            <form action="{{ route('laporan.submit', $report) }}" method="POST" class="mt-5 rounded-lg border border-slate-200 p-4">
                @csrf
                <p class="text-slate-700">Setelah dikirim, isian laporan terkunci. Pastikan data dan foto sudah benar.</p>
                <label class="mt-3 flex items-start gap-2"><input type="checkbox" name="confirmation" value="1" required class="mt-1 rounded border-slate-300"><span>Saya sudah memeriksa laporan ini.</span></label>
                <button type="submit" class="mt-4 rounded-lg bg-brand px-5 py-3 font-medium text-white hover:bg-brand-dark">Kirim laporan</button>
            </form>
        @elseif ($report->status !== 'draft')
            <p class="mt-6 text-slate-700">Laporan sudah dikirim. Isian laporan terkunci.</p>
        @endif
        @if ($canReview)
            <section class="mt-6 rounded-lg border border-slate-200 p-4" aria-labelledby="decision-heading">
                <h2 id="decision-heading" class="font-semibold">Keputusan review</h2>
                <p class="mt-2 text-slate-700">Periksa isi dan dokumentasi sebelum mengambil keputusan. Keputusan yang tersimpan tidak dapat diubah.</p>
                <form action="{{ route('laporan.approve', $report) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="rounded-lg bg-green-700 px-5 py-3 font-medium text-white hover:bg-green-800">Setujui laporan</button>
                </form>
                <form action="{{ route('laporan.reject', $report) }}" method="POST" class="mt-5">
                    @csrf
                    <label for="reject_reason" class="block font-medium">Alasan penolakan *</label>
                    <textarea id="reject_reason" name="reject_reason" required maxlength="16383" rows="4" class="mt-2 w-full rounded-lg border-slate-300">{{ old('reject_reason') }}</textarea>
                    <button type="submit" class="mt-3 rounded-lg bg-red-700 px-5 py-3 font-medium text-white hover:bg-red-800">Tolak laporan</button>
                </form>
            </section>
        @endif
        @if ($report->reviewed_at)
            <section class="mt-6 rounded-lg bg-slate-50 p-4" aria-labelledby="result-heading">
                <h2 id="result-heading" class="font-semibold">Hasil review</h2>
                <p class="mt-2 text-slate-700">{{ $report->reviewer?->name ?? 'Reviewer tidak tersedia' }} · {{ $report->reviewed_at->format('d/m/Y H:i') }}</p>
                @if ($report->status === 'rejected')
                    <h3 class="mt-3 font-medium">Alasan penolakan</h3>
                    <p class="mt-2 whitespace-pre-line break-words text-slate-700">{{ $report->reject_reason }}</p>
                @endif
            </section>
        @endif
        @can('review-report')
            <a href="{{ route('laporan.summary') }}" class="mt-6 inline-block text-indigo-700 hover:underline">Rekap dan ekspor</a>
        @endcan
    </article>
@endsection
