    <section data-report-modal-content class="bg-white p-5 sm:p-7" aria-labelledby="report-modal-heading">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <p class="text-sm font-medium text-brand">Laporan Kajian</p>
                <h1 id="report-modal-heading" tabindex="-1" class="mt-1 text-2xl font-semibold text-ink">{{ isset($report) ? 'Edit laporan' : 'Buat draf' }}</h1>
                <p class="mt-2 text-sm leading-6 text-muted">{{ ($administrativeEdit ?? false) ? 'Perubahan administratif dicatat. Status laporan dan hasil review dipertahankan.' : 'Simpan draf, periksa hasilnya, lalu kirim. Foto hanya dapat dilihat pengguna yang berhak.' }}</p>
            </div>
            <a href="{{ route('laporan.index') }}" data-report-close aria-label="Tutup formulir laporan" class="rf-btn-secondary shrink-0 px-3">Tutup</a>
        </div>
        @include('laporan.feedback')
        <form data-report-form action="{{ isset($report) ? route('laporan.update', $report) : route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="mt-5 grid gap-4 sm:grid-cols-2">
            <div data-report-form-errors role="alert" tabindex="-1" hidden class="rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800 sm:col-span-2"></div>
            @csrf
            @isset($report)
                @method('PUT')
            @endisset
            @if ($canChooseRegion)
                <div class="sm:col-span-2">
                    <label for="wilayah_id" class="block font-medium">Wilayah *</label>
                    <select id="wilayah_id" name="wilayah_id" required class="mt-2 w-full rounded-lg border-slate-300">
                        <option value="">Pilih wilayah</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" @selected((string) old('wilayah_id', $report->wilayah_id ?? '') === (string) $region->id)>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <p class="rounded-lg bg-slate-50 p-3 sm:col-span-2">Wilayah: {{ $regions->first()?->name ?? 'Wilayah belum ditentukan' }}</p>
            @endif
            <div class="sm:col-span-2">
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
            <div>
                <label for="durasi_menit" class="block font-medium">Durasi (menit)</label>
                <input id="durasi_menit" name="durasi_menit" type="number" value="{{ old('durasi_menit', $report->durasi_menit ?? '') }}" min="0" max="4294967295" step="1" class="mt-2 w-full rounded-lg border-slate-300">
            </div>
            <div class="grid gap-4 sm:col-span-2 sm:grid-cols-2">
                @foreach (['peserta_l' => 'Peserta laki-laki', 'peserta_p' => 'Peserta perempuan'] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="block font-medium">{{ $label }} *</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="number" value="{{ old($field, $report->{$field} ?? 0) }}" required min="0" max="2147483647" step="1" class="mt-2 w-full rounded-lg border-slate-300">
                    </div>
                @endforeach
            </div>
            <p class="text-sm text-slate-600 sm:col-span-2">Total peserta dihitung dari kedua jumlah peserta.</p>
            <div class="sm:col-span-2">
                <label for="catatan" class="block font-medium">Catatan kegiatan</label>
                <textarea id="catatan" name="catatan" rows="5" maxlength="16383" class="mt-2 w-full rounded-lg border-slate-300">{{ old('catatan', $report->catatan ?? '') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label for="foto" class="block font-medium">Foto kegiatan</label>
                <p id="foto-help" class="mt-1 text-sm text-slate-600">JPG atau PNG, maksimal 2 MB. Foto baru menggantikan foto lama setelah perubahan disimpan.</p>
                @if (isset($report) && $report->foto_url)
                    <p class="mt-1 text-sm text-slate-600">Foto lama dipertahankan jika tidak memilih foto baru.</p>
                @endif
                <input id="foto" name="foto" type="file" accept="image/jpeg,image/png" aria-describedby="foto-help" class="mt-2 block w-full text-sm">
                <div class="mt-3 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    <img data-report-photo-preview data-original-src="{{ isset($report) && $report->foto_url ? route('laporan.photo', $report) : '' }}" @if (isset($report) && $report->foto_url) src="{{ route('laporan.photo', $report) }}" @endif alt="Pratinjau foto kegiatan" class="h-40 w-full object-contain" @if (!isset($report) || !$report->foto_url) hidden @endif>
                    <p data-report-photo-placeholder class="p-4 text-center text-sm text-muted" @if (isset($report) && $report->foto_url) hidden @endif>Belum ada foto.</p>
                </div>
                <button type="button" data-report-photo-clear data-report-tooltip="Batalkan pemilihan foto baru" class="rf-btn-secondary mt-2 px-3" hidden>Batal</button>
            </div>
            <div class="sticky bottom-0 flex flex-wrap gap-2 border-t border-slate-200 bg-white py-4 sm:col-span-2">
                <button type="submit" class="rf-btn px-4">Simpan</button>
                <a href="{{ route('laporan.index') }}" data-report-close class="rf-btn-secondary px-4">Tutup</a>
            </div>
        </form>
    </section>
