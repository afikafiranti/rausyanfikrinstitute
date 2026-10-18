@php
    $showSectionHeader = $showSectionHeader ?? true;
    $featured = $featured ?? true;
@endphp
<section id="{{ $isArticle ? 'artikel' : 'refleksi' }}" class="px-4 py-16 sm:px-6 sm:py-20">
    <div class="mx-auto max-w-6xl">
        @if ($showSectionHeader)
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4 sm:mb-10">
                <div>
                    <span class="mb-3 block h-1 w-10 bg-accent" aria-hidden="true"></span>
                    <h2 class="rf-page-title text-4xl sm:text-5xl">{{ $isArticle ? 'Artikel' : 'Refleksi' }}</h2>
                    <p class="mt-3 text-muted">Tulisan dan catatan yang telah diterbitkan oleh pengelola.</p>
                </div>
                <a href="{{ route($isArticle ? 'public.articles.index' : 'public.posts.index') }}" class="rf-text-action">Semua {{ $isArticle ? 'artikel' : 'refleksi' }} <span aria-hidden="true">&rarr;</span></a>
            </div>
        @endif
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-12 lg:gap-6">
            @forelse ($posts as $post)
                @php($isFeatured = $featured && $loop->first && $post->public_cover_url)
                <article class="rf-card min-w-0 overflow-hidden p-0 {{ $featured ? ($isFeatured ? 'sm:col-span-2 lg:col-span-6 lg:row-span-3' : 'lg:col-span-6') : 'lg:col-span-4' }} {{ $isFeatured ? 'border-t-4 border-t-accent' : '' }}">
                    <div class="{{ $featured && ! $isFeatured ? 'sm:flex sm:h-full' : '' }}">
                        @if ($post->public_cover_url)
                            <a href="{{ route($isArticle ? 'public.articles.show' : 'public.posts.show', $post->slug) }}" tabindex="-1" aria-hidden="true" class="block shrink-0 {{ $featured && ! $isFeatured ? 'sm:w-2/5' : '' }}">
                                <img src="{{ $post->public_cover_url }}" alt="" width="800" height="450" loading="lazy" class="w-full object-cover {{ $isFeatured ? 'aspect-[16/10]' : ($featured ? 'aspect-video sm:h-full sm:aspect-auto' : 'aspect-video') }}">
                            </a>
                        @endif
                        <div class="min-w-0 p-5 {{ $isFeatured ? 'sm:p-8' : 'sm:p-6' }}">
                            <time datetime="{{ $post->published_at->toAtomString() }}" class="text-sm font-medium text-muted">{{ $post->published_at->locale('id')->translatedFormat('d F Y') }}</time>
                            <h3 class="mt-3 break-words font-bold leading-snug text-ink {{ $isFeatured ? 'text-2xl sm:text-3xl' : 'text-xl' }}"><a href="{{ route($isArticle ? 'public.articles.show' : 'public.posts.show', $post->slug) }}" class="rounded-sm hover:text-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">{{ $post->title }}</a></h3>
                            <p class="mt-3 break-words leading-relaxed text-muted">{{ $post->excerpt ? Str::limit($post->excerpt, $isFeatured ? 220 : 130) : ($isArticle ? 'Baca artikel selengkapnya.' : 'Baca refleksi selengkapnya.') }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <p class="rounded-xl border border-ink/15 bg-white p-6 text-muted sm:col-span-2 lg:col-span-12" role="status">Belum ada {{ $isArticle ? 'artikel' : 'refleksi' }} yang diterbitkan.</p>
            @endforelse
        </div>
    </div>
</section>
