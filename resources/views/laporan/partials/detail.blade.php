    <article data-report-modal-content class="bg-white p-5 sm:p-7">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
            <div><p class="text-sm font-medium text-brand">Detail Laporan</p>
                <h1 id="report-modal-heading" tabindex="-1" class="mt-1 break-words text-2xl font-semibold text-ink">{{ $report->judul }}</h1>
                <p class="mt-2 font-medium text-indigo-700">@include('laporan.status', ['status' => $report->status])</p>
            </div>
            <a href="{{ route('laporan.index') }}" data-report-close aria-label="Tutup detail laporan" class="rf-btn-secondary shrink-0 px-3">Tutup</a>
        </div>
        @include('laporan.feedback')
        <dl class="mt-6 grid gap-5 sm:grid-cols-2">
            @foreach (['Tanggal kegiatan' => $report->tanggal->format('d/m/Y'), 'Wilayah' => $report->wilayah?->name ?? 'Wilayah belum ditentukan', 'Pelapor' => $report->user?->name, 'Tempat' => $report->tempat, 'Pemateri' => $report->pemateri, 'Durasi (menit)' => $report->durasi_menit, 'Peserta laki-laki' => $report->peserta_l, 'Peserta perempuan' => $report->peserta_p, 'Total peserta' => $report->total_peserta] as $label => $value)
                <div><dt class="text-sm text-slate-600">{{ $label }}</dt><dd class="mt-1 break-words font-medium text-slate-900">{{ $value ?? 'Belum diisi' }}</dd></div>
            @endforeach
        </dl>
        <h2 class="mt-6 font-semibold text-slate-900">Catatan kegiatan</h2>
        <p class="mt-2 whitespace-pre-line break-words text-slate-700">{{ $report->catatan ?? 'Belum diisi' }}</p>
        @if ($report->foto_url)
            <h2 class="mt-6 font-semibold text-slate-900">Foto kegiatan</h2>
            <div class="mt-3">
                <img data-report-photo src="{{ route('laporan.photo', $report) }}" alt="Dokumentasi kegiatan {{ $report->judul }}" class="max-h-80 w-full rounded-lg border border-slate-200 bg-slate-50 object-contain" loading="lazy">
                <p data-report-photo-placeholder hidden class="rounded-lg bg-slate-50 p-4 text-sm text-muted">Foto tidak tersedia.</p>
            </div>
        @else
            <p class="mt-6 rounded-lg bg-slate-50 p-4 text-sm text-muted">Belum ada foto dokumentasi.</p>
        @endif
        @if ($report->status === 'draft' && $report->user_id === auth()->id())
            <a href="{{ route('laporan.edit', $report) }}" data-report-modal data-report-tooltip="Edit draf dan foto laporan" class="rf-btn-secondary mt-6 px-4">Edit</a>
            <form data-report-form action="{{ route('laporan.submit', $report) }}" method="POST" class="mt-5 rounded-lg border border-slate-200 p-4">
                @csrf
                <div data-report-form-errors role="alert" tabindex="-1" hidden class="mb-3 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800"></div>
                <p class="text-slate-700">Setelah dikirim, isian laporan terkunci. Pastikan data dan foto sudah benar.</p>
                <label class="mt-3 flex items-start gap-2"><input type="checkbox" name="confirmation" value="1" required class="mt-1 rounded border-slate-300"><span>Saya sudah memeriksa laporan ini.</span></label>
                <button type="submit" data-report-tooltip="Kirim laporan untuk masuk antrean review" class="rf-btn mt-4 px-4">Kirim</button>
            </form>
        @elseif ($report->status === 'draft')
            <p class="mt-6 rounded-lg bg-slate-50 p-4 text-sm text-slate-700">Hanya pembuat draf yang dapat mengedit laporan ini.</p>
        @else
            <p class="mt-6 text-slate-700">Laporan sudah dikirim. Isian laporan terkunci.</p>
        @endif
        @if ($canReview)
            <section class="mt-6 rounded-lg border border-slate-200 p-4" aria-labelledby="decision-heading">
                <h2 id="decision-heading" class="font-semibold">Keputusan review</h2>
                <p class="mt-2 text-slate-700">Periksa isi dan dokumentasi sebelum mengambil keputusan. Keputusan yang tersimpan tidak dapat diubah.</p>
                <form data-report-form action="{{ route('laporan.approve', $report) }}" method="POST" class="mt-4">
                    @csrf
                    <div data-report-form-errors role="alert" tabindex="-1" hidden class="mb-3 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800"></div>
                    <button type="submit" data-report-tooltip="Setujui laporan; keputusan tidak dapat diubah" class="rf-btn px-4">Setujui</button>
                </form>
                <form data-report-form action="{{ route('laporan.reject', $report) }}" method="POST" class="mt-5">
                    @csrf
                    <div data-report-form-errors role="alert" tabindex="-1" hidden class="mb-3 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800"></div>
                    <label for="reject_reason" class="block font-medium">Alasan penolakan *</label>
                    <textarea id="reject_reason" name="reject_reason" required maxlength="16383" rows="4" class="mt-2 w-full rounded-lg border-slate-300">{{ old('reject_reason') }}</textarea>
                    <button type="submit" data-report-tooltip="Tolak laporan dengan alasan; keputusan tidak dapat diubah" class="rf-btn-danger mt-3 px-4">Tolak</button>
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
            <a href="{{ route('laporan.summary') }}" class="rf-btn-secondary mt-6 px-4">Rekap</a>
        @endcan
    </article>
