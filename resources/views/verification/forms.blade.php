@php($restore = $target && (string) old('_verification_target') === (string) $target->id)
<section data-alumni-modal-content class="p-5 sm:p-6">
<h2 id="alumni-modal-heading" tabindex="-1" class="pr-20 text-xl font-semibold">Verifikasi akun</h2>
<p class="mt-3 break-words font-semibold">{{ $target->name }}</p><p class="mt-1 break-words text-sm text-muted">{{ $target->email }}</p>
<div data-verification-forms>
    @if ($restore && $errors->any())<div role="alert" class="mt-4 rounded-lg bg-red-50 p-3 text-red-800"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form data-alumni-form data-alumni-confirm-title="Setujui akun?" data-alumni-confirm="Akun akan diaktifkan sesuai level yang dipilih." data-alumni-confirm-label="Setujui" data-verification-form="approve" action="{{ $target ? route('verification.approve', $target) : '' }}" method="POST" class="mt-5 space-y-3">
        @csrf
        @if ($target)<input type="hidden" name="_return" value="{{ $returnTo }}">@endif
        <input type="hidden" name="_verification_target" value="{{ $target?->id }}">
        <div data-verification-errors role="alert" tabindex="-1" hidden class="rounded-lg bg-red-50 p-3 text-sm text-red-800"></div>
        <div><label for="verification-level" class="block text-sm font-medium">Level awal *</label><select id="verification-level" name="level_id" required class="mt-1 w-full"><option value="">Pilih level</option>@foreach ($levels as $level)<option value="{{ $level->id }}" @selected($restore && (string) old('level_id') === (string) $level->id)>{{ $level->description ?: $level->name }}</option>@endforeach</select></div>
        <div><label for="verification-note" class="block text-sm font-medium">Catatan (opsional)</label><textarea id="verification-note" name="note" maxlength="500" rows="2" class="mt-1 w-full">{{ $restore ? old('note') : '' }}</textarea></div>
        @if ($levels->isEmpty())<p class="text-sm text-amber-800">Belum ada level aktif untuk persetujuan.</p>@endif
        <button type="submit" class="rf-btn" @disabled($levels->isEmpty())>Setujui</button>
    </form>
    <form data-alumni-form data-alumni-confirm-title="Kirim alasan?" data-alumni-confirm="Alasan perbaikan dikirim kepada pemilik akun. Akun tetap menunggu verifikasi." data-alumni-confirm-label="Kirim" data-verification-form="reject" action="{{ $target ? route('verification.reject', $target) : '' }}" method="POST" class="mt-6 space-y-3 border-t border-slate-200 pt-5">
        @csrf
        @if ($target)<input type="hidden" name="_return" value="{{ $returnTo }}">@endif
        <input type="hidden" name="_verification_target" value="{{ $target?->id }}">
        <div data-verification-errors role="alert" tabindex="-1" hidden class="rounded-lg bg-red-50 p-3 text-sm text-red-800"></div>
        <div><label for="verification-reason" class="block text-sm font-medium">Alasan perbaikan *</label><textarea id="verification-reason" name="reason" required minlength="5" maxlength="500" rows="3" aria-describedby="verification-reject-help" class="mt-1 w-full">{{ $restore ? old('reason') : '' }}</textarea></div>
        <p id="verification-reject-help" class="text-sm leading-6 text-muted">Alasan dikirim kepada pemilik akun. Akun tetap menunggu verifikasi sampai diperbaiki.</p>
        <button type="submit" class="rf-btn-danger">Tolak</button>
    </form>
</div>
</section>
