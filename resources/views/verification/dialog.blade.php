<dialog id="verification-dialog" aria-labelledby="verification-heading" class="rf-verification-dialog w-[min(96vw,36rem)] max-h-[92dvh] max-w-none overflow-y-auto rounded-xl border border-slate-200 bg-white p-5 shadow-2xl sm:p-6">
    <header class="flex items-start justify-between gap-3"><div class="min-w-0"><h2 id="verification-heading" tabindex="-1" class="text-xl font-semibold">Verifikasi akun</h2><p data-verification-name class="mt-2 break-words font-medium"></p><p data-verification-email class="mt-1 break-words text-sm text-muted"></p></div><button type="button" data-verification-close class="rf-btn-secondary shrink-0 px-3">Tutup</button></header>
    <p data-verification-result role="status" tabindex="-1" hidden class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"></p>
    @include('verification.forms', ['target' => null])
</dialog>
