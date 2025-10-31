<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#000000" />
    <link rel="shortcut icon" href="../assets/img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png" />
    @if (!app()->environment('testing'))
        @vite(['resources/assets/tailwind.css', 'resources/assets/fontawesome-free/css/all.min.css', 'resources/js/landing.js', 'resources/js/app.js'])
    @endif
    <link rel="stylesheet" href="https://unpkg.com/swiper@10/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper@10/swiper-bundle.min.js"></script>

    <title>Rausyan Fikr </title>
</head>


<body class="text-blueGray-700 antialiased">
    {{-- ======================= NAVBAR ======================= --}}
    <nav class="top-0 absolute z-50 w-full flex flex-wrap items-center justify-between px-2 py-3 navbar-expand-lg">
        <div class="container px-4 mx-auto flex flex-wrap items-center justify-between">
            <div class="w-full relative flex justify-between lg:w-auto lg:static lg:block lg:justify-start">
                <a href="{{ route('landing') }}"
                    class="text-sm font-bold leading-relaxed inline-block mr-4 py-2 whitespace-nowrap uppercase text-white">
                    Rausyan Fikr
                </a>
                <button
                    class="cursor-pointer text-xl leading-none px-3 py-1 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none focus:outline-none"
                    type="button" onclick="toggleNavbar('nav-collapse')">
                    <i class="text-white fas fa-bars"></i>
                </button>
            </div>

            <div class="lg:flex flex-grow items-center bg-white lg:bg-opacity-0 lg:shadow-none hidden"
                id="nav-collapse">
                <ul class="flex flex-col lg:flex-row list-none mr-auto">
                    <li><a href="#tentang"
                            class="lg:text-white px-3 py-4 lg:py-2 text-xs uppercase font-bold">Tentang</a></li>
                    <li><a href="#fitur" class="lg:text-white px-3 py-4 lg:py-2 text-xs uppercase font-bold">Fitur</a>
                    </li>
                    <li><a href="#kontak" class="lg:text-white px-3 py-4 lg:py-2 text-xs uppercase font-bold">Kontak</a>
                    </li>
                </ul>

                <ul class="flex flex-col lg:flex-row list-none lg:ml-auto items-center">
                    <li><a href="/login"
                            class="bg-white text-blueGray-700 text-xs font-bold uppercase px-4 py-2 rounded shadow hover:shadow-md ml-3 mb-3 lg:mb-0">Masuk</a>
                    </li>
                    <li><a href="/register"
                            class="bg-emerald-500 text-white text-xs font-bold uppercase px-4 py-2 rounded shadow hover:shadow-md ml-3 mb-3 lg:mb-0">Daftar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    {{-- ======================= HERO (jumbotron) ======================= --}}
    <main>
        <div class="relative pt-16 pb-32 flex content-center items-center justify-center min-h-screen-75 bg-blueGray-200 ">
            <div class="absolute top-0 w-full h-full bg-center bg-cover"
                style="background-image: url('{{ asset('assets/img/hero.jpg') }}')">
                <span class="w-full h-full absolute opacity-75 bg-black"></span>
            </div>

            <div class="container relative mx-auto">
                <div class="items-center flex flex-wrap">
                    <div class="w-full lg:w-6/12 px-4 ml-auto mr-auto text-center">
                        <h1 class="text-white font-semibold text-5xl">Komunitas Kajian Rausyan Fikr</h1>
                        <p class="mt-4 text-lg text-blueGray-200">
                            Wadah silaturahmi, kajian, dan kolaborasi untuk membangun kontribusi nyata.
                        </p>
                        <div class="mt-6">
                            <a href="/register"
                                class="bg-emerald-500 text-white font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg mr-4">Gabung
                                Sekarang</a>
                            <a href="/login"
                                class="bg-white text-blueGray-700 font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg">Masuk</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="top-auto bottom-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden  h-70-px">
                <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 2560 100">
                    <polygon class="text-blueGray-200 fill-current" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>
        {{-- ======================= SECTION: FITUR ======================= --}}
        <section id="fitur" class="pb-20 bg-blueGray-200 -mt-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-semibold text-center mb-12">Temukan Manfaatnya</h2>
                <div class="flex flex-wrap">
                    @php
                        $features = [
                            [
                                'icon' => 'fa-users',
                                'color' => 'emerald',
                                'title' => 'Sharing Ilmu',
                                'text' => 'Dapatkan berbagai ilmu.',
                            ],
                            [
                                'icon' => 'fa-book',
                                'color' => 'lightBlue',
                                'title' => 'Materi Kajian',
                                'text' => 'Kumpulan materi kajian yang dapat dipelajari kapan saja.',
                            ],
                            [
                                'icon' => 'fa-file-alt',
                                'color' => 'pink',
                                'title' => 'Ikut Kegiatan ',
                                'text' => 'Ikuti kegiatan sosial dan berkontribusi di wilayah masing-masing.',
                            ],
                        ];
                    @endphp
                    @foreach ($features as $f)
                        <div class="w-full md:w-4/12 px-4 text-center">
                            <div class="relative flex flex-col bg-white w-full mb-8 shadow-lg rounded-lg">
                                <div class="px-4 py-5 flex-auto">
                                    <div
                                        class="text-white p-3 inline-flex items-center justify-center w-12 h-12 mb-5 shadow-lg rounded-full bg-{{ $f['color'] }}-500">
                                        <i class="fas {{ $f['icon'] }}"></i>
                                    </div>
                                    <h6 class="text-xl font-semibold">{{ $f['title'] }}</h6>
                                    <p class="mt-2 mb-4 text-blueGray-500">{{ $f['text'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ======================= Galeri ======================= --}}
        <section class="py-10 bg-gray-50">
            <div class="container mx-auto max-w-screen-xl px-4">
                <p class="py-2 font-semibold text-2xl">Galeri</p>
                <p class="py-1 font-medium text-gray-600">Dokumentasi kegiatan.</p>

                <!-- Wrapper card -->
                <div class="flex gap-6  overflow-x-auto py-6 hide-scrollbar">

                    <!-- Card 1 -->
                    <div
                        class="min-w-[18rem] max-w-[18rem] min-h-[10rem] max-h-[10rem] bg-white border border-gray-200 rounded-lg shadow hover:-translate-y-1 transition-transform duration-300">
                        <img src="https://source.unsplash.com/random/300x200?news" alt="Berita 1"
                            class="w-full h-32 object-cover rounded-t-lg">
                        <div class="px-4 py-3 text-gray-800">
                            <p class="text-lg font-bold mb-1">Judul Berita Pertama</p>
                            <p class="text-xs text-gray-500 italic mb-2">31 Oktober 2025</p>
                            <p class="text-sm text-gray-700">Ini adalah contoh deskripsi singkat dari berita pertama
                                yang membahas hal-hal menarik dan informatif.</p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="min-w-[18rem] max-w-[18rem] bg-white border border-gray-200 rounded-lg shadow hover:-translate-y-1 transition-transform duration-300">
                        <img src="https://source.unsplash.com/random/300x200?city" alt="Berita 2"
                            class="w-full h-32 object-cover rounded-t-lg">
                        <div class="px-4 py-3 text-gray-800">
                            <p class="text-lg font-bold mb-1">Kegiatan Sosial di Kota</p>
                            <p class="text-xs text-gray-500 italic mb-2">30 Oktober 2025</p>
                            <p class="text-sm text-gray-700">Kegiatan sosial yang diselenggarakan masyarakat untuk
                                membantu sesama dalam berbagai bentuk kegiatan positif.</p>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div
                        class="min-w-[18rem] max-w-[18rem] bg-white border border-gray-200 rounded-lg shadow hover:-translate-y-1 transition-transform duration-300">
                        <img src="https://source.unsplash.com/random/300x200?laundry" alt="Berita 3"
                            class="w-full h-32 object-cover rounded-t-lg">
                        <div class="px-4 py-3 text-gray-800">
                            <p class="text-lg font-bold mb-1">Tips Mencuci Pakaian</p>
                            <p class="text-xs text-gray-500 italic mb-2">29 Oktober 2025</p>
                            <p class="text-sm text-gray-700">Berbagai tips praktis agar pakaian tetap bersih, lembut,
                                dan awet saat dicuci menggunakan mesin cuci.</p>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div
                        class="min-w-[18rem] max-w-[18rem] bg-white border border-gray-200 rounded-lg shadow hover:-translate-y-1 transition-transform duration-300">
                        <img src="https://source.unsplash.com/random/300x200?clean" alt="Berita 4"
                            class="w-full h-32 object-cover rounded-t-lg">
                        <div class="px-4 py-3 text-gray-800">
                            <p class="text-lg font-bold mb-1">Inovasi Terbaru Laundry</p>
                            <p class="text-xs text-gray-500 italic mb-2">28 Oktober 2025</p>
                            <p class="text-sm text-gray-700">Teknologi baru dalam industri laundry yang membuat proses
                                lebih cepat dan hemat energi.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>



        {{-- ======================= SECTION: Berita ======================= --}}
        <section class="py-12 bg-gray-50" id="galeri">
            <div class="container mx-auto max-w-screen-xl px-4">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Berita & Informasi</h2>
                    <p class="py-1 font-medium">Kumpulan informasi dan update terbaru dari kami.</p>

                </div>
                @php
                    $first = $posts[0] ?? null;
                    $second = $posts[1] ?? null;
                    $third = $posts[2] ?? null;

                    function postUrl($post)
                    {
                        if (!$post) {
                            return asset('assets/img/default-img.jpg');
                        }

                        if (isset($post->cover_url)) {
                            return Str::startsWith($post->cover_url, ['http', 'https'])
                                ? $post->cover_url
                                : asset('storage/' . $post->cover_url);
                        }

                        return asset('assets/img/default-img.jpg');
                    }
                @endphp

                <div class="grid grid-cols-2 gap-4 h-[320px] md:h-[380px]">
                    {{-- Kiri --}}
                    <section class="col-span-1 rounded-xl bg-white p-4 shadow">
                        @if ($first)
                            <div class="relative rounded-xl overflow-hidden shadow-lg group h-full">
                                <img src="{{ postUrl($first) }}" alt="{{ $first->title }}"
                                    class="w-full h-full object-cover object-center transition duration-500 group-hover:scale-105">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent">
                                </div>
                                <div class="absolute bottom-0 p-4 text-white z-10">
                                    <h3 class="text-lg font-semibold leading-snug mb-1">
                                        <a href="#"
                                            class="hover:underline">{{ Str::limit($first->title, 90) }}</a>
                                    </h3>
                                    @if ($first->excerpt)
                                        <p class="text-sm text-gray-300 line-clamp-2">{{ $first->excerpt }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </section>

                    {{-- Kanan: dua gambar sama tinggi masing-masing setengah parent --}}
                    <section class="col-span-1 rounded-xl bg-white p-4 shadow grid grid-rows-2 gap-4 h-full">
                        @foreach ([$second, $third] as $p)
                            @if ($p)
                                <div class="relative rounded-xl overflow-hidden shadow-md group h-full">
                                    <img src="{{ postUrl($p) }}" alt="{{ $p->title }}"
                                        class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent">
                                    </div>
                                    <div class="absolute bottom-0 p-3 text-white z-10">
                                        <h4 class="text-sm font-semibold leading-tight">
                                            <a href="#"
                                                class="hover:underline">{{ Str::limit($p->title, 70) }}</a>
                                        </h4>
                                        @if ($p->excerpt)
                                            <p class="text-xs text-gray-200 line-clamp-1">{{ $p->excerpt }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </section>
                </div>

                {{-- ======================= SECTION: TENTANG ======================= --}}
                <section id="tentang" class="py-20 bg-white ">
                    <div class="container mx-auto px-4 text-center">
                        <h2 class="text-3xl font-semibold mb-12">Tentang Kami</h2>
                        <p class="text-blueGray-600 max-w-2xl mx-auto">
                            Rausyan Fikr adalah komunitas kajian Islam yang berfokus pada pengembangan diri, kontribusi
                            sosial,
                            dan
                            kolaborasi antarwilayah untuk menghadirkan dampak positif bagi masyarakat.
                        </p>
                    </div>
                </section>



                {{-- ======================= SECTION: KONTAK ======================= --}}
                <section id="kontak" class="relative block py-24 bg-blueGray-800">
                    <div class="container mx-auto px-4 text-center text-white">
                        <h2 class="text-3xl font-semibold mb-6">Hubungi Kami</h2>
                        <p class="text-blueGray-400 mb-8">
                            Ada pertanyaan atau ingin berkontribusi? Kirim pesan Anda.
                        </p>
                        <a href="mailto:info@rausyanfikr.org"
                            class="bg-white text-blueGray-800 px-6 py-3 rounded shadow font-bold uppercase hover:shadow-lg">Kirim
                            Email</a>
                    </div>
                </section>
    </main>

    {{-- ======================= FOOTER ======================= --}}
    <footer class="relative bg-blueGray-200 pt-8 pb-6">
        <div class="container mx-auto px-4 text-center">
            <hr class="my-6 border-blueGray-300" />
            <p class="text-sm text-blueGray-500 font-semibold py-1">
                © {{ date('Y') }} Rausyan Fikr — Semua Hak Dilindungi.
            </p>
        </div>
    </footer>

    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.js"></script>
    <script>
        function toggleNavbar(id) {
            document.getElementById(id).classList.toggle('hidden');
            document.getElementById(id).classList.toggle('block');
        }
    </script>

</body>


</html>
