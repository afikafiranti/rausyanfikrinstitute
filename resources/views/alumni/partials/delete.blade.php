<section data-alumni-modal-content class="p-5 sm:p-6">
    <h2 id="alumni-modal-heading" tabindex="-1" class="pr-20 text-xl font-semibold">Hapus alumni</h2>
    <p class="mt-4 break-words font-semibold">{{ $alumni->name }}</p>
    <p class="mt-1 break-words text-sm text-muted">{{ $alumni->email }}</p>
    <p class="mt-4 text-sm leading-6">Akun dan aksesnya akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
    <form data-alumni-form data-alumni-delete method="POST" action="{{ route('alumni.destroy', $alumni) }}" class="mt-6">
        @csrf @method('DELETE')
        <div class="flex flex-wrap justify-end gap-2"><a data-alumni-close href="{{ route('alumni.index') }}" class="rf-btn-secondary">Batal</a><button type="submit" class="rf-btn-danger">Hapus</button></div>
    </form>
</section>
