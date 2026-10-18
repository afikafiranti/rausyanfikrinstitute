@php
    $showSectionHeader = $showSectionHeader ?? true;
    $featuredGallery = $featuredGallery ?? true;
@endphp
<section id="galeri" class="bg-white px-4 py-16 sm:px-6 sm:py-20">
    <div class="mx-auto max-w-6xl">
        @if ($showSectionHeader)
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4 sm:mb-10">
                <div>
                    <span class="mb-3 block h-1 w-10 bg-accent" aria-hidden="true"></span>
                    <h2 class="rf-page-title text-4xl sm:text-5xl">Galeri kajian</h2>
                    <p class="mt-3 text-muted">Dokumentasi yang dibagikan oleh pengelola Rausyan Fikr.</p>
                </div>
                <a href="{{ route('public.gallery.index') }}" class="rf-text-action">Semua galeri <span aria-hidden="true">&rarr;</span></a>
            </div>
        @endif
        <div class="grid gap-5 sm:grid-cols-2 lg:gap-6 {{ $featuredGallery ? 'lg:grid-cols-6' : 'lg:grid-cols-3' }}">
            @forelse ($galleries as $gallery)
                @php($isFeatured = $featuredGallery && $loop->first)
                <figure class="rf-card min-w-0 overflow-hidden p-0 {{ $featuredGallery ? ($isFeatured ? 'sm:col-span-2 lg:col-span-4' : ($loop->iteration === 4 ? 'lg:col-span-4' : 'lg:col-span-2')) : '' }}">
                    @if ($gallery->public_image_url)
                        <img src="{{ $gallery->public_image_url }}" alt="{{ $gallery->title }}" width="800" height="600" loading="lazy" class="w-full object-cover {{ $isFeatured ? 'aspect-[4/3] sm:aspect-[16/10]' : 'aspect-[4/3]' }} {{ $featuredGallery ? 'lg:h-64' : '' }}">
                    @else
                        <div class="flex aspect-[4/3] items-center justify-center bg-canvas p-4 text-muted">Gambar belum tersedia.</div>
                    @endif
                    <figcaption class="p-5 {{ $isFeatured ? 'sm:p-7' : '' }}">
                        <h3 class="break-words font-bold text-ink {{ $isFeatured ? 'text-xl sm:text-2xl' : 'text-lg' }}">{{ $gallery->title }}</h3>
                        @if ($gallery->caption)
                            <p class="mt-2 break-words leading-relaxed text-muted">{{ $gallery->caption }}</p>
                        @endif
                    </figcaption>
                </figure>
            @empty
                <p class="rounded-xl border border-ink/15 bg-canvas p-6 text-muted sm:col-span-2 {{ $featuredGallery ? 'lg:col-span-6' : 'lg:col-span-3' }}" role="status">Belum ada foto galeri.</p>
            @endforelse
        </div>
    </div>
</section>
