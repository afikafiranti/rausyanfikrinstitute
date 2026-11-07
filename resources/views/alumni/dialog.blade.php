<dialog id="alumni-dialog" data-auto-create="{{ old('_form') === 'alumni-create' && $errors->any() ? 'true' : 'false' }}" aria-labelledby="alumni-modal-heading" class="rf-alumni-dialog relative max-h-[92dvh] w-[min(96vw,48rem)] max-w-none overflow-y-auto rounded-xl border border-slate-200 bg-white p-0 shadow-2xl">
    <button type="button" data-alumni-close class="rf-btn-secondary absolute right-4 top-4 z-10 px-3">Tutup</button>
    <div data-alumni-content></div>
    <p data-alumni-dialog-status role="status" aria-live="polite" class="sr-only"></p>
</dialog>
