<section id="galeri" class="px-4 py-16 sm:px-6">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div><h2 class="text-3xl font-bold">Galeri kegiatan</h2><p class="mt-3 text-slate-600">Dokumentasi yang dibagikan oleh pengelola Rausyan Fikr.</p></div>
            <a href="{{ route('public.gallery.index') }}" class="font-semibold text-blue-800 underline underline-offset-4">Semua galeri</a>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($galleries as $gallery)
                <figure class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    @if ($gallery->public_image_url)
                        <img src="{{ $gallery->public_image_url }}" alt="{{ $gallery->title }}" width="800" height="600" loading="lazy" class="aspect-video w-full object-cover">
                    @else
                        <div class="flex aspect-video items-center justify-center bg-slate-100 p-4 text-slate-600">Gambar belum tersedia.</div>
                    @endif
                    <figcaption class="p-6">
                        <h3 class="break-words text-lg font-semibold">{{ $gallery->title }}</h3>
                        @if ($gallery->caption)
                            <p class="mt-2 break-words leading-relaxed text-slate-700">{{ $gallery->caption }}</p>
                        @endif
                    </figcaption>
                </figure>
            @empty
                <p class="rounded-lg bg-white p-6 text-slate-600 md:col-span-2 lg:col-span-3" role="status">Belum ada foto galeri.</p>
            @endforelse
        </div>
    </div>
</section>
