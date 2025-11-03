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
                ><button
                    class="cursor-pointer text-xl leading-none px-3 py-1 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none focus:outline-none"
                    type="button" onclick="toggleNavbar('example-collapse-navbar')">
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
    <main>
        {{-- ======================= HERO (jumbotron) ======================= --}}
        <div class="relative pt-16 pb-32 flex content-center items-center justify-center min-h-screen-75">
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
            <div class="top-auto bottom-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden h-70-px"
                style="transform: translateZ(0px)">
                <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                    <polygon class="text-blueGray-200 fill-current" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </div>
        {{-- ======================= SECTION: FITUR ======================= --}}
        <section class="pb-20 bg-blueGray-200 -mt-24">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap">
                    <div class="lg:pt-12 pt-6 w-full md:w-4/12 px-4 text-center">
                        <div
                            class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-lg rounded-lg">
                            <div class="px-4 py-5 flex-auto">
                                <div
                                    class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 mb-5 shadow-lg rounded-full bg-red-400">
                                    <i class="fas fa-award"></i>
                                </div>
                                <h6 class="text-xl font-semibold">Kajian Islami</h6>
                                <p class="mt-2 mb-4 text-blueGray-500">
                                    Divide details about your product or agency work into parts.
                                    A paragraph describing a feature will be enough.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="w-full md:w-4/12 px-4 text-center">
                        <div
                            class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-lg rounded-lg">
                            <div class="px-4 py-5 flex-auto">
                                <div
                                    class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 mb-5 shadow-lg rounded-full bg-lightBlue-400">
                                    <i class="fas fa-retweet"></i>
                                </div>
                                <h6 class="text-xl font-semibold">Silaturahmi</h6>
                                <p class="mt-2 mb-4 text-blueGray-500">
                                    Keep you user engaged by providing meaningful information.
                                    Remember that by this time, the user is curious.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="pt-6 w-full md:w-4/12 px-4 text-center">
                        <div
                            class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-lg rounded-lg">
                            <div class="px-4 py-5 flex-auto">
                                <div
                                    class="text-white p-3 text-center inline-flex items-center justify-center w-12 h-12 mb-5 shadow-lg rounded-full bg-emerald-400">
                                    <i class="fas fa-fingerprint"></i>
                                </div>
                                <h6 class="text-xl font-semibold">Kgiatan Sosial</h6>
                                <p class="mt-2 mb-4 text-blueGray-500">
                                    Write a few lines about each one. A paragraph describing a
                                    feature will be enough. Keep you user engaged!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Berita & Informasi</h2>
                    <p class="py-1 font-medium">Kumpulan informasi dan update terbaru dari kami. (Belum fix)</p>

                </div>
                <div class="flex flex-wrap">
                    @foreach ($posts as $p)
                        <div class="w-full md:w-4/12 px-4 mr-auto ml-auto">
                            <div
                                class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-lg rounded-lg bg-pink-500">
                                @php
                                    $imgSrc = Str::startsWith($p->cover_url, ['http', 'https'])
                                        ? $p->cover_url
                                        : ($p->cover_url
                                            ? asset('storage/' . $p->cover_url)
                                            : asset('assets/img/default-img.jpg'));
                                @endphp
                                <img src="{{ $imgSrc }}"
                                    alt="{{ $p->title }}"class="w-full align-middle rounded-t-lg" />
                                <blockquote class="relative p-8 mb-4">

                                    <h4 class="text-xl font-bold text-white">
                                        {{ Str::limit($p->title, 90) }}
                                    </h4>

                                    <p class="text-md font-light mt-2 text-white">Published :
                                        {{ $p->updated_at }}
                                    </p>

                                </blockquote>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="relative py-20">
            <div class="bottom-auto top-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden -mt-20 h-20"
                style="transform: translateZ(0px)">
                <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                    <polygon class="text-white fill-current" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
        </section>
        {{-- ======================= Galeri ======================= --}}
        <section class="pt-20 pb-48">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap justify-center text-center mb-24">
                    <div class="w-full lg:w-6/12 px-4">
                        <h2 class="text-4xl font-semibold">Galeri</h2>
                        <p class="text-lg leading-relaxed m-4 text-blueGray-500">
                            Dokumentasi kegiatan
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap">
                    @foreach ($galleries as $g)
                        <div class="w-full md:w-6/12 lg:w-3/12 lg:mb-0 mb-12 px-4">
                            <div class="px-6">
                                <img src="{{ asset('storage/' . $g->image_url) }}" alt="{{ $g->title }}"
                                    class="shadow-lg object-cover  mx-auto max-h-120-px" />
                                <div class="pt-6 text-center">
                                    <h5 class="text-xl font-bold">{{ $g->title }}</h5>
                                    <p class="mt-1 text-sm text-blueGray-400  font-semibold">
                                        {{ $g->caption }}
                                    </p>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </section>

        {{-- ======================= SECTION: TENTANG ======================= --}}
        <section class="pb-20 relative block bg-blueGray-800">
            <div class="bottom-auto top-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden -mt-20 h-20"
                style="transform: translateZ(0px)">
                <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                    <polygon class="text-blueGray-800 fill-current" points="2560 0 2560 100 0 100"></polygon>
                </svg>
            </div>
            <div class="container mx-auto px-4 lg:pt-24 lg:pb-64">
                <div class="flex flex-wrap text-center justify-center">
                    <div class="w-full lg:w-6/12 px-4">
                        <h2 class="text-4xl font-semibold text-white">Tentang Kami</h2>
                        <p class="text-lg leading-relaxed mt-4 mb-4 text-blueGray-400">
                            Rausyan Fikr adalah komunitas kajian Islam yang berfokus pada pengembangan diri,
                            kontribusi
                            sosial,
                            dan
                            kolaborasi antarwilayah untuk menghadirkan dampak positif bagi masyarakat.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap mt-12 justify-center">
                    <div class="w-full lg:w-3/12 px-4 text-center">
                        <div
                            class="text-blueGray-800 p-3 w-12 h-12 shadow-lg rounded-full bg-white inline-flex items-center justify-center">
                            <i class="fas fa-medal text-xl"></i>
                        </div>
                        <h6 class="text-xl mt-5 font-semibold text-white">
                            Visi
                        </h6>
                        <p class="mt-2 mb-4 text-blueGray-400">
                            Some quick example text to build on the card title and make up
                            the bulk of the card's content.
                        </p>
                    </div>
                    <div class="w-full lg:w-3/12 px-4 text-center">
                        <div
                            class="text-blueGray-800 p-3 w-12 h-12 shadow-lg rounded-full bg-white inline-flex items-center justify-center">
                            <i class="fas fa-poll text-xl"></i>
                        </div>
                        <h5 class="text-xl mt-5 font-semibold text-white">
                            Misi
                        </h5>
                        <p class="mt-2 mb-4 text-blueGray-400">
                            Some quick example text to build on the card title and make up
                            the bulk of the card's content.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- <section class="relative block py-24 lg:pt-0 bg-blueGray-800">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center lg:-mt-64 -mt-48">
                        <div class="w-full lg:w-6/12 px-4">
                            <div
                                class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-200">
                                <div class="flex-auto p-5 lg:p-10">
                                    <h4 class="text-2xl font-semibold">Want to work with us?</h4>
                                    <p class="leading-relaxed mt-1 mb-4 text-blueGray-500">
                                        Complete this form and we will get back to you in 24 hours.
                                    </p>
                                    <div class="relative w-full mb-3 mt-8">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            for="full-name">Full Name</label><input type="text"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            placeholder="Full Name" />
                                    </div>
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            for="email">Email</label><input type="email"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                            placeholder="Email" />
                                    </div>
                                    <div class="relative w-full mb-3">
                                        <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            for="message">Message</label>
                                        <textarea rows="4" cols="80"
                                            class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full"
                                            placeholder="Type a message..."></textarea>
                                    </div>
                                    <div class="text-center mt-6">
                                        <button
                                            class="bg-blueGray-800 text-white active:bg-blueGray-600 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                                            type="button">
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section> --}}
        <section class="relative py-24 lg:pt-0">
            <div class="container mx-auto px-4">
                <div class="content mt-10 md:mt-15 xl:mt-25 mb-10 md:mb-25 max-xxl:p-2" id="portfolio">
                    <div class="xl:mb-17.5 mb-5">
                        <div class="max-sm:px-2 text-center mx-auto max-w-144.25">
                            <p class="section-title ">Portfolio</p>
                            <p class="font-normal text-[18px] max-sm:text-[14px] pt-6 text-gray-400">Here's a
                                selection
                                of my recent work, showcasing my skills in creating user-centric and visually
                                appealing
                                interfaces.</p>
                        </div>
                    </div>
                    <div class="mx-auto flex justify-center">
                        <div class="grid xl:grid-cols-3 md:grid-cols-2 gap-6">
                            <div
                                class="max-w-106 rounded-lg outline-[#FFFFFF] hover:shadow-2xl duration-300 transition-all shadow-gray-300 border border-gray-200">
                                <img alt="Product Admin Dashboard image" src="/picto/assets/card-1-D83uj-qZ.png">
                                <div class="p-4 xs:p-8">
                                    <p class="text-gray-400 text-xs font-medium">UI-UX DESIGN</p>
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product
                                        Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">I focus on crafting smooth, responsive
                                        interfaces
                                        that balance aesthetic appeal with practical functionality.</p><a
                                        href="#!"
                                        class="btn hover:border-picto-primary hover:text-picto-primary bg-white text-sm xs:text-[16px] font-semibold hover:gap-3 xs:hover:gap-4 transition-all duration-300 mt-5 xs:py-5.75 px-6 max-sm:w-full">Case
                                        Study<span class="ms-1 xs:ms-3"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="arrow-right"
                                                class="svg-inline--fa fa-arrow-right fa-l " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                                </path>
                                            </svg></span></a>
                                </div>
                            </div>
                            <div
                                class="max-w-106 rounded-lg outline-[#FFFFFF] hover:shadow-2xl duration-300 transition-all shadow-gray-300 border border-gray-200">
                                <img alt="Product Admin Dashboard image" src="/picto/assets/card-2-BJ8-9N8h.png">
                                <div class="p-4 xs:p-8">
                                    <p class="text-gray-400 text-xs font-medium">UI-UX DESIGN</p>
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product
                                        Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Designed an intuitive dashboard for product
                                        management, emphasizing clarity and user efficiency.</p><a href="#!"
                                        class="btn hover:border-picto-primary hover:text-picto-primary bg-white text-sm xs:text-[16px] font-semibold hover:gap-3 xs:hover:gap-4 transition-all duration-300 mt-5 xs:py-5.75 px-6 max-sm:w-full">Case
                                        Study<span class="ms-1 xs:ms-3"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="arrow-right"
                                                class="svg-inline--fa fa-arrow-right fa-l " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                                </path>
                                            </svg></span></a>
                                </div>
                            </div>
                            <div
                                class="max-w-106 rounded-lg outline-[#FFFFFF] hover:shadow-2xl duration-300 transition-all shadow-gray-300 border border-gray-200">
                                <img alt="Product Admin Dashboard image" src="/picto/assets/card-3-Ka1ll87R.png">
                                <div class="p-4 xs:p-8">
                                    <p class="text-gray-400 text-xs font-medium">UI-UX DESIGN</p>
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product
                                        Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Developed a modern admin panel with a focus on
                                        usability and seamless navigation for end users and so on.</p><a href="#!"
                                        class="btn hover:border-picto-primary hover:text-picto-primary bg-white text-sm xs:text-[16px] font-semibold hover:gap-3 xs:hover:gap-4 transition-all duration-300 mt-5 xs:py-5.75 px-6 max-sm:w-full">Case
                                        Study<span class="ms-1 xs:ms-3"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="arrow-right"
                                                class="svg-inline--fa fa-arrow-right fa-l " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                                </path>
                                            </svg></span></a>
                                </div>
                            </div>
                            <div
                                class="max-w-106 rounded-lg outline-[#FFFFFF] hover:shadow-2xl duration-300 transition-all shadow-gray-300 border border-gray-200">
                                <img alt="Product Admin Dashboard image" src="/picto/assets/card-4-1lvzje-u.png">
                                <div class="p-4 xs:p-8">
                                    <p class="text-gray-400 text-xs font-medium">UI-UX DESIGN</p>
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product
                                        Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Created a responsive dashboard layout that
                                        adapts
                                        smoothly across devices and screen sizes and so on.</p><a href="#!"
                                        class="btn hover:border-picto-primary hover:text-picto-primary bg-white text-sm xs:text-[16px] font-semibold hover:gap-3 xs:hover:gap-4 transition-all duration-300 mt-5 xs:py-5.75 px-6 max-sm:w-full">Case
                                        Study<span class="ms-1 xs:ms-3"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="arrow-right"
                                                class="svg-inline--fa fa-arrow-right fa-l " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                                </path>
                                            </svg></span></a>
                                </div>
                            </div>
                            <div
                                class="max-w-106 rounded-lg outline-[#FFFFFF] hover:shadow-2xl duration-300 transition-all shadow-gray-300 border border-gray-200">
                                <img alt="Product Admin Dashboard image" src="/picto/assets/card-5-TrbU7d8r.png">
                                <div class="p-4 xs:p-8">
                                    <p class="text-gray-400 text-xs font-medium">UI-UX DESIGN</p>
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product
                                        Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Implemented interactive charts and widgets to
                                        visualize product data effectively for stakeholders.</p><a href="#!"
                                        class="btn hover:border-picto-primary hover:text-picto-primary bg-white text-sm xs:text-[16px] font-semibold hover:gap-3 xs:hover:gap-4 transition-all duration-300 mt-5 xs:py-5.75 px-6 max-sm:w-full">Case
                                        Study<span class="ms-1 xs:ms-3"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="arrow-right"
                                                class="svg-inline--fa fa-arrow-right fa-l " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                                </path>
                                            </svg></span></a>
                                </div>
                            </div>
                            <div
                                class="max-w-106 rounded-lg outline-[#FFFFFF] hover:shadow-2xl duration-300 transition-all shadow-gray-300 border border-gray-200">
                                <img alt="Product Admin Dashboard image" src="/picto/assets/card-6-CMi2awuo.png">
                                <div class="p-4 xs:p-8">
                                    <p class="text-gray-400 text-xs font-medium">UI-UX DESIGN</p>
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product
                                        Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Enhanced user experience by streamlining
                                        workflows
                                        and optimizing interface components and so on.</p><a href="#!"
                                        class="btn hover:border-picto-primary hover:text-picto-primary bg-white text-sm xs:text-[16px] font-semibold hover:gap-3 xs:hover:gap-4 transition-all duration-300 mt-5 xs:py-5.75 px-6 max-sm:w-full">Case
                                        Study<span class="ms-1 xs:ms-3"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="arrow-right"
                                                class="svg-inline--fa fa-arrow-right fa-l " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                                </path>
                                            </svg></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center"><a href="#!"
                            class="btn btn-primary py-3 px-6 mt-12.5 text-center text-[16px] font-semibold">More
                            Project</a></div>
                </div>
            </div>
        </section>
    </main>
    {{-- ======================= Footer ======================= --}}
    <footer id="kontak" class="relative bg-blueGray-200 pt-8 pb-6">
        <div class="bottom-auto top-0 left-0 right-0 w-full absolute pointer-events-none overflow-hidden -mt-20 h-20"
            style="transform: translateZ(0px)">
            <svg class="absolute bottom-0 overflow-hidden" xmlns="http://www.w3.org/2000/svg"
                preserveAspectRatio="none" version="1.1" viewBox="0 0 2560 100" x="0" y="0">
                <polygon class="text-blueGray-200 fill-current" points="2560 0 2560 100 0 100"></polygon>
            </svg>
        </div>
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap text-center lg:text-left">
                <div class="w-full lg:w-6/12 px-4">
                    <h4 class="text-3xl font-semibold">Let's keep in touch!</h4>
                    <h5 class="text-lg mt-0 mb-2 text-blueGray-600">
                        Find us on any of these platforms, we respond 1-2 business days.
                    </h5>
                    <div class="mt-6 lg:mb-0 mb-6">
                        <button
                            class="bg-white text-lightBlue-400 shadow-lg font-normal h-10 w-10 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2"
                            type="button">
                            <i class="fab fa-twitter"></i></button><button
                            class="bg-white text-lightBlue-600 shadow-lg font-normal h-10 w-10 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2"
                            type="button">
                            <i class="fab fa-facebook-square"></i></button><button
                            class="bg-white text-pink-400 shadow-lg font-normal h-10 w-10 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2"
                            type="button">
                            <i class="fab fa-dribbble"></i></button><button
                            class="bg-white text-blueGray-800 shadow-lg font-normal h-10 w-10 items-center justify-center align-center rounded-full outline-none focus:outline-none mr-2"
                            type="button">
                            <i class="fab fa-github"></i>
                        </button>
                    </div>
                </div>
                <div class="w-full lg:w-6/12 px-4">
                    <div class="flex flex-wrap items-top mb-6">
                        <div class="w-full lg:w-4/12 px-4 ml-auto">
                            <span class="block uppercase text-blueGray-500 text-sm font-semibold mb-2">Useful
                                Links</span>
                            <ul class="list-unstyled">
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://www.creative-tim.com/presentation?ref=njs-landing">About Us</a>
                                </li>
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://blog.creative-tim.com?ref=njs-landing">Blog</a>
                                </li>
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://www.github.com/creativetimofficial?ref=njs-landing">Github</a>
                                </li>
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://www.creative-tim.com/bootstrap-themes/free?ref=njs-landing">Free
                                        Products</a>
                                </li>
                            </ul>
                        </div>
                        <div class="w-full lg:w-4/12 px-4">
                            <span class="block uppercase text-blueGray-500 text-sm font-semibold mb-2">Other
                                Resources</span>
                            <ul class="list-unstyled">
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://github.com/creativetimofficial/notus-js/blob/main/LICENSE.md?ref=njs-landing">MIT
                                        License</a>
                                </li>
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://creative-tim.com/terms?ref=njs-landing">Terms &amp;
                                        Conditions</a>
                                </li>
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://creative-tim.com/privacy?ref=njs-landing">Privacy Policy</a>
                                </li>
                                <li>
                                    <a class="text-blueGray-600 hover:text-blueGray-800 font-semibold block pb-2 text-sm"
                                        href="https://creative-tim.com/contact-us?ref=njs-landing">Contact Us</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-blueGray-300" />
            <div class="flex flex-wrap items-center md:justify-between justify-center">
                <div class="w-full md:w-4/12 px-4 mx-auto text-center">
                    <div class="text-sm text-blueGray-500 font-semibold py-1">
                        © {{ date('Y') }} Rausyan Fikr — Semua Hak Dilindungi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</body>


</html>
