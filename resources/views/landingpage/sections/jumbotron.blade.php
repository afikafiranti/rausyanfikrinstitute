<section id="home" class="relative overflow-hidden bg-ink px-4 py-16 text-white sm:px-6 sm:py-24 lg:py-28">
    <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-1/3 border-l border-white/10 bg-white/[0.03] lg:block" aria-hidden="true"></div>
    <div class="relative mx-auto grid max-w-6xl items-end gap-12 lg:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.65fr)] lg:gap-16">
        <div>
            <p class="mb-5 flex items-center gap-3 text-sm font-bold uppercase tracking-[0.18em] text-white"><span class="h-1 w-8 bg-accent" aria-hidden="true"></span>Bangkit Bersaksi</p>
            <h1 class="max-w-3xl text-5xl font-extrabold leading-[1.05] tracking-tight sm:text-6xl lg:text-7xl">Rausyan Fikr <span class="text-white/75">Institute</span></h1>
            <p class="mt-7 max-w-2xl text-lg leading-relaxed text-white/85 sm:text-xl">Ruang alumni untuk merawat pengetahuan, berbagi artikel, dan mendokumentasikan kegiatan kajian.</p>
            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="{{ route('public.articles.index') }}" class="rf-btn justify-center focus-visible:outline-white">Baca artikel <span aria-hidden="true">&rarr;</span></a>
                <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-white/60 px-6 py-3 font-semibold text-white transition-colors hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Masuk area anggota</a>
            </div>
        </div>
        <blockquote class="border-l-4 border-accent bg-white/[0.08] p-6 text-lg leading-relaxed text-white sm:p-8 lg:mb-2">
            <p>“Setiap ajaran yang mempercayai dan meyakini kebenarannya harus melindungi kebebasan berfikir dan berkepercayaan.”</p>
            <cite class="mt-6 block text-sm font-semibold not-italic text-white/80">Murtadha Muthahhari</cite>
        </blockquote>
    </div>
</section>
