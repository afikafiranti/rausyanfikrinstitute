<dialog id="rf-feedback-dialog" class="rf-feedback-dialog" aria-labelledby="rf-feedback-title" aria-describedby="rf-feedback-message">
    <div class="rf-feedback-panel">
        <h2 id="rf-feedback-title" class="text-xl font-semibold text-ink"></h2>
        <p id="rf-feedback-message" class="mt-3 text-sm leading-6 text-muted"></p>
        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <button type="button" data-feedback-cancel class="rf-btn-secondary">Batal</button>
            <button type="button" data-feedback-confirm class="rf-btn">Lanjut</button>
        </div>
    </div>
</dialog>
<div id="rf-feedback-toasts" class="rf-feedback-toasts" popover="manual" aria-label="Notifikasi"></div>
@php
    $flash = session('success') ?: session('error');
    $flashType = session('success') ? 'success' : 'error';
    $status = session('status');
    if (!$flash && $status) {
        $flash = match ($status) {
            'verification-link-sent' => 'Tautan verifikasi telah dikirim.',
            'verification-link-failed' => 'Tautan verifikasi gagal dikirim.',
            'profile-updated' => 'Profil diperbarui.',
            'password-updated' => 'Kata sandi diperbarui.',
            default => is_string($status) ? $status : null,
        };
        $flashType = $status === 'verification-link-failed' ? 'error' : 'success';
    }
@endphp
@if ($flash)
    <span hidden data-feedback-flash data-feedback-type="{{ $flashType }}">{{ $flash }}</span>
@endif
@if ($errors->any())
    <span hidden data-feedback-flash data-feedback-type="error">Permintaan belum berhasil. Periksa isian yang ditandai.</span>
@endif
