<section id="refleksi" class="bg-white px-4 py-16 sm:px-6">
    <div class="mx-auto max-w-6xl">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div><h2 class="text-3xl font-bold">Refleksi</h2><p class="mt-3 text-slate-600">Tulisan dan catatan yang telah diterbitkan oleh pengelola.</p></div>
            <a href="{{ route('public.posts.index') }}" class="font-semibold text-blue-800 underline underline-offset-4">Semua refleksi</a>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <article class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                    @if ($post->public_cover_url)
                        <a href="{{ route('public.posts.show', $post->slug) }}" tabindex="-1" aria-hidden="true">
                            <img src="{{ $post->public_cover_url }}" alt="" width="800" height="450" loading="lazy" class="aspect-video w-full object-cover">
                        </a>
                    @endif
                    <div class="p-6">
                        <time datetime="{{ $post->published_at->toAtomString() }}" class="text-sm text-slate-600">{{ $post->published_at->locale('id')->translatedFormat('d F Y') }}</time>
                        <h3 class="mt-3 break-words text-xl font-semibold"><a href="{{ route('public.posts.show', $post->slug) }}" class="text-blue-900 hover:underline">{{ $post->title }}</a></h3>
                        <p class="mt-3 break-words leading-relaxed text-slate-700">{{ $post->excerpt ? Str::limit($post->excerpt, 200) : 'Baca refleksi selengkapnya.' }}</p>
                    </div>
                </article>
            @empty
                <p class="rounded-lg bg-slate-50 p-6 text-slate-600 md:col-span-2 lg:col-span-3" role="status">Belum ada refleksi yang diterbitkan.</p>
            @endforelse
        </div>
    </div>
</section>
