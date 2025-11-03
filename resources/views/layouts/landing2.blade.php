<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="hUTF-8">
    <style type="text/css">
        :root,
        :host {
            --fa-font-solid: normal 900 1em/1 "Font Awesome 6 Free";
            --fa-font-regular: normal 400 1em/1 "Font Awesome 6 Free";
            --fa-font-light: normal 300 1em/1 "Font Awesome 6 Pro";
            --fa-font-thin: normal 100 1em/1 "Font Awesome 6 Pro";
            --fa-font-duotone: normal 900 1em/1 "Font Awesome 6 Duotone";
            --fa-font-duotone-regular: normal 400 1em/1 "Font Awesome 6 Duotone";
            --fa-font-duotone-light: normal 300 1em/1 "Font Awesome 6 Duotone";
            --fa-font-duotone-thin: normal 100 1em/1 "Font Awesome 6 Duotone";
            --fa-font-brands: normal 400 1em/1 "Font Awesome 6 Brands";
            --fa-font-sharp-solid: normal 900 1em/1 "Font Awesome 6 Sharp";
            --fa-font-sharp-regular: normal 400 1em/1 "Font Awesome 6 Sharp";
            --fa-font-sharp-light: normal 300 1em/1 "Font Awesome 6 Sharp";
            --fa-font-sharp-thin: normal 100 1em/1 "Font Awesome 6 Sharp";
            --fa-font-sharp-duotone-solid: normal 900 1em/1 "Font Awesome 6 Sharp Duotone";
            --fa-font-sharp-duotone-regular: normal 400 1em/1 "Font Awesome 6 Sharp Duotone";
            --fa-font-sharp-duotone-light: normal 300 1em/1 "Font Awesome 6 Sharp Duotone";
            --fa-font-sharp-duotone-thin: normal 100 1em/1 "Font Awesome 6 Sharp Duotone";
        }

        svg:not(:root).svg-inline--fa,
        svg:not(:host).svg-inline--fa {
            overflow: visible;
            box-sizing: content-box;
        }

        .svg-inline--fa {
            display: var(--fa-display, inline-block);
            height: 1em;
            overflow: visible;
            vertical-align: -0.125em;
        }

        .svg-inline--fa.fa-2xs {
            vertical-align: 0.1em;
        }

        .svg-inline--fa.fa-xs {
            vertical-align: 0em;
        }

        .svg-inline--fa.fa-sm {
            vertical-align: -0.0714285705em;
        }

        .svg-inline--fa.fa-lg {
            vertical-align: -0.2em;
        }

        .svg-inline--fa.fa-xl {
            vertical-align: -0.25em;
        }

        .svg-inline--fa.fa-2xl {
            vertical-align: -0.3125em;
        }

        .svg-inline--fa.fa-pull-left {
            margin-right: var(--fa-pull-margin, 0.3em);
            width: auto;
        }

        .svg-inline--fa.fa-pull-right {
            margin-left: var(--fa-pull-margin, 0.3em);
            width: auto;
        }

        .svg-inline--fa.fa-li {
            width: var(--fa-li-width, 2em);
            top: 0.25em;
        }

        .svg-inline--fa.fa-fw {
            width: var(--fa-fw-width, 1.25em);
        }

        .fa-layers svg.svg-inline--fa {
            bottom: 0;
            left: 0;
            margin: auto;
            position: absolute;
            right: 0;
            top: 0;
        }

        .fa-layers-counter,
        .fa-layers-text {
            display: inline-block;
            position: absolute;
            text-align: center;
        }

        .fa-layers {
            display: inline-block;
            height: 1em;
            position: relative;
            text-align: center;
            vertical-align: -0.125em;
            width: 1em;
        }

        .fa-layers svg.svg-inline--fa {
            transform-origin: center center;
        }

        .fa-layers-text {
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            transform-origin: center center;
        }

        .fa-layers-counter {
            background-color: var(--fa-counter-background-color, #ff253a);
            border-radius: var(--fa-counter-border-radius, 1em);
            box-sizing: border-box;
            color: var(--fa-inverse, #fff);
            line-height: var(--fa-counter-line-height, 1);
            max-width: var(--fa-counter-max-width, 5em);
            min-width: var(--fa-counter-min-width, 1.5em);
            overflow: hidden;
            padding: var(--fa-counter-padding, 0.25em 0.5em);
            right: var(--fa-right, 0);
            text-overflow: ellipsis;
            top: var(--fa-top, 0);
            transform: scale(var(--fa-counter-scale, 0.25));
            transform-origin: top right;
        }

        .fa-layers-bottom-right {
            bottom: var(--fa-bottom, 0);
            right: var(--fa-right, 0);
            top: auto;
            transform: scale(var(--fa-layers-scale, 0.25));
            transform-origin: bottom right;
        }

        .fa-layers-bottom-left {
            bottom: var(--fa-bottom, 0);
            left: var(--fa-left, 0);
            right: auto;
            top: auto;
            transform: scale(var(--fa-layers-scale, 0.25));
            transform-origin: bottom left;
        }

        .fa-layers-top-right {
            top: var(--fa-top, 0);
            right: var(--fa-right, 0);
            transform: scale(var(--fa-layers-scale, 0.25));
            transform-origin: top right;
        }

        .fa-layers-top-left {
            left: var(--fa-left, 0);
            right: auto;
            top: var(--fa-top, 0);
            transform: scale(var(--fa-layers-scale, 0.25));
            transform-origin: top left;
        }

        .fa-1x {
            font-size: 1em;
        }

        .fa-2x {
            font-size: 2em;
        }

        .fa-3x {
            font-size: 3em;
        }

        .fa-4x {
            font-size: 4em;
        }

        .fa-5x {
            font-size: 5em;
        }

        .fa-6x {
            font-size: 6em;
        }

        .fa-7x {
            font-size: 7em;
        }

        .fa-8x {
            font-size: 8em;
        }

        .fa-9x {
            font-size: 9em;
        }

        .fa-10x {
            font-size: 10em;
        }

        .fa-2xs {
            font-size: 0.625em;
            line-height: 0.1em;
            vertical-align: 0.225em;
        }

        .fa-xs {
            font-size: 0.75em;
            line-height: 0.0833333337em;
            vertical-align: 0.125em;
        }

        .fa-sm {
            font-size: 0.875em;
            line-height: 0.0714285718em;
            vertical-align: 0.0535714295em;
        }

        .fa-lg {
            font-size: 1.25em;
            line-height: 0.05em;
            vertical-align: -0.075em;
        }

        .fa-xl {
            font-size: 1.5em;
            line-height: 0.0416666682em;
            vertical-align: -0.125em;
        }

        .fa-2xl {
            font-size: 2em;
            line-height: 0.03125em;
            vertical-align: -0.1875em;
        }

        .fa-fw {
            text-align: center;
            width: 1.25em;
        }

        .fa-ul {
            list-style-type: none;
            margin-left: var(--fa-li-margin, 2.5em);
            padding-left: 0;
        }

        .fa-ul>li {
            position: relative;
        }

        .fa-li {
            left: calc(-1 * var(--fa-li-width, 2em));
            position: absolute;
            text-align: center;
            width: var(--fa-li-width, 2em);
            line-height: inherit;
        }

        .fa-border {
            border-color: var(--fa-border-color, #eee);
            border-radius: var(--fa-border-radius, 0.1em);
            border-style: var(--fa-border-style, solid);
            border-width: var(--fa-border-width, 0.08em);
            padding: var(--fa-border-padding, 0.2em 0.25em 0.15em);
        }

        .fa-pull-left {
            float: left;
            margin-right: var(--fa-pull-margin, 0.3em);
        }

        .fa-pull-right {
            float: right;
            margin-left: var(--fa-pull-margin, 0.3em);
        }

        .fa-beat {
            animation-name: fa-beat;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, ease-in-out);
        }

        .fa-bounce {
            animation-name: fa-bounce;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, cubic-bezier(0.28, 0.84, 0.42, 1));
        }

        .fa-fade {
            animation-name: fa-fade;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, cubic-bezier(0.4, 0, 0.6, 1));
        }

        .fa-beat-fade {
            animation-name: fa-beat-fade;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, cubic-bezier(0.4, 0, 0.6, 1));
        }

        .fa-flip {
            animation-name: fa-flip;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, ease-in-out);
        }

        .fa-shake {
            animation-name: fa-shake;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, linear);
        }

        .fa-spin {
            animation-name: fa-spin;
            animation-delay: var(--fa-animation-delay, 0s);
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 2s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, linear);
        }

        .fa-spin-reverse {
            --fa-animation-direction: reverse;
        }

        .fa-pulse,
        .fa-spin-pulse {
            animation-name: fa-spin;
            animation-direction: var(--fa-animation-direction, normal);
            animation-duration: var(--fa-animation-duration, 1s);
            animation-iteration-count: var(--fa-animation-iteration-count, infinite);
            animation-timing-function: var(--fa-animation-timing, steps(8));
        }

        @media (prefers-reduced-motion: reduce) {

            .fa-beat,
            .fa-bounce,
            .fa-fade,
            .fa-beat-fade,
            .fa-flip,
            .fa-pulse,
            .fa-shake,
            .fa-spin,
            .fa-spin-pulse {
                animation-delay: -1ms;
                animation-duration: 1ms;
                animation-iteration-count: 1;
                transition-delay: 0s;
                transition-duration: 0s;
            }
        }

        @keyframes fa-beat {

            0%,
            90% {
                transform: scale(1);
            }

            45% {
                transform: scale(var(--fa-beat-scale, 1.25));
            }
        }

        @keyframes fa-bounce {
            0% {
                transform: scale(1, 1) translateY(0);
            }

            10% {
                transform: scale(var(--fa-bounce-start-scale-x, 1.1), var(--fa-bounce-start-scale-y, 0.9)) translateY(0);
            }

            30% {
                transform: scale(var(--fa-bounce-jump-scale-x, 0.9), var(--fa-bounce-jump-scale-y, 1.1)) translateY(var(--fa-bounce-height, -0.5em));
            }

            50% {
                transform: scale(var(--fa-bounce-land-scale-x, 1.05), var(--fa-bounce-land-scale-y, 0.95)) translateY(0);
            }

            57% {
                transform: scale(1, 1) translateY(var(--fa-bounce-rebound, -0.125em));
            }

            64% {
                transform: scale(1, 1) translateY(0);
            }

            100% {
                transform: scale(1, 1) translateY(0);
            }
        }

        @keyframes fa-fade {
            50% {
                opacity: var(--fa-fade-opacity, 0.4);
            }
        }

        @keyframes fa-beat-fade {

            0%,
            100% {
                opacity: var(--fa-beat-fade-opacity, 0.4);
                transform: scale(1);
            }

            50% {
                opacity: 1;
                transform: scale(var(--fa-beat-fade-scale, 1.125));
            }
        }

        @keyframes fa-flip {
            50% {
                transform: rotate3d(var(--fa-flip-x, 0), var(--fa-flip-y, 1), var(--fa-flip-z, 0), var(--fa-flip-angle, -180deg));
            }
        }

        @keyframes fa-shake {
            0% {
                transform: rotate(-15deg);
            }

            4% {
                transform: rotate(15deg);
            }

            8%,
            24% {
                transform: rotate(-18deg);
            }

            12%,
            28% {
                transform: rotate(18deg);
            }

            16% {
                transform: rotate(-22deg);
            }

            20% {
                transform: rotate(22deg);
            }

            32% {
                transform: rotate(-12deg);
            }

            36% {
                transform: rotate(12deg);
            }

            40%,
            100% {
                transform: rotate(0deg);
            }
        }

        @keyframes fa-spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fa-rotate-90 {
            transform: rotate(90deg);
        }

        .fa-rotate-180 {
            transform: rotate(180deg);
        }

        .fa-rotate-270 {
            transform: rotate(270deg);
        }

        .fa-flip-horizontal {
            transform: scale(-1, 1);
        }

        .fa-flip-vertical {
            transform: scale(1, -1);
        }

        .fa-flip-both,
        .fa-flip-horizontal.fa-flip-vertical {
            transform: scale(-1, -1);
        }

        .fa-rotate-by {
            transform: rotate(var(--fa-rotate-angle, 0));
        }

        .fa-stack {
            display: inline-block;
            vertical-align: middle;
            height: 2em;
            position: relative;
            width: 2.5em;
        }

        .fa-stack-1x,
        .fa-stack-2x {
            bottom: 0;
            left: 0;
            margin: auto;
            position: absolute;
            right: 0;
            top: 0;
            z-index: var(--fa-stack-z-index, auto);
        }

        .svg-inline--fa.fa-stack-1x {
            height: 1em;
            width: 1.25em;
        }

        .svg-inline--fa.fa-stack-2x {
            height: 2em;
            width: 2.5em;
        }

        .fa-inverse {
            color: var(--fa-inverse, #fff);
        }

        .sr-only,
        .fa-sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .sr-only-focusable:not(:focus),
        .fa-sr-only-focusable:not(:focus) {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        .svg-inline--fa .fa-primary {
            fill: var(--fa-primary-color, currentColor);
            opacity: var(--fa-primary-opacity, 1);
        }

        .svg-inline--fa .fa-secondary {
            fill: var(--fa-secondary-color, currentColor);
            opacity: var(--fa-secondary-opacity, 0.4);
        }

        .svg-inline--fa.fa-swap-opacity .fa-primary {
            opacity: var(--fa-secondary-opacity, 0.4);
        }

        .svg-inline--fa.fa-swap-opacity .fa-secondary {
            opacity: var(--fa-primary-opacity, 1);
        }

        .svg-inline--fa mask .fa-primary,
        .svg-inline--fa mask .fa-secondary {
            fill: black;
        }
    </style>
    <link rel="icon" type="image/svg+xml" class="rounded-b-4xl w-8 text-white" href="/picto/logo.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap');
        /* Google font - Work Sans */
    </style>

    <title>Picto - Personal portfolio</title>
    <script type="module" crossorigin="" src="/src/assets/index-DEoNznxD.js"></script>
    <link rel="stylesheet" crossorigin="" href="/src/assets/index-DhZmhbdT.css">
    <link rel="modulepreload" as="script" crossorigin="" href="/src/assets/Main-DuFPCR7o.js">
    <link rel="modulepreload" as="script" crossorigin="" href="/src/assets/index-ZpvRGqu6.js">
    <style></style>
    <style></style>
    <link rel="modulepreload" as="script" crossorigin="" href="/src/assets/Home-CvFw0TlZ.js">
    <link rel="stylesheet" crossorigin="" href="/src/assets/Home-lUc8SIoi.css">
    <style type="text/css">
        .rfm-marquee-container {
            overflow-x: hidden;
            display: flex;
            flex-direction: row;
            position: relative;
            width: var(--width);
            transform: var(--transform);
        }

        .rfm-marquee-container:hover div {
            animation-play-state: var(--pause-on-hover);
        }

        .rfm-marquee-container:active div {
            animation-play-state: var(--pause-on-click);
        }

        .rfm-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .rfm-overlay::before,
        .rfm-overlay::after {
            background: linear-gradient(to right, var(--gradient-color), rgba(255, 255, 255, 0));
            content: "";
            height: 100%;
            position: absolute;
            width: var(--gradient-width);
            z-index: 2;
            pointer-events: none;
            touch-action: none;
        }

        .rfm-overlay::after {
            right: 0;
            top: 0;
            transform: rotateZ(180deg);
        }

        .rfm-overlay::before {
            left: 0;
            top: 0;
        }

        .rfm-marquee {
            flex: 0 0 auto;
            min-width: var(--min-width);
            z-index: 1;
            display: flex;
            flex-direction: row;
            align-items: center;
            animation: scroll var(--duration) linear var(--delay) var(--iteration-count);
            animation-play-state: var(--play);
            animation-delay: var(--delay);
            animation-direction: var(--direction);
        }

        @keyframes scroll {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .rfm-initial-child-container {
            flex: 0 0 auto;
            display: flex;
            min-width: auto;
            flex-direction: row;
            align-items: center;
        }

        .rfm-child {
            transform: var(--transform);
        }
    </style>
</head>

<body class="">
    <div id="root" class="">
        <div data-theme="light" class="relative">
            <div class="sticky top-0 bg-soft-white border-b border-gray-300 z-50 transition-all duration-1000">
                <div class="navbar flex justify-between mx-auto content">
                    <div class="flex items-center justify-between">
                        <div class="dropdown">
                            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden"><svg
                                    xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h8m-8 6h16"></path>
                                </svg></div>
                            <ul tabindex="0"
                                class="menu menu-lg dropdown-content rounded-box z-1 mt-3 w-lvw p-2 shadow font-semibold flex-nowrap bg-white text-black">
                                <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Home</a></li>
                                <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">About</a></li>
                                <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Process</a></li>
                                <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Portfolio</a></li>
                                <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Blog</a></li>
                                <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Services</a></li>
                            </ul>
                        </div><a href="#introduction" class="flex items-center border-0 lg:max-xxl:ps-5"><img
                                class="h-8 sm:h-14 rounded-2xl" alt="logo" src="/picto/assets/logo-BZuaoNYZ.png">
                            <p class="text-2xl sm:text-[32px] my-auto ms-[12px] font-semibold">Brooklyn</p>
                        </a>
                    </div>
                    <div class="lg:flex items-center">
                        <ul class="hidden lg:flex menu menu-horizontal text-[16px] font-medium md:shrink-0">
                            <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Home</a></li>
                            <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">About</a></li>
                            <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Process</a></li>
                            <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Portfolio</a></li>
                            <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Blog</a></li>
                            <li><a class="hover:text-picto-primary px-5 py-3 mx-1" style="">Services</a></li>
                        </ul>
                        <p class=""><a class="btn btn-sm xs:btn-md sm:btn-lg btn-primary"
                                href="#contact">Contact</a></p>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="introduction-profile-background">
                    <div class="content">
                        <div class="flex max-lg:flex-col-reverse sm:justify-between pt-10 lg:pt-31.5 lg:mb-27.5 max-xl:gap-2 p-2 max-xxl:px-4"
                            id="introduction">
                            <div class="w-full flex flex-col justify-between max-lg:text-center">
                                <div class="pt-13 me-31.5 w-full lg:w-auto transition-all duration-500">
                                    <p
                                        class="text-3xl xxs:text-4xl sm:max-xl:text-5xl xl:text-6xl font-semibold w-full">
                                        Hello, I’m<span class="text-nowrap shrink-0 inline-block w-full">Brooklyn
                                            Gilbert</span></p>
                                    <p class="text-xs xxs:text-lg lg:text-[18px] my-6">I'm a Freelance <span
                                            class="bg-highlight">UI/UX Designer</span> and <span class="bg-highlight">
                                            Developer</span> based in London, England. I strives to build immersive and
                                        beautiful web applications through carefully crafted code and user-centric
                                        design.</p>
                                    <p class="text-center lg:text-start"><a
                                            class="btn-primary btn btn-xs xxs:btn-lg text-white"
                                            href="mailto:example@gmail.com">Say Hello!</a></p>
                                </div>
                                <div class="mx-auto lg:mx-0 relative">
                                    <div class="grid max-xxs:grid-flow-col grid-cols-3 w-fit mt-10 gap-1">
                                        <div class="bg-[#F6EBFE] text-center">
                                            <div class="w-auto h-auto mx-2 sm:mx-4 my-5 xxs:my-5 sm:my-[17px]">
                                                <p
                                                    class="text-[16px] xxs:text-[18px] sm:text-[32px] font-semibold text-gray-700">
                                                    15 Y.</p>
                                                <p
                                                    class="text-[8px] xxs:text-[9px] sm:text-[16px] font-normal px-[0.90rem] sm:px-[1rem] text-wrap text-gray-500">
                                                    Experience</p>
                                            </div>
                                        </div>
                                        <div class="bg-[#F6EBFE] text-center">
                                            <div class="w-auto h-auto mx-2 sm:mx-4 my-5 xxs:my-5 sm:my-[17px]">
                                                <p
                                                    class="text-[16px] xxs:text-[18px] sm:text-[32px] font-semibold text-gray-700">
                                                    250+</p>
                                                <p
                                                    class="text-[8px] xxs:text-[9px] sm:text-[16px] font-normal px-[0.90rem] sm:px-[1rem] text-wrap text-gray-500">
                                                    Projects Completed</p>
                                            </div>
                                        </div>
                                        <div class="bg-[#F6EBFE] text-center">
                                            <div class="w-auto h-auto mx-2 sm:mx-4 my-5 xxs:my-5 sm:my-[17px]">
                                                <p
                                                    class="text-[16px] xxs:text-[18px] sm:text-[32px] font-semibold text-gray-700">
                                                    58</p>
                                                <p
                                                    class="text-[8px] xxs:text-[9px] sm:text-[16px] font-normal px-[0.90rem] sm:px-[1rem] text-wrap text-gray-500">
                                                    Happy Clients</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="max-w-134 w-full h-full max-lg:mx-auto aspect-[536/636] relative"><img
                                    class="shadow-2xl shadow-gray-200 w-full h-full absolute bottom-0 object-cover bg-white rounded-3xl"
                                    alt="person" src="/picto/assets/person-CqOZwXV1.png"></div>
                        </div>
                        <div class="relative mx-4 xxl:mx-0.5 -bottom-20 lg:-bottom-28 z-10 rounded-2xl bg-white drop-shadow-2xl max-xl:mb-5 shadow-white xl:p-28 lg:p-20 md:p-16 sm:p-10 p-4"
                            id="profile">
                            <div class="flex max-md:flex-col justify-between items-center gap-6">
                                <div class="xxl:max-w-106 w-auto h-auto xxl:max-h-126">
                                    <div class="max-w-106 h-117 object-fill overflow-hidden rounded-xl"><img
                                            class="bg-soft-white h-[120%] object-cover" alt=""
                                            src="/picto/assets/person2-DvYIsw9m.png"></div>
                                    <div class="relative bottom-9">
                                        <div class="flex justify-center">
                                            <div
                                                class="px-6 max-w-66 py-3 z-50 text-center bg-white rounded-[4px] center shadow-2xl drop-shadow-2xl shadow-white">
                                                <a href="#!"
                                                    class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                                        aria-hidden="true" focusable="false" data-prefix="fab"
                                                        data-icon="facebook-f"
                                                        class="svg-inline--fa fa-facebook-f text-xl w-4.5 aspect-square"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 320 512">
                                                        <path fill="currentColor"
                                                            d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z">
                                                        </path>
                                                    </svg></a><a href="#!"
                                                    class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                                        aria-hidden="true" focusable="false" data-prefix="fab"
                                                        data-icon="dribbble"
                                                        class="svg-inline--fa fa-dribbble text-xl w-4.5 aspect-square"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 512 512">
                                                        <path fill="currentColor"
                                                            d="M256 8C119.252 8 8 119.252 8 256s111.252 248 248 248 248-111.252 248-248S392.748 8 256 8zm163.97 114.366c29.503 36.046 47.369 81.957 47.835 131.955-6.984-1.477-77.018-15.682-147.502-6.818-5.752-14.041-11.181-26.393-18.617-41.614 78.321-31.977 113.818-77.482 118.284-83.523zM396.421 97.87c-3.81 5.427-35.697 48.286-111.021 76.519-34.712-63.776-73.185-116.168-79.04-124.008 67.176-16.193 137.966 1.27 190.061 47.489zm-230.48-33.25c5.585 7.659 43.438 60.116 78.537 122.509-99.087 26.313-186.36 25.934-195.834 25.809C62.38 147.205 106.678 92.573 165.941 64.62zM44.17 256.323c0-2.166.043-4.322.108-6.473 9.268.19 111.92 1.513 217.706-30.146 6.064 11.868 11.857 23.915 17.174 35.949-76.599 21.575-146.194 83.527-180.531 142.306C64.794 360.405 44.17 310.73 44.17 256.323zm81.807 167.113c22.127-45.233 82.178-103.622 167.579-132.756 29.74 77.283 42.039 142.053 45.189 160.638-68.112 29.013-150.015 21.053-212.768-27.882zm248.38 8.489c-2.171-12.886-13.446-74.897-41.152-151.033 66.38-10.626 124.7 6.768 131.947 9.055-9.442 58.941-43.273 109.844-90.795 141.978z">
                                                        </path>
                                                    </svg></a><a href="#!"
                                                    class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                                        aria-hidden="true" focusable="false" data-prefix="fab"
                                                        data-icon="instagram"
                                                        class="svg-inline--fa fa-instagram text-xl w-4.5 aspect-square"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 448 512">
                                                        <path fill="currentColor"
                                                            d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z">
                                                        </path>
                                                    </svg></a><a href="#!"
                                                    class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                                        aria-hidden="true" focusable="false" data-prefix="fab"
                                                        data-icon="linkedin"
                                                        class="svg-inline--fa fa-linkedin text-xl w-4.5 aspect-square"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 448 512">
                                                        <path fill="currentColor"
                                                            d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z">
                                                        </path>
                                                    </svg></a><a href="#!"
                                                    class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                                        aria-hidden="true" focusable="false" data-prefix="fab"
                                                        data-icon="behance"
                                                        class="svg-inline--fa fa-behance text-xl w-4.5 aspect-square"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 576 512">
                                                        <path fill="currentColor"
                                                            d="M232 237.2c31.8-15.2 48.4-38.2 48.4-74 0-70.6-52.6-87.8-113.3-87.8H0v354.4h171.8c64.4 0 124.9-30.9 124.9-102.9 0-44.5-21.1-77.4-64.7-89.7zM77.9 135.9H151c28.1 0 53.4 7.9 53.4 40.5 0 30.1-19.7 42.2-47.5 42.2h-79v-82.7zm83.3 233.7H77.9V272h84.9c34.3 0 56 14.3 56 50.6 0 35.8-25.9 47-57.6 47zm358.5-240.7H376V94h143.7v34.9zM576 305.2c0-75.9-44.4-139.2-124.9-139.2-78.2 0-131.3 58.8-131.3 135.8 0 79.9 50.3 134.7 131.3 134.7 61.3 0 101-27.6 120.1-86.3H509c-6.7 21.9-34.3 33.5-55.7 33.5-41.3 0-63-24.2-63-65.3h185.1c.3-4.2.6-8.7.6-13.2zM390.4 274c2.3-33.7 24.7-54.8 58.5-54.8 35.4 0 53.2 20.8 56.2 54.8H390.4z">
                                                        </path>
                                                    </svg></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="max-sm:w-full w-[33rem]">
                                    <h2
                                        class="text-2xl xxs:text-3xl sm:text-4xl lg:text-[38px] text-[min(24px,38px)] max-md:text-center font-semibold mb-8">
                                        I am Professional User Experience Designer</h2>
                                    <div
                                        class="text-xs xs:text-[16px] lg:text-lg font-normal max-md:text-center text-gray-600">
                                        <p class="">I design and develop services for customers specializing
                                            creating stylish, modern websites, web services and online stores. My
                                            passion is to design digital user experiences.</p>
                                        <p class="mt-3">I design and develop services for customers specializing
                                            creating stylish, modern websites, web services.</p>
                                    </div>
                                    <div class="mt-8 flex max-md:justify-center"><a
                                            class="btn xxs:btn-lg px-6 max-xs:px-2 xxs:py-3 btn-primary text-xs xxs:text-[14px] sm:text-[16px]"
                                            href="#!">My Projects</a><a
                                            class="btn xxs:btn-lg px-6 max-xs:px-2 xxs:py-3 hover:border-picto-primary bg-white duration-300 transition-all hover:text-picto-primary  ms-4 text-xs xxs:text-[14px] sm:text-[16px]"
                                            href="#!"><svg aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="download"
                                                class="svg-inline--fa fa-download " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path fill="currentColor"
                                                    d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z">
                                                </path>
                                            </svg> Download CV</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-soft-white pt-30">
                    <div class="content grid xl:grid-cols-2 xl:items-center px-2 py-5 md:py-10 lg:py-25 xl:py-35 max-xxl:px-4"
                        id="work-process">
                        <div class="lg:pe-10 xl:pe-35.75 max-xs:mb-3 max-xl:mb-8">
                            <p class="section-title max-xl:text-center">Work Process</p>
                            <p class="mt-6 mb-4 md:text-[18px] text-sm font-normal max-xl:text-center text-gray-500">
                                Driven by design and powered by code, I create digital interfaces that feel intuitive
                                and perform seamlessly. Every layout, animation, and component is crafted with intention
                                — merging usability with visual clarity,</p>
                            <p class="mt-6 md:text-[18px] text-sm font-normal max-xl:text-center text-gray-500">I blend
                                clean design with efficient code to build engaging, user-friendly web experiences that
                                stand out.</p>
                        </div>
                        <div class="grid xs:grid-cols-2 justify-end my-2 w-fit mx-auto ">
                            <div
                                class="rounded-xl hover:drop-shadow-2xl shadow-gray-300 ease-out duration-1000  max-xs:mt-3 p-4 sm:p-8 bg-white aspect-auto sm:max-w-78 xs:mb-6">
                                <div class="w-11 h-11 sm:w-18 sm:h-18 bg-[#EDD8FF80] text-center center rounded-md">
                                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="max-sm:p-2 sm:w-8 sm:h-8">
                                        <path
                                            d="M21.3333 18.6667H10.6667C10.313 18.6667 9.97391 18.8072 9.72386 19.0573C9.47381 19.3073 9.33333 19.6465 9.33333 20.0001C9.33333 20.3537 9.47381 20.6928 9.72386 20.9429C9.97391 21.1929 10.313 21.3334 10.6667 21.3334H21.3333C21.687 21.3334 22.0261 21.1929 22.2761 20.9429C22.5262 20.6928 22.6667 20.3537 22.6667 20.0001C22.6667 19.6465 22.5262 19.3073 22.2761 19.0573C22.0261 18.8072 21.687 18.6667 21.3333 18.6667ZM21.3333 13.3334H13.3333C12.9797 13.3334 12.6406 13.4739 12.3905 13.7239C12.1405 13.974 12 14.3131 12 14.6667C12 15.0204 12.1405 15.3595 12.3905 15.6096C12.6406 15.8596 12.9797 16.0001 13.3333 16.0001H21.3333C21.687 16.0001 22.0261 15.8596 22.2761 15.6096C22.5262 15.3595 22.6667 15.0204 22.6667 14.6667C22.6667 14.3131 22.5262 13.974 22.2761 13.7239C22.0261 13.4739 21.687 13.3334 21.3333 13.3334ZM26.6667 5.33341H22.6667V4.00008C22.6667 3.64646 22.5262 3.30732 22.2761 3.05727C22.0261 2.80722 21.687 2.66675 21.3333 2.66675C20.9797 2.66675 20.6406 2.80722 20.3905 3.05727C20.1405 3.30732 20 3.64646 20 4.00008V5.33341H17.3333V4.00008C17.3333 3.64646 17.1929 3.30732 16.9428 3.05727C16.6928 2.80722 16.3536 2.66675 16 2.66675C15.6464 2.66675 15.3072 2.80722 15.0572 3.05727C14.8071 3.30732 14.6667 3.64646 14.6667 4.00008V5.33341H12V4.00008C12 3.64646 11.8595 3.30732 11.6095 3.05727C11.3594 2.80722 11.0203 2.66675 10.6667 2.66675C10.313 2.66675 9.97391 2.80722 9.72386 3.05727C9.47381 3.30732 9.33333 3.64646 9.33333 4.00008V5.33341H5.33333C4.97971 5.33341 4.64057 5.47389 4.39052 5.72394C4.14048 5.97399 4 6.31313 4 6.66675V25.3334C4 26.3943 4.42143 27.4117 5.17157 28.1618C5.92172 28.912 6.93913 29.3334 8 29.3334H24C25.0609 29.3334 26.0783 28.912 26.8284 28.1618C27.5786 27.4117 28 26.3943 28 25.3334V6.66675C28 6.31313 27.8595 5.97399 27.6095 5.72394C27.3594 5.47389 27.0203 5.33341 26.6667 5.33341ZM25.3333 25.3334C25.3333 25.687 25.1929 26.0262 24.9428 26.2762C24.6928 26.5263 24.3536 26.6667 24 26.6667H8C7.64638 26.6667 7.30724 26.5263 7.05719 26.2762C6.80714 26.0262 6.66667 25.687 6.66667 25.3334V8.00008H9.33333V9.33342C9.33333 9.68704 9.47381 10.0262 9.72386 10.2762C9.97391 10.5263 10.313 10.6667 10.6667 10.6667C11.0203 10.6667 11.3594 10.5263 11.6095 10.2762C11.8595 10.0262 12 9.68704 12 9.33342V8.00008H14.6667V9.33342C14.6667 9.68704 14.8071 10.0262 15.0572 10.2762C15.3072 10.5263 15.6464 10.6667 16 10.6667C16.3536 10.6667 16.6928 10.5263 16.9428 10.2762C17.1929 10.0262 17.3333 9.68704 17.3333 9.33342V8.00008H20V9.33342C20 9.68704 20.1405 10.0262 20.3905 10.2762C20.6406 10.5263 20.9797 10.6667 21.3333 10.6667C21.687 10.6667 22.0261 10.5263 22.2761 10.2762C22.5262 10.0262 22.6667 9.68704 22.6667 9.33342V8.00008H25.3333V25.3334Z"
                                            fill="#A53DFF"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 xs:mt-4 sm:mt-8">
                                    <p class="font-semibold sm:text-xl">1. Research</p>
                                    <p class="mt-3 text-[13px] sm:text-[16px] text-[#697482]">Design meets function in
                                        every pixel, blending clarity with intuitive motion.</p>
                                </div>
                            </div>
                            <div
                                class="rounded-xl hover:drop-shadow-2xl shadow-gray-300 ease-out duration-1000  max-xs:mt-3 p-4 sm:p-8 bg-white aspect-auto sm:max-w-78 xs:ms-3 xs:mt-6 ">
                                <div class="w-11 h-11 sm:w-18 sm:h-18 bg-[#EDD8FF80] text-center center rounded-md">
                                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="max-sm:p-2 sm:w-8 sm:h-8">
                                        <path
                                            d="M9.33333 21.3334C9.86377 21.3334 10.3725 21.1227 10.7475 20.7476C11.1226 20.3726 11.3333 19.8638 11.3333 19.3334C11.3398 19.2669 11.3398 19.1999 11.3333 19.1334L15.0533 15.4134H15.36H15.6667L17.8133 17.5601C17.8133 17.5601 17.8133 17.6267 17.8133 17.6667C17.8133 18.1972 18.024 18.7059 18.3991 19.081C18.7742 19.456 19.2829 19.6667 19.8133 19.6667C20.3438 19.6667 20.8525 19.456 21.2275 19.081C21.6026 18.7059 21.8133 18.1972 21.8133 17.6667V17.5601L26.6667 12.6667C27.0622 12.6667 27.4489 12.5495 27.7778 12.3297C28.1067 12.1099 28.363 11.7976 28.5144 11.4321C28.6658 11.0667 28.7054 10.6645 28.6282 10.2766C28.5511 9.88861 28.3606 9.53224 28.0809 9.25253C27.8012 8.97283 27.4448 8.78235 27.0568 8.70518C26.6689 8.62801 26.2668 8.66761 25.9013 8.81899C25.5358 8.97036 25.2235 9.22671 25.0037 9.55561C24.784 9.88451 24.6667 10.2712 24.6667 10.6667C24.6602 10.7333 24.6602 10.8002 24.6667 10.8667L19.8533 15.6801H19.64L17.3333 13.3334C17.3333 12.803 17.1226 12.2943 16.7475 11.9192C16.3725 11.5441 15.8638 11.3334 15.3333 11.3334C14.8029 11.3334 14.2942 11.5441 13.9191 11.9192C13.544 12.2943 13.3333 12.803 13.3333 13.3334L9.33333 17.3334C8.8029 17.3334 8.29419 17.5441 7.91912 17.9192C7.54405 18.2943 7.33333 18.803 7.33333 19.3334C7.33333 19.8638 7.54405 20.3726 7.91912 20.7476C8.29419 21.1227 8.8029 21.3334 9.33333 21.3334ZM27.3333 26.6667H4.66667V4.00008C4.66667 3.64646 4.52619 3.30732 4.27614 3.05727C4.02609 2.80722 3.68696 2.66675 3.33333 2.66675C2.97971 2.66675 2.64057 2.80722 2.39052 3.05727C2.14048 3.30732 2 3.64646 2 4.00008V28.0001C2 28.3537 2.14048 28.6928 2.39052 28.9429C2.64057 29.1929 2.97971 29.3334 3.33333 29.3334H27.3333C27.687 29.3334 28.0261 29.1929 28.2761 28.9429C28.5262 28.6928 28.6667 28.3537 28.6667 28.0001C28.6667 27.6465 28.5262 27.3073 28.2761 27.0573C28.0261 26.8072 27.687 26.6667 27.3333 26.6667Z"
                                            fill="#A53DFF"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 xs:mt-4 sm:mt-8">
                                    <p class="font-semibold sm:text-xl">2. Analyze</p>
                                    <p class="mt-3 text-[13px] sm:text-[16px] text-[#697482]">Crafting clean,
                                        thoughtful interfaces where form flows seamlessly into function and clarity.</p>
                                </div>
                            </div>
                            <div
                                class="rounded-xl hover:drop-shadow-2xl shadow-gray-300 ease-out duration-1000  max-xs:mt-3 p-4 sm:p-8 bg-white aspect-auto sm:max-w-78 xs:mb-6">
                                <div class="w-11 h-11 sm:w-18 sm:h-18 bg-[#EDD8FF80] text-center center rounded-md">
                                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="max-sm:p-2 sm:w-8 sm:h-8">
                                        <path
                                            d="M29.3333 9.65319C29.3343 9.47772 29.3007 9.30377 29.2343 9.14132C29.168 8.97887 29.0702 8.83111 28.9466 8.70653L23.2933 3.05319C23.1687 2.92962 23.021 2.83185 22.8585 2.7655C22.6961 2.69915 22.5221 2.66551 22.3466 2.66653C22.1712 2.66551 21.9972 2.69915 21.8348 2.7655C21.6723 2.83185 21.5246 2.92962 21.4 3.05319L17.6266 6.82653L3.05331 21.3999C2.92974 21.5244 2.83197 21.6722 2.76562 21.8347C2.69927 21.9971 2.66563 22.1711 2.66665 22.3465V27.9999C2.66665 28.3535 2.80712 28.6926 3.05717 28.9427C3.30722 29.1927 3.64636 29.3332 3.99998 29.3332H9.65331C9.83988 29.3433 10.0265 29.3142 10.2011 29.2475C10.3756 29.1809 10.5343 29.0784 10.6666 28.9465L25.16 14.3732L28.9466 10.6665C29.0683 10.5373 29.1675 10.3886 29.24 10.2265C29.2528 10.1202 29.2528 10.0128 29.24 9.90653C29.2462 9.84446 29.2462 9.78192 29.24 9.71986L29.3333 9.65319ZM9.10665 26.6665H5.33331V22.8932L18.5733 9.65319L22.3466 13.4265L9.10665 26.6665ZM24.2266 11.5465L20.4533 7.77319L22.3466 5.89319L26.1066 9.65319L24.2266 11.5465Z"
                                            fill="#A53DFF"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 xs:mt-4 sm:mt-8">
                                    <p class="font-semibold sm:text-xl">3. Design</p>
                                    <p class="mt-3 text-[13px] sm:text-[16px] text-[#697482]">I design seamless digital
                                        experiences with precision, purpose, and a touch of elegance.</p>
                                </div>
                            </div>
                            <div
                                class="rounded-xl hover:drop-shadow-2xl shadow-gray-300 ease-out duration-1000  max-xs:mt-3 p-4 sm:p-8 bg-white aspect-auto sm:max-w-78 xs:ms-3 xs:mt-6 ">
                                <div class="w-11 h-11 sm:w-18 sm:h-18 bg-[#EDD8FF80] text-center center rounded-md">
                                    <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                                        class="max-sm:p-2 sm:w-8 sm:h-8">
                                        <path
                                            d="M28 18.6668H26.6666V9.3335C26.6666 8.27263 26.2452 7.25521 25.4951 6.50507C24.7449 5.75492 23.7275 5.3335 22.6666 5.3335H9.33329C8.27243 5.3335 7.25501 5.75492 6.50487 6.50507C5.75472 7.25521 5.33329 8.27263 5.33329 9.3335V18.6668H3.99996C3.64634 18.6668 3.3072 18.8073 3.05715 19.0574C2.8071 19.3074 2.66663 19.6465 2.66663 20.0002V22.6668C2.66663 23.7277 3.08805 24.7451 3.8382 25.4953C4.58834 26.2454 5.60576 26.6668 6.66663 26.6668H25.3333C26.3942 26.6668 27.4116 26.2454 28.1617 25.4953C28.9119 24.7451 29.3333 23.7277 29.3333 22.6668V20.0002C29.3333 19.6465 29.1928 19.3074 28.9428 19.0574C28.6927 18.8073 28.3536 18.6668 28 18.6668ZM7.99996 9.3335C7.99996 8.97987 8.14044 8.64074 8.39048 8.39069C8.64053 8.14064 8.97967 8.00016 9.33329 8.00016H22.6666C23.0202 8.00016 23.3594 8.14064 23.6094 8.39069C23.8595 8.64074 24 8.97987 24 9.3335V18.6668H7.99996V9.3335ZM26.6666 22.6668C26.6666 23.0205 26.5262 23.3596 26.2761 23.6096C26.0261 23.8597 25.6869 24.0002 25.3333 24.0002H6.66663C6.313 24.0002 5.97387 23.8597 5.72382 23.6096C5.47377 23.3596 5.33329 23.0205 5.33329 22.6668V21.3335H26.6666V22.6668Z"
                                            fill="#A53DFF"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 xs:mt-4 sm:mt-8">
                                    <p class="font-semibold sm:text-xl">4. Launch</p>
                                    <p class="mt-3 text-[13px] sm:text-[16px] text-[#697482]">I craft digital products
                                        where thoughtful design meets performance-driven, responsive development.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content mt-10 md:mt-15 xl:mt-25 mb-10 md:mb-25 max-xxl:p-2" id="portfolio">
                    <div class="xl:mb-17.5 mb-5">
                        <div class="max-sm:px-2 text-center mx-auto max-w-144.25">
                            <p class="section-title ">Portfolio</p>
                            <p class="font-normal text-[18px] max-sm:text-[14px] pt-6 text-gray-400">Here's a selection
                                of my recent work, showcasing my skills in creating user-centric and visually appealing
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
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">I focus on crafting smooth, responsive interfaces
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
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product Admin
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
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product Admin
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
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Created a responsive dashboard layout that adapts
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
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product Admin
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
                                    <p class="text-gray-900 text-md xxs:text-lg font-semibold pt-1 mb-3">Product Admin
                                        Dashboard</p>
                                    <p class="text-gray-600 text-xs xxs:text-[14px] text-wrap"
                                        style="line-height: 20px;">Enhanced user experience by streamlining workflows
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
                <div class="bg-gray-900">
                    <div class="py-25 max-w-169 mx-auto px-2">
                        <div class="text-center">
                            <p class="text-white md:font-semibold text-2xl sm:text-3xl md:text-5xl pb-8">Do you have a
                                Project Idea? Let's discuss your project!</p>
                            <p class="text-[#A5ACB5] text-xs sm:text-lg font-normal text-center pb-8">I'm always open
                                to discussing new projects and creative ideas. Let's connect and build something amazing
                                together.</p><a href="#!"
                                class="btn btn-primary px-4 md:px-6.5 py-3 md:py-6 text-[12px] md:text-[16px]">Let's
                                work Together<svg aria-hidden="true" focusable="false" data-prefix="fas"
                                    data-icon="arrow-right" class="svg-inline--fa fa-arrow-right fa-l ms-3"
                                    role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                    style="color: rgb(255, 255, 255);">
                                    <path fill="currentColor"
                                        d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z">
                                    </path>
                                </svg></a>
                        </div>
                    </div>
                </div>
                <div class="blog-background">
                    <div class="content py-25 px-2 relative" id="blog">
                        <div class="max-w-135 text-center mx-auto pb-17.5">
                            <p class="section-title pb-6">Blog</p>
                            <p class="text-xs xs:text-[16px] md:text-lg text-gray-400">Check out my recent blog posts
                                where I share insights on design, development, and the latest industry trends.</p>
                        </div>
                        <div class="swiper swiper-initialized swiper-horizontal swiper-backface-hidden">
                            <div class="swiper-wrapper" style="cursor: grab;">
                                <div class="swiper-slide swiper-slide-active mb-10"
                                    style="background-color: rgba(0, 0, 0, 0); width: 308px; margin-right: 24px;">
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-100 hover:shadow-2xl bg-white shadow-gray-300 transition-all duration-300">
                                        <a href="#!"><img alt="Blog" class="w-full h-56.5 object-cover "
                                                src="/picto/assets/blog-1-9asS__eh.jpg">
                                            <div class="m-6">
                                                <p class="text-[10px] xs:text-[14px] font-normal text-gray-400 ">22
                                                    Oct, 2020 / 246 Comments</p>
                                                <p class="text-[14px] xs:text-lg font-medium text-[#333333]">Designing
                                                    Engaging User Interfaces for M...</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="swiper-slide swiper-slide-next mb-10"
                                    style="background-color: rgba(0, 0, 0, 0); width: 308px; margin-right: 24px;">
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-100 hover:shadow-2xl bg-white shadow-gray-300 transition-all duration-300">
                                        <a href="#!"><img alt="Blog" class="w-full h-56.5 object-cover "
                                                src="/picto/assets/blog-2-Jn4pr14V.jpg">
                                            <div class="m-6">
                                                <p class="text-[10px] xs:text-[14px] font-normal text-gray-400 ">22
                                                    Oct, 2020 / 246 Comments</p>
                                                <p class="text-[14px] xs:text-lg font-medium text-[#333333]">Tips for
                                                    Effective Dashboard Layouts and...</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="swiper-slide mb-10"
                                    style="background-color: rgba(0, 0, 0, 0); width: 308px; margin-right: 24px;">
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-100 hover:shadow-2xl bg-white shadow-gray-300 transition-all duration-300">
                                        <a href="#!"><img alt="Blog" class="w-full h-56.5 object-cover "
                                                src="/picto/assets/blog-3-lkBU4Y-F.jpg">
                                            <div class="m-6">
                                                <p class="text-[10px] xs:text-[14px] font-normal text-gray-400 ">22
                                                    Oct, 2020 / 246 Comments</p>
                                                <p class="text-[14px] xs:text-lg font-medium text-[#333333]">How to
                                                    Visualize Data for Better Product...</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="swiper-slide mb-10"
                                    style="background-color: rgba(0, 0, 0, 0); width: 308px; margin-right: 24px;">
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-100 hover:shadow-2xl bg-white shadow-gray-300 transition-all duration-300">
                                        <a href="#!"><img alt="Blog" class="w-full h-56.5 object-cover "
                                                src="/picto/assets/blog-4-C9dcE4M3.jpg">
                                            <div class="m-6">
                                                <p class="text-[10px] xs:text-[14px] font-normal text-gray-400 ">22
                                                    Oct, 2020 / 246 Comments</p>
                                                <p class="text-[14px] xs:text-lg font-medium text-[#333333]">Responsive
                                                    Design: Adapting to All Devic...</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="swiper-slide mb-10"
                                    style="background-color: rgba(0, 0, 0, 0); width: 308px; margin-right: 24px;">
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-100 hover:shadow-2xl bg-white shadow-gray-300 transition-all duration-300">
                                        <a href="#!"><img alt="Blog" class="w-full h-56.5 object-cover "
                                                src="/picto/assets/blog-2-Jn4pr14V.jpg">
                                            <div class="m-6">
                                                <p class="text-[10px] xs:text-[14px] font-normal text-gray-400 ">22
                                                    Oct, 2020 / 246 Comments</p>
                                                <p class="text-[14px] xs:text-lg font-medium text-[#333333]">
                                                    Streamlining Workflows with UI/UX Best P...</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="swiper-slide mb-10"
                                    style="background-color: rgba(0, 0, 0, 0); width: 308px; margin-right: 24px;">
                                    <div
                                        class="overflow-hidden rounded-lg border border-gray-100 hover:shadow-2xl bg-white shadow-gray-300 transition-all duration-300">
                                        <a href="#!"><img alt="Blog" class="w-full h-56.5 object-cover "
                                                src="/picto/assets/blog-1-9asS__eh.jpg">
                                            <div class="m-6">
                                                <p class="text-[10px] xs:text-[14px] font-normal text-gray-400 ">22
                                                    Oct, 2020 / 246 Comments</p>
                                                <p class="text-[14px] xs:text-lg font-medium text-[#333333]">Optimizing
                                                    Interface Components for Perf...</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                                <span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span
                                    class="swiper-pagination-bullet"></span><span
                                    class="swiper-pagination-bullet"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-soft-white">
                    <div class="content grid md:grid-cols-2 max-xxl:px-4 xxl:px-2 py-10 md:py-15 lg:py-37.5"
                        id="services">
                        <div
                            class="flex flex-col justify-between h-fit md:pe-8 lg:pe-35.75 max-md:text-center my-auto">
                            <p class="section-title max-md:text-center">What I do?</p>
                            <div class="mt-6 text-[14px]">
                                <p class="text-xs sm:text-lg font-normal text-gray-400 mb-4">I specialize in designing
                                    user experiences, crafting engaging interfaces, and building robust web applications
                                    that deliver value and usability.</p>
                                <p class="text-xs sm:text-lg font-normal text-gray-400">My approach combines creativity
                                    and technical expertise to deliver solutions that are both visually appealing and
                                    highly functional for users.</p>
                            </div><a href="#!"
                                class="mt-5 md:mt-12.5 btn btn-primary text-white w-fit md:py-3 md:px-6 text-[12px] sm:text-[16px] font-semibold max-md:mx-auto max-md:mb-5">Say
                                Hello!</a>
                        </div>
                        <div class="">
                            <div
                                class="p-4 xs:p-8 bg-white hover:shadow-xl h-auto shadow-gray-300 ease-out duration-800 rounded-lg my-6 flex relative overflow-hidden">
                                <p class="bg-picto-primary absolute start-0 w-0 h-full mt-[-16px] xs:mt-[-32px] false">
                                </p>
                                <div>
                                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 pb-4">User Experience
                                        (UX)</p>
                                    <p class="text-[13px] sm:text-[16px] font-normal text-gray-700">I design intuitive
                                        and enjoyable experiences by understanding user needs, conducting research, and
                                        creating wireframes and prototypes that enhance usability.</p>
                                </div>
                            </div>
                            <div
                                class="p-4 xs:p-8 bg-white hover:shadow-xl h-auto shadow-gray-300 ease-out duration-800 rounded-lg my-6 flex relative overflow-hidden">
                                <p class="bg-picto-primary absolute start-0 w-0 h-full mt-[-16px] xs:mt-[-32px] false">
                                </p>
                                <div>
                                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 pb-4">User Interface (UI)
                                    </p>
                                    <p class="text-[13px] sm:text-[16px] font-normal text-gray-700">I craft visually
                                        appealing and consistent interfaces, focusing on layout, color, and typography
                                        to ensure a seamless and engaging user journey.</p>
                                </div>
                            </div>
                            <div
                                class="p-4 xs:p-8 bg-white hover:shadow-xl h-auto shadow-gray-300 ease-out duration-800 rounded-lg my-6 flex relative overflow-hidden">
                                <p class="bg-picto-primary absolute start-0 w-0 h-full mt-[-16px] xs:mt-[-32px] false">
                                </p>
                                <div>
                                    <p class="text-xl sm:text-2xl font-semibold text-gray-900 pb-4">Web Development</p>
                                    <p class="text-[13px] sm:text-[16px] font-normal text-gray-700">I build responsive
                                        and high-performance web applications using modern technologies, ensuring
                                        accessibility, scalability, and maintainability.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content py-10 md:py-25 flex flex-col items-center px-2">
                    <div class="max-w-144.25 text-center ">
                        <p class="section-title mb-6">Happy Clients</p>
                        <p class="text-[14px] sm:text-lg text-soft-dark font-normal">I've had the pleasure of working
                            with a diverse range of companies, from startups to established brands.</p>
                    </div>
                    <div class="rfm-marquee-container "
                        style="--pause-on-hover: paused; --pause-on-click: paused; --width: 100%; --transform: none;">
                        <div class="rfm-marquee"
                            style="--play: running; --direction: normal; --duration: 13.04s; --delay: 0s; --iteration-count: infinite; --min-width: 100%;">
                            <div class="rfm-initial-child-container">
                                <div class="rfm-child" style="--transform: none;">
                                    <p class="flex items-center pt-4 md:pt-10"><span
                                            class="ps-5 sm:ps-10 md:ps-20"><svg class="h-5 sm:h-8 md:h-10"
                                                viewBox="0 0 90 30">
                                                <path fill="#B8BCC2"
                                                    d="M38.9 15.51c0 4.26-3.32 7.39-7.4 7.39s-7.4-3.14-7.4-7.39c0-4.28 3.32-7.39 7.4-7.39s7.4 3.1 7.4 7.39zm-3.24 0c0-2.66-1.93-4.48-4.16-4.48-2.23 0-4.16 1.82-4.16 4.48 0 2.63 1.93 4.48 4.16 4.48 2.23 0 4.16-1.85 4.16-4.48zm-23.7 7.47C5.63 22.98.31 17.83.31 11.5S5.63.02 11.96.02c3.5 0 5.99 1.37 7.87 3.16L17.62 5.4c-1.34-1.26-3.16-2.24-5.66-2.24-4.62 0-8.23 3.72-8.23 8.34 0 4.62 3.61 8.34 8.23 8.34 3 0 4.7-1.2 5.79-2.3.9-.9 1.49-2.2 1.74-4.17H12v-3.14h10.52c.11.56.17 1.23.17 1.96 0 2.35-.64 5.49-2.72 7.56-2.02 2.11-4.59 3.23-8.01 3.23zm42.94-7.47c0 4.26-3.32 7.39-7.4 7.39s-7.4-3.14-7.4-7.39c0-4.28 3.32-7.39 7.4-7.39s7.4 3.1 7.4 7.39zm-3.24 0c0-2.66-1.93-4.48-4.16-4.48-2.23 0-4.16 1.82-4.16 4.48 0 2.63 1.93 4.48 4.16 4.48 2.23 0 4.16-1.85 4.16-4.48zM70 8.56v13.27c0 5.46-3.05 7.7-6.86 7.7-3.58 0-5.74-2.41-6.55-4.37l2.83-1.18c.5 1.2 1.74 2.63 3.72 2.63 2.44 0 3.78-1.51 3.78-4.34v-1.06h-.11c-.73.9-2.04 1.68-3.81 1.68-3.7 0-7-3.22-7-7.36 0-4.17 3.3-7.42 7-7.42 1.76 0 3.08.78 3.81 1.65h.11v-1.2H70zm-2.86 6.97c0-2.6-1.74-4.51-3.95-4.51-2.24 0-3.95 1.9-3.95 4.51 0 2.58 1.71 4.45 3.95 4.45 2.22.01 3.95-1.87 3.95-4.45zM75 1.17V22.9h-3V1.17h3zm12.5 16.77l2.48 1.68c-.8 1.2-2.73 3.28-6.06 3.28-4.13 0-7.22-3.25-7.22-7.39 0-4.4 3.11-7.39 6.86-7.39 3.78 0 5.62 3.05 6.23 4.7l.31.85-9.71 4.08c.74 1.48 1.9 2.24 3.53 2.24s2.76-.82 3.58-2.05zm-7.63-2.66l6.5-2.74c-.36-.92-1.43-1.57-2.7-1.57-1.62 0-3.88 1.46-3.8 4.31z">
                                                </path>
                                            </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-5 sm:h-8 md:h-10"
                                                viewBox="0 0 210 59">
                                                <path fill="#B8BCC2" fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M206.622 31.928C207.065 31.4116 207.85 31.4352 208.253 31.986H208.25L209.784 34.0834C210.075 34.4864 210.073 35.0425 209.769 35.4349C207.106 38.8893 202.44 42.2143 196.81 42.5359C192.366 42.7887 188.701 41.1051 186.706 37.9221C186.311 37.2925 185.44 37.2557 184.997 37.8511C182.63 41.0286 179.766 43.5134 176.782 43.6845C171.467 43.9876 169.966 40.4228 171.28 32.563C171.412 31.7805 170.726 31.1192 169.987 31.3141C168.885 31.6065 167.715 31.7356 166.528 31.633C166.034 31.5907 165.571 31.8912 165.422 32.3811C163.455 38.8418 158.774 44.8518 152.715 45.1997C148.847 45.421 143.069 43.205 143.647 33.9462C143.695 33.1927 143.019 32.5999 142.323 32.8106C141.11 33.1795 139.804 33.3534 138.474 33.2401C137.981 33.1979 137.52 33.4983 137.371 33.9885C135.404 40.449 130.723 46.4592 124.664 46.8068C120.796 47.0282 115.018 44.8124 115.596 35.5536C115.644 34.7998 114.968 34.207 114.272 34.418C113.059 34.7869 111.753 34.9634 110.423 34.8473C109.93 34.8053 109.469 35.1057 109.32 35.5956C107.352 42.0564 102.672 48.0664 96.6132 48.4142C93.8613 48.5723 90.1398 47.4945 88.4308 43.5264C88.1016 42.7599 87.1144 42.6438 86.6257 43.3105C84.2334 46.5751 81.3193 49.152 78.2762 49.3259C75.1571 49.505 73.4509 48.2535 72.7091 46.0216C72.4458 45.2339 71.4609 45.0467 70.9293 45.6712C68.8002 48.1744 66.3749 50.0082 63.9216 50.1479C60.1393 50.3666 57.9619 47.563 57.7823 44.1667C57.5747 40.204 59.2887 35.564 61.2025 30.4999C61.4684 29.7964 60.9873 29.0348 60.2608 29.0032C59.157 28.956 57.8963 28.8399 56.7113 28.6185C56.1771 28.5159 55.6583 28.8479 55.5063 29.3907C53.243 37.4716 49.7771 45.392 46.8529 50.074C46.5263 50.5984 45.8505 50.7381 45.3593 50.377L43.1264 48.7331C42.6682 48.393 42.5441 47.7397 42.8504 47.247C47.0759 40.478 50.8278 29.8807 52.1215 22.0421C52.2025 21.5415 52.61 21.17 53.0986 21.141L56.0683 20.9697C56.7493 20.9302 57.2861 21.5652 57.162 22.2634L57.1493 22.3372C57.0379 22.959 57.4532 23.5439 58.0532 23.6257C60.7164 23.992 64.6963 24.0366 67.3975 23.9313C68.157 23.9023 68.6938 24.6875 68.4178 25.4226C66.2507 31.1876 63.3469 39.1765 63.5139 42.3382C63.5899 43.7662 64.2204 44.5462 65.3291 44.4829C67.4508 44.3619 70.7141 40.0959 73.1876 35.3455C73.2331 35.261 73.2659 35.169 73.2862 35.0741C74.1196 31.3543 75.3565 27.2068 76.6061 23.0163L76.6837 22.7561C76.8128 22.3188 77.1901 22.0131 77.6306 21.9868L81.1876 21.7839C81.9219 21.7417 82.4712 22.4795 82.2485 23.2093C82.0654 23.8112 81.883 24.409 81.7023 25.0014C78.5723 35.2603 75.9438 43.8759 79.4838 43.6742C81.7978 43.5422 85.0764 39.6164 87.8966 34.0279C87.9421 33.9356 87.9751 33.8381 87.9954 33.7355C88.1372 33.0055 88.3092 32.2416 88.5195 31.4432C90.1639 24.8753 92.0286 18.3691 93.8955 11.855C94.4717 9.8446 95.0481 7.83341 95.6182 5.81945C95.7449 5.37417 96.1245 5.06062 96.57 5.03426L100.221 4.82611C100.963 4.78396 101.512 5.52962 101.279 6.26474C99.8208 10.8388 98.2967 15.7106 96.8487 20.4006C96.5448 21.3887 97.603 22.2107 98.4386 21.6416C99.8791 20.6562 101.545 20.0027 103.158 19.9105C107.267 19.676 110.064 23.0565 110.332 28.1496C110.347 28.4184 110.363 28.7082 110.37 29.0032C110.385 29.5673 110.808 30.023 111.348 30.0704C113.282 30.2417 115.259 29.6673 116.786 28.3051C116.943 28.1654 117.049 27.9757 117.102 27.7701C118.616 21.8916 120.287 16.0568 121.959 10.2147C122.532 8.21455 123.105 6.21353 123.672 4.20956C123.798 3.76427 124.178 3.45072 124.624 3.42438L128.274 3.21623C129.016 3.17408 129.566 3.91972 129.333 4.65484C127.874 9.22892 126.35 14.1007 124.902 18.7907C124.598 19.7788 125.657 20.6008 126.492 20.0317C127.933 19.0463 129.599 18.3929 131.211 18.3006C135.32 18.0662 138.117 21.4466 138.386 26.5399C138.401 26.8084 138.416 27.0985 138.424 27.3935C138.436 27.9573 138.862 28.4132 139.401 28.4607C141.335 28.6318 143.312 28.0573 144.839 26.6951C144.996 26.5557 145.102 26.3659 145.156 26.1604C146.67 20.2818 148.34 14.4471 150.013 8.6051C150.586 6.60484 151.158 4.60372 151.725 2.59968C151.852 2.15439 152.232 1.84085 152.677 1.8145L156.328 1.60635C157.07 1.56419 157.619 2.30985 157.386 3.04497C155.928 7.61902 154.404 12.4908 152.956 17.1808C152.652 18.1689 153.71 18.991 154.546 18.4219C155.986 17.4364 157.652 16.783 159.265 16.6908C163.374 16.4563 166.171 19.8367 166.44 24.9299C166.455 25.2013 166.47 25.4885 166.477 25.7835C166.493 26.3447 166.913 26.8032 167.452 26.8507C169.323 27.0166 171.237 26.4844 172.741 25.2171C172.908 25.0774 173.024 24.8798 173.08 24.6637C174.804 18.0187 177.336 9.31324 179.777 0.981894C179.906 0.541877 180.285 0.236236 180.726 0.209888L184.344 0.0017367C185.078 -0.0404207 185.627 0.692063 185.407 1.42191C182.047 12.5778 179.308 22.3372 177.797 28.0944C175.789 35.9039 175.711 38.1567 177.994 38.025C179.911 37.9143 182.493 35.1952 184.928 31.0847C185.025 30.924 185.075 30.7397 185.083 30.5526C185.402 22.324 190.447 14.8385 197.946 14.409C202.969 14.1218 205.721 17.916 205.918 21.6495C206.293 28.7767 199.248 33.3324 192.42 32.9107C191.625 32.8606 191.047 33.7145 191.397 34.4574C192.351 36.4967 194.359 37.6352 197.787 37.4374C201.048 37.2531 204.468 34.439 206.622 31.928ZM93.7548 33.9278C92.1345 40.4228 94.1017 42.9652 96.646 42.8203C100.823 42.5805 104.864 34.9263 104.553 29.019C104.416 26.4396 102.907 25.0958 101.145 25.1961C98.2106 25.3646 95.0512 28.745 93.7548 33.9278ZM121.808 32.3207C120.188 38.8154 122.155 41.3581 124.7 41.2131H124.697C128.874 40.9734 132.917 33.3192 132.606 27.4119C132.472 24.8324 130.96 23.4886 129.198 23.5887C126.264 23.7574 123.105 27.1379 121.808 32.3207ZM149.862 30.7133C148.242 37.2082 150.209 39.7509 152.753 39.606H152.751C156.925 39.3662 160.971 31.712 160.66 25.8047C160.525 23.2251 159.014 21.8814 157.252 21.9815C154.318 22.1501 151.158 25.5307 149.862 30.7133ZM200.584 22.2239C200.559 20.5218 199.513 19.2887 197.817 19.3862H197.815C194.483 19.5785 191.875 23.1856 191.045 27.562C190.913 28.2577 191.422 28.9058 192.103 28.8899C196.407 28.7821 200.721 25.9416 200.584 22.2239ZM44.3525 25.3837C43.9171 12.1962 35.3423 3.49339 22.6712 3.94658C17.2307 4.19426 11.0052 6.25733 6.32164 9.9461C5.88113 10.2939 5.76719 10.9315 6.06593 11.4163L8.05331 14.6519C8.39254 15.2052 9.11407 15.3185 9.60776 14.9075C13.1724 11.9459 18.0383 10.0041 22.7193 9.79855C31.403 9.43757 37.7828 14.9971 38.1551 25.7367C38.6209 38.2417 30.2157 52.5461 16.7091 53.3207C16.2382 53.3471 15.7471 53.3577 15.2559 53.3577C14.5673 53.3577 14.0585 52.6858 14.2306 51.9901C16.8357 41.4744 19.8763 30.1974 22.9776 19.7029C23.1928 18.973 22.6459 18.2458 21.9143 18.288L17.9648 18.5146C17.5218 18.5409 17.142 18.8492 17.0129 19.2918C14.0331 29.6045 11.0508 40.7895 8.36723 51.284C8.21279 51.89 7.59761 52.2379 7.02544 52.0427C5.62543 51.566 4.34693 51.0232 3.2583 50.3881C2.73677 50.0825 2.07601 50.2987 1.80765 50.8571L0.11142 54.4037C-0.139216 54.9281 0.0455967 55.5709 0.539275 55.8527C4.38489 58.0345 10.223 59.2806 16.0914 58.9462C35.4032 57.8393 44.864 40.0015 44.3525 25.3889V25.3837ZM82.3044 9.18082C79.955 9.31518 77.8713 11.9553 78.0183 14.7377C78.1143 16.5715 79.2917 17.7967 81.1195 17.694C83.4689 17.5596 85.6106 14.7798 85.4714 12.1318C85.3754 10.298 84.0005 9.08333 82.3044 9.18082Z">
                                                </path>
                                            </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 568.291 144"
                                                class="h-5 sm:h-8 md:h-10">
                                                <g>
                                                    <g>
                                                        <g>
                                                            <g>
                                                                <g>
                                                                    <polygon fill="#231F20"
                                                                        points="0,21.297 22.215,21.297 22.215,102.206 63.342,102.206 63.342,122.703 0,122.703      ">
                                                                    </polygon>
                                                                    <g>
                                                                        <path fill="#231F20"
                                                                            d="M73.154,53.955h21.352v68.721H73.154V53.955z M83.83,19.794c6.837,0,12.386,5.549,12.386,12.38        c0,6.843-5.549,12.376-12.386,12.376c-6.829,0-12.378-5.534-12.378-12.376C71.451,25.344,77,19.794,83.83,19.794">
                                                                        </path>
                                                                    </g>
                                                                    <polygon fill="#231F20"
                                                                        points="183.755,21.29 205.099,21.29 205.099,81.897 229.277,53.924 255.444,53.924 227.442,85.742        254.859,122.703 228.029,122.703 205.376,88.732 205.099,88.732 205.099,122.703 183.755,122.703      ">
                                                                    </polygon>
                                                                    <path fill="#231F20"
                                                                        d="M106.314,53.964h20.495v9.394h0.286c2.844-5.409,9.82-11.1,20.209-11.1       c21.638,0,25.622,14.232,25.622,32.731v37.687h-21.351V89.263c0-7.974-0.146-18.217-11.107-18.217       c-11.1,0-12.802,8.684-12.802,17.647v33.983h-21.351V53.964z">
                                                                    </path>
                                                                    <path fill="#231F20"
                                                                        d="M300.386,80.326c0.14-7.117-5.411-13.096-13.104-13.096c-9.389,0-14.517,6.405-15.08,13.096H300.386z        M318.457,110.778c-6.833,8.684-18.071,13.671-29.179,13.671c-21.346,0-38.427-14.241-38.427-36.302       c0-22.058,17.082-36.29,38.427-36.29c19.942,0,32.456,14.231,32.456,36.29v6.69h-49.531c1.695,8.114,7.825,13.38,16.082,13.38       c6.977,0,11.67-3.56,15.229-8.399L318.457,110.778z">
                                                                    </path>
                                                                    <path fill="#231F20"
                                                                        d="M366.199,70.573c-10.667,0-17.07,7.122-17.07,17.503c0,10.399,6.403,17.507,17.07,17.507       c10.683,0,17.09-7.107,17.09-17.507C383.289,77.694,376.882,70.573,366.199,70.573 M402.936,122.665h-19.646v-9.112h-0.277       c-3.284,4.98-11.532,10.819-21.214,10.819c-20.502,0-34.021-14.797-34.021-35.723c0-19.217,11.955-36.866,31.596-36.866       c8.833,0,17.086,2.417,21.927,9.108h0.277V21.29h21.359V122.665z">
                                                                    </path>
                                                                </g>
                                                                <path fill="#0077B5"
                                                                    d="M557.632,0H434.916c-5.864,0-10.629,4.648-10.629,10.376V133.61c0,5.734,4.765,10.39,10.629,10.39      h122.716c5.874,0,10.659-4.655,10.659-10.39V10.376C568.291,4.648,563.506,0,557.632,0z">
                                                                </path>
                                                                <path fill="#FFFFFF"
                                                                    d="M445.638,53.985h21.359v68.722h-21.359V53.985z M456.322,19.825c6.828,0,12.377,5.549,12.377,12.38      c0,6.837-5.549,12.386-12.377,12.386c-6.846,0-12.387-5.549-12.387-12.386C443.936,25.375,449.477,19.825,456.322,19.825">
                                                                </path>
                                                                <path fill="#FFFFFF"
                                                                    d="M480.394,53.985h20.485v9.39h0.286c2.852-5.403,9.818-11.099,20.209-11.099      c21.628,0,25.621,14.234,25.621,32.736v37.694h-21.344v-33.42c0-7.969-0.146-18.22-11.099-18.22      c-11.113,0-12.819,8.681-12.819,17.644v33.996h-21.34V53.985z">
                                                                </path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                                class="w-10 sm:w-20 md:w-30 aspect-square" viewBox="0 0 650 80">
                                                <path
                                                    d="M 402.46213,152.88634 C 364.76308,180.67353 310.1206,195.499 263.07446,195.499 c -65.9663,0 -125.35289,-24.39849 -170.281138,-64.97793 -3.529868,-3.19103 -0.367119,-7.53982 3.868745,-5.0548 48.486343,28.21077 108.437743,45.18241 170.365863,45.18241 41.76544,0 87.71029,-8.64115 129.95583,-26.5729 6.38201,-2.71096 11.71918,4.17937 5.47837,8.81056"
                                                    fill="#B8BCC2"></path>
                                                <path
                                                    d="m 418.13478,134.95459 c -4.80065,-6.1561 -31.8536,-2.90862 -43.99636,-1.46843 -3.69932,0.45183 -4.26408,-2.76742 -0.9319,-5.08303 21.54636,-15.16434 56.90159,-10.78729 61.02448,-5.70428 4.12288,5.11127 -1.07308,40.55122 -21.32045,57.46636 -3.10629,2.59798 -6.07138,1.21429 -4.68766,-2.23088 4.54647,-11.35208 14.74076,-36.7954 9.91189,-42.97974"
                                                    fill="#B8BCC2"></path>
                                                <path
                                                    d="m 374.98559,21.349107 0,-14.740752 c 0,-2.2308798 1.69434,-3.7275435 3.72755,-3.7275435 l 65.99451,0 c 2.11794,0 3.81228,1.5249061 3.81228,3.7275435 l 0,12.622831 c -0.0282,2.117921 -1.8073,4.885342 -4.97008,9.262385 l -34.19741,48.825227 c 12.70756,-0.310612 26.12108,1.58138 37.64259,8.076355 2.59798,1.468422 3.30396,3.614585 3.50163,5.732527 l 0,15.72912 c 0,2.14616 -2.37208,4.65941 -4.85711,3.36043 -20.30384,-10.646108 -47.27208,-11.803906 -69.72208,0.11296 -2.28735,1.24252 -4.68766,-1.24251 -4.68766,-3.38868 l 0,-14.938423 c 0,-2.400313 0.0282,-6.494986 2.42853,-10.137813 l 39.61932,-56.816855 -34.47981,0 c -2.11792,0 -3.81226,-1.496653 -3.81226,-3.699312"
                                                    fill="#B8BCC2"></path>
                                                <path
                                                    d="m 134.24816,113.32351 -20.07792,0 c -1.92024,-0.14118 -3.44515,-1.58137 -3.58635,-3.41692 l 0,-103.0440642 c 0,-2.061447 1.72258,-3.6993227 3.86874,-3.6993227 l 18.72245,0 c 1.94851,0.084663 3.50163,1.5813802 3.64284,3.4451519 l 0,13.470017 0.36711,0 c 4.88534,-13.0181926 14.063,-19.08957463 26.43169,-19.08957463 12.56635,0 20.41679,6.07138203 26.06459,19.08957463 4.85711,-13.0181926 15.89855,-19.08957463 27.7307,-19.08957463 8.41522,0 17.62113,3.47339423 23.24069,11.26735763 6.35377,8.66937 5.05477,21.263959 5.05477,32.30542 l -0.0282,65.034405 c 0,2.06144 -1.72258,3.72753 -3.86874,3.72753 l -20.0497,0 c -2.00495,-0.14118 -3.61457,-1.7508 -3.61457,-3.72753 l 0,-54.614222 c 0,-4.348801 0.39532,-15.192576 -0.56479,-19.315481 -1.49666,-6.918535 -5.98666,-8.867045 -11.80391,-8.867045 -4.85711,0 -9.94012,3.247487 -12.00157,8.443474 -2.06145,5.195975 -1.86377,13.893566 -1.86377,19.739052 l 0,54.614222 c 0,2.06144 -1.72258,3.72753 -3.86875,3.72753 l -20.04967,0 c -2.03321,-0.14118 -3.6146,-1.7508 -3.6146,-3.72753 l -0.0282,-54.614222 c 0,-11.493265 1.89199,-28.408433 -12.36868,-28.408433 -14.43014,0 -13.86536,16.491565 -13.86536,28.408433 l 0,54.614222 c 0,2.06144 -1.72258,3.72753 -3.86874,3.72753"
                                                    fill="#B8BCC2"></path>
                                                <path
                                                    d="m 505.33679,0.98879737 c 29.79214,0 45.91659,25.58452763 45.91659,58.11585463 0,31.429992 -17.81882,56.365018 -45.91659,56.365018 -29.25561,0 -45.18241,-25.584526 -45.18241,-57.466342 0,-32.079503 16.12447,-57.01453063 45.18241,-57.01453063 M 505.50621,22.02686 c -14.79722,0 -15.72912,20.162623 -15.72912,32.728991 0,12.594589 -0.19767,39.478105 15.5597,39.478105 15.55968,0 16.2939,-21.68753 16.2939,-34.903408 0,-8.697591 -0.36711,-19.089542 -2.99335,-27.335341 -2.25912,-7.172684 -6.7491,-9.968347 -13.13113,-9.968347"
                                                    fill="#B8BCC2"></path>
                                                <path
                                                    d="m 589.88433,113.32351 -19.9932,0 c -2.00498,-0.14118 -3.6146,-1.7508 -3.6146,-3.72753 L 566.2483,6.5236386 c 0.16944,-1.8920141 1.83555,-3.3604355 3.86875,-3.3604355 l 18.60951,0 c 1.7508,0.084663 3.19099,1.2707569 3.58635,2.8803795 l 0,15.7573484 0.36709,0 c 5.61958,-14.0912519 13.49823,-20.81213363 27.36358,-20.81213363 9.00824,0 17.79057,3.24748743 23.43837,12.14276463 5.25245,8.245787 5.25245,22.111144 5.25245,32.079491 l 0,64.864987 c -0.2259,1.80728 -1.89201,3.24747 -3.86872,3.24747 l -20.13441,0 c -1.83555,-0.14118 -3.36046,-1.49665 -3.55811,-3.24747 l 0,-55.969688 c 0,-11.267358 1.29899,-27.758945 -12.56637,-27.758945 -4.88533,0 -9.37534,3.275741 -11.60622,8.245788 -2.82389,6.297311 -3.191,12.566368 -3.191,19.513157 l 0,55.489628 c -0.0282,2.06144 -1.77905,3.72753 -3.92521,3.72753"
                                                    fill="#B8BCC2"></path>
                                                <path
                                                    d="m 322.37634,64.102952 c 0,7.822195 0.19767,14.345402 -3.75579,21.292201 -3.191,5.647811 -8.27402,9.121205 -13.89357,9.121205 -7.70925,0 -12.22749,-5.873728 -12.22749,-14.543098 0,-17.112822 15.33377,-20.219108 29.87685,-20.219108 l 0,4.3488 m 20.24736,48.938168 c -1.32722,1.18604 -3.24749,1.27075 -4.74415,0.48007 -6.66441,-5.53484 -7.87868,-8.10458 -11.52151,-13.38527 -11.01321,11.23911 -18.83542,14.59954 -33.09611,14.59954 -16.91516,0 -30.04628,-10.42019 -30.04628,-31.288784 0,-16.293911 8.81058,-27.391836 21.40517,-32.813719 10.90024,-4.800646 26.12104,-5.6478 37.75552,-6.975031 l 0,-2.597987 c 0,-4.772405 0.36711,-10.420205 -2.45679,-14.543077 -2.42855,-3.699323 -7.11624,-5.224229 -11.26736,-5.224229 -7.65278,0 -14.45836,3.925219 -16.12447,12.058048 -0.33887,1.807308 -1.66609,3.586364 -3.50164,3.67108 l -19.45666,-2.0897 c -1.63787,-0.367087 -3.47339,-1.694339 -2.99333,-4.207589 4.46176,-23.607797 25.81045,-30.72403991022 44.89999,-30.72403991022 9.7707,0 22.53472,2.59798781022 30.24397,9.99660081022 9.77068,9.1211941 8.8388,21.2922011 8.8388,34.5363001 l 0,31.288801 c 0,9.403597 3.89698,13.52647 7.56805,18.609508 1.27077,1.807276 1.55316,3.981682 -0.0848,5.337155 -4.09465,3.416933 -11.38032,9.770703 -15.39025,13.328813 l -0.0282,-0.0566"
                                                    id="path28" fill="#B8BCC2" fill-rule="evenodd" stroke="none"
                                                    inkscape:connector-curvature="0"></path>
                                                <path
                                                    d="m 59.160693,64.102952 c 0,7.822195 0.197653,14.345402 -3.755787,21.292201 -3.191024,5.647811 -8.245799,9.121205 -13.893599,9.121205 -7.709247,0 -12.199238,-5.873728 -12.199238,-14.543098 0,-17.112822 15.333767,-20.219108 29.848624,-20.219108 l 0,4.3488 m 20.247351,48.938168 c -1.327231,1.18604 -3.247499,1.27075 -4.744162,0.48007 -6.664387,-5.53484 -7.850438,-8.10458 -11.521507,-13.38527 -11.01321,11.23911 -18.807152,14.59954 -33.096101,14.59954 C 13.159348,114.73546 -2.141151e-6,104.31527 -2.141151e-6,83.446676 -2.141151e-6,67.152765 8.8388006,56.05484 21.405147,50.632957 c 10.900249,-4.800646 26.121068,-5.6478 37.755546,-6.975031 l 0,-2.597987 c 0,-4.772405 0.367108,-10.420205 -2.428555,-14.543077 -2.456809,-3.699323 -7.144465,-5.224229 -11.26737,-5.224229 -7.652751,0 -14.486602,3.925219 -16.152699,12.058048 -0.338876,1.807308 -1.666107,3.586364 -3.473394,3.67108 L 6.3537716,34.932061 C 4.7159066,34.564974 2.9086089,33.237722 3.3604333,30.724472 7.8504353,7.116675 29.170868,4.3208978e-4 48.260442,4.3208978e-4 c 9.770674,0 22.534695,2.59798781022 30.243942,9.99660081022 9.770705,9.1211941 8.838803,21.2922011 8.838803,34.5363001 l 0,31.288801 c 0,9.403597 3.896998,13.52647 7.568067,18.609508 1.298989,1.807276 1.58138,3.981682 -0.05648,5.337155 -4.094651,3.416933 -11.380305,9.770703 -15.390262,13.328813 l -0.05646,-0.0566"
                                                    id="path30" fill="#B8BCC2" fill-rule="evenodd" stroke="none"
                                                    inkscape:connector-curvature="0"></path>
                                            </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-3 sm:h-5 md:h-7"
                                                viewBox="0 0 719 160">
                                                <path fill="#B8BCC2"
                                                    d="m174.104 9.734.215-.047V8.02H130.39L89.6 103.89 48.81 8.021H1.472v1.666l.212.047c8.018 1.81 12.09 4.509 12.09 14.242V137.93c0 9.734-4.087 12.433-12.106 14.243l-.212.047v1.671h32.118v-1.665l-.213-.048c-8.018-1.809-12.089-4.509-12.089-14.242V30.586l52.399 123.305h2.972l53.925-126.743V140.75c-.687 7.688-4.721 10.062-11.982 11.701l-.215.05v1.652h55.948v-1.652l-.215-.05c-7.269-1.639-11.4-4.013-12.087-11.701l-.037-116.774h.037c0-9.733 4.071-12.432 12.087-14.242m25.555 75.488c.915-20.474 8.268-35.252 20.606-35.507 3.806.063 6.998 1.312 9.479 3.714 5.272 5.118 7.751 15.812 7.368 31.793zm-.553 5.77h65.573v-.275c-.186-15.656-4.721-27.834-13.466-36.196-7.559-7.227-18.751-11.203-30.507-11.203h-.263c-6.101 0-13.584 1.48-18.909 4.16-6.061 2.807-11.407 7.003-15.855 12.511-7.161 8.874-11.499 20.866-12.554 34.343q-.05.606-.092 1.212a50 50 0 0 0-.065 1.151 85.807 85.807 0 0 0-.094 5.689c.71 30.524 17.198 54.917 46.483 54.917 25.705 0 40.675-18.791 44.407-44.013l-1.886-.664c-6.557 13.556-18.334 21.771-31.738 20.769-18.297-1.369-32.314-19.922-31.042-42.395m139.722 41.359c-2.151 5.101-6.639 7.908-12.653 7.908s-11.513-4.129-15.418-11.63c-4.197-8.053-6.405-19.436-6.405-32.92 0-28.067 8.729-46.22 22.24-46.22 5.657 0 10.111 2.807 12.236 7.704zm43.499 20.008c-8.019-1.897-12.089-4.722-12.089-14.951V1.309l-48.716 14.353v1.757l.299-.024c6.72-.543 11.278.386 13.925 2.83 2.072 1.915 3.082 4.853 3.082 8.987v18.66c-4.803-3.067-10.516-4.56-17.448-4.56-14.059 0-26.909 5.92-36.176 16.672-9.66 11.205-14.767 26.518-14.767 44.278-.003 31.72 15.612 53.039 38.851 53.039 13.595 0 24.533-7.449 29.54-20.013v16.865h43.711v-1.746zM424.1 19.819c0-9.904-7.468-17.374-17.375-17.374-9.859 0-17.573 7.632-17.573 17.374s7.721 17.374 17.573 17.374c9.907 0 17.375-7.47 17.375-17.374m11.499 132.546c-8.019-1.897-12.089-4.722-12.089-14.951h-.035V43.635l-43.714 12.551v1.705l.263.024c9.458.842 12.047 4.1 12.047 15.152v81.086h43.751v-1.746zm112.013 0c-8.018-1.897-12.089-4.722-12.089-14.951V43.635l-41.621 12.137v1.71l.246.026c7.733.813 9.967 4.257 9.967 15.36v59.279c-2.578 5.102-7.415 8.131-13.274 8.336-9.503 0-14.736-6.419-14.736-18.073V43.638l-43.714 12.55v1.703l.262.024c9.459.84 12.05 4.097 12.05 15.152v50.17a56.3 56.3 0 0 0 .91 10.444l.787 3.423c3.701 13.262 13.398 20.197 28.59 20.197 12.868 0 24.147-7.966 29.115-20.43v17.311h43.714v-1.747zm169.818 1.788v-1.749l-.213-.05c-8.7-2.006-12.089-5.789-12.089-13.49v-63.79c0-19.89-11.171-31.761-29.883-31.761-13.64 0-25.141 7.882-29.569 20.16-3.517-13.01-13.639-20.16-28.606-20.16-13.146 0-23.449 6.938-27.869 18.657V43.643L545.487 55.68v1.715l.263.024c9.345.829 12.047 4.181 12.047 14.95v81.784h40.787v-1.746l-.215-.053c-6.941-1.631-9.181-4.606-9.181-12.239V66.998c1.836-4.289 5.537-9.37 12.853-9.37 9.086 0 13.692 6.296 13.692 18.697v77.828h40.797v-1.746l-.215-.053c-6.94-1.631-9.18-4.606-9.18-12.239V75.066a42 42 0 0 0-.578-7.26c1.947-4.661 5.86-10.177 13.475-10.177 9.214 0 13.691 6.114 13.691 18.696v77.828z">
                                                </path>
                                            </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                                xmlns="http://www.w3.org/2000/svg" class="h-5 sm:h-8 md:h-10"
                                                viewBox="0 0 559 168">
                                                <path fill="#B8BCC2"
                                                    d="m83.996 0.277c-46.249 0-83.743 37.493-83.743 83.742 0 46.251 37.494 83.741 83.743 83.741 46.254 0 83.744-37.49 83.744-83.741 0-46.246-37.49-83.738-83.745-83.738l0.001-0.004zm38.404 120.78c-1.5 2.46-4.72 3.24-7.18 1.73-19.662-12.01-44.414-14.73-73.564-8.07-2.809 0.64-5.609-1.12-6.249-3.93-0.643-2.81 1.11-5.61 3.926-6.25 31.9-7.288 59.263-4.15 81.337 9.34 2.46 1.51 3.24 4.72 1.73 7.18zm10.25-22.802c-1.89 3.072-5.91 4.042-8.98 2.152-22.51-13.836-56.823-17.843-83.448-9.761-3.453 1.043-7.1-0.903-8.148-4.35-1.04-3.453 0.907-7.093 4.354-8.143 30.413-9.228 68.222-4.758 94.072 11.127 3.07 1.89 4.04 5.91 2.15 8.976v-0.001zm0.88-23.744c-26.99-16.031-71.52-17.505-97.289-9.684-4.138 1.255-8.514-1.081-9.768-5.219-1.254-4.14 1.08-8.513 5.221-9.771 29.581-8.98 78.756-7.245 109.83 11.202 3.73 2.209 4.95 7.016 2.74 10.733-2.2 3.722-7.02 4.949-10.73 2.739zm94.56 3.072c-14.46-3.448-17.03-5.868-17.03-10.953 0-4.804 4.52-8.037 11.25-8.037 6.52 0 12.98 2.455 19.76 7.509 0.2 0.153 0.46 0.214 0.71 0.174 0.26-0.038 0.48-0.177 0.63-0.386l7.06-9.952c0.29-0.41 0.21-0.975-0.18-1.288-8.07-6.473-17.15-9.62-27.77-9.62-15.61 0-26.52 9.369-26.52 22.774 0 14.375 9.41 19.465 25.67 23.394 13.83 3.187 16.17 5.857 16.17 10.629 0 5.29-4.72 8.58-12.32 8.58-8.44 0-15.33-2.85-23.03-9.51-0.19-0.17-0.45-0.24-0.69-0.23-0.26 0.02-0.49 0.14-0.65 0.33l-7.92 9.42c-0.33 0.4-0.29 0.98 0.09 1.32 8.96 8 19.98 12.22 31.88 12.22 16.82 0 27.69-9.19 27.69-23.42 0.03-12.007-7.16-18.657-24.77-22.941l-0.03-0.013zm62.86-14.26c-7.29 0-13.27 2.872-18.21 8.757v-6.624c0-0.523-0.42-0.949-0.94-0.949h-12.95c-0.52 0-0.94 0.426-0.94 0.949v73.601c0 0.52 0.42 0.95 0.94 0.95h12.95c0.52 0 0.94-0.43 0.94-0.95v-23.23c4.94 5.53 10.92 8.24 18.21 8.24 13.55 0 27.27-10.43 27.27-30.369 0.02-19.943-13.7-30.376-27.26-30.376l-0.01 0.001zm12.21 30.375c0 10.149-6.25 17.239-15.21 17.239-8.85 0-15.53-7.41-15.53-17.239 0-9.83 6.68-17.238 15.53-17.238 8.81-0.001 15.21 7.247 15.21 17.237v0.001zm50.21-30.375c-17.45 0-31.12 13.436-31.12 30.592 0 16.972 13.58 30.262 30.91 30.262 17.51 0 31.22-13.39 31.22-30.479 0-17.031-13.62-30.373-31.01-30.373v-0.002zm0 47.714c-9.28 0-16.28-7.46-16.28-17.344 0-9.929 6.76-17.134 16.07-17.134 9.34 0 16.38 7.457 16.38 17.351 0 9.927-6.8 17.127-16.17 17.127zm68.27-46.53h-14.25v-14.566c0-0.522-0.42-0.948-0.94-0.948h-12.95c-0.52 0-0.95 0.426-0.95 0.948v14.566h-6.22c-0.52 0-0.94 0.426-0.94 0.949v11.127c0 0.522 0.42 0.949 0.94 0.949h6.22v28.795c0 11.63 5.79 17.53 17.22 17.53 4.64 0 8.49-0.96 12.12-3.02 0.3-0.16 0.48-0.48 0.48-0.82v-10.6c0-0.32-0.17-0.63-0.45-0.8-0.28-0.18-0.63-0.19-0.92-0.04-2.49 1.25-4.9 1.83-7.6 1.83-4.15 0-6.01-1.89-6.01-6.11v-26.76h14.25c0.52 0 0.94-0.426 0.94-0.949v-11.126c0.02-0.523-0.4-0.949-0.93-0.949l-0.01-0.006zm49.64 0.057v-1.789c0-5.263 2.02-7.61 6.54-7.61 2.7 0 4.87 0.536 7.3 1.346 0.3 0.094 0.61 0.047 0.85-0.132 0.25-0.179 0.39-0.466 0.39-0.77v-10.91c0-0.417-0.26-0.786-0.67-0.909-2.56-0.763-5.84-1.546-10.76-1.546-11.95 0-18.28 6.734-18.28 19.467v2.74h-6.22c-0.52 0-0.95 0.426-0.95 0.948v11.184c0 0.522 0.43 0.949 0.95 0.949h6.22v44.405c0 0.53 0.43 0.95 0.95 0.95h12.94c0.53 0 0.95-0.42 0.95-0.95v-44.402h12.09l18.52 44.402c-2.1 4.66-4.17 5.59-6.99 5.59-2.28 0-4.69-0.68-7.14-2.03-0.23-0.12-0.51-0.14-0.75-0.07-0.25 0.09-0.46 0.27-0.56 0.51l-4.39 9.63c-0.21 0.46-0.03 0.99 0.41 1.23 4.58 2.48 8.71 3.54 13.82 3.54 9.56 0 14.85-4.46 19.5-16.44l22.46-58.037c0.12-0.292 0.08-0.622-0.1-0.881-0.17-0.257-0.46-0.412-0.77-0.412h-13.48c-0.41 0-0.77 0.257-0.9 0.636l-13.81 39.434-15.12-39.46c-0.14-0.367-0.49-0.61-0.88-0.61h-22.12v-0.003zm-28.78-0.057h-12.95c-0.52 0-0.95 0.426-0.95 0.949v56.481c0 0.53 0.43 0.95 0.95 0.95h12.95c0.52 0 0.95-0.42 0.95-0.95v-56.477c0-0.523-0.42-0.949-0.95-0.949v-0.004zm-6.4-25.719c-5.13 0-9.29 4.152-9.29 9.281 0 5.132 4.16 9.289 9.29 9.289s9.28-4.157 9.28-9.289c0-5.128-4.16-9.281-9.28-9.281zm113.42 43.88c-5.12 0-9.11-4.115-9.11-9.112s4.04-9.159 9.16-9.159 9.11 4.114 9.11 9.107c0 4.997-4.04 9.164-9.16 9.164zm0.05-17.365c-4.67 0-8.2 3.71-8.2 8.253 0 4.541 3.51 8.201 8.15 8.201 4.67 0 8.2-3.707 8.2-8.253 0-4.541-3.51-8.201-8.15-8.201zm2.02 9.138l2.58 3.608h-2.18l-2.32-3.31h-1.99v3.31h-1.82v-9.564h4.26c2.23 0 3.69 1.137 3.69 3.051 0.01 1.568-0.9 2.526-2.21 2.905h-0.01zm-1.54-4.315h-2.37v3.025h2.37c1.18 0 1.89-0.579 1.89-1.514 0-0.984-0.71-1.511-1.89-1.511z">
                                                </path>
                                            </svg></span></p>
                                </div>
                            </div>
                        </div>
                        <div class="rfm-marquee"
                            style="--play: running; --direction: normal; --duration: 13.04s; --delay: 0s; --iteration-count: infinite; --min-width: 100%;">
                            <div class="rfm-child" style="--transform: none;">
                                <p class="flex items-center pt-4 md:pt-10"><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                            class="h-5 sm:h-8 md:h-10" viewBox="0 0 90 30">
                                            <path fill="#B8BCC2"
                                                d="M38.9 15.51c0 4.26-3.32 7.39-7.4 7.39s-7.4-3.14-7.4-7.39c0-4.28 3.32-7.39 7.4-7.39s7.4 3.1 7.4 7.39zm-3.24 0c0-2.66-1.93-4.48-4.16-4.48-2.23 0-4.16 1.82-4.16 4.48 0 2.63 1.93 4.48 4.16 4.48 2.23 0 4.16-1.85 4.16-4.48zm-23.7 7.47C5.63 22.98.31 17.83.31 11.5S5.63.02 11.96.02c3.5 0 5.99 1.37 7.87 3.16L17.62 5.4c-1.34-1.26-3.16-2.24-5.66-2.24-4.62 0-8.23 3.72-8.23 8.34 0 4.62 3.61 8.34 8.23 8.34 3 0 4.7-1.2 5.79-2.3.9-.9 1.49-2.2 1.74-4.17H12v-3.14h10.52c.11.56.17 1.23.17 1.96 0 2.35-.64 5.49-2.72 7.56-2.02 2.11-4.59 3.23-8.01 3.23zm42.94-7.47c0 4.26-3.32 7.39-7.4 7.39s-7.4-3.14-7.4-7.39c0-4.28 3.32-7.39 7.4-7.39s7.4 3.1 7.4 7.39zm-3.24 0c0-2.66-1.93-4.48-4.16-4.48-2.23 0-4.16 1.82-4.16 4.48 0 2.63 1.93 4.48 4.16 4.48 2.23 0 4.16-1.85 4.16-4.48zM70 8.56v13.27c0 5.46-3.05 7.7-6.86 7.7-3.58 0-5.74-2.41-6.55-4.37l2.83-1.18c.5 1.2 1.74 2.63 3.72 2.63 2.44 0 3.78-1.51 3.78-4.34v-1.06h-.11c-.73.9-2.04 1.68-3.81 1.68-3.7 0-7-3.22-7-7.36 0-4.17 3.3-7.42 7-7.42 1.76 0 3.08.78 3.81 1.65h.11v-1.2H70zm-2.86 6.97c0-2.6-1.74-4.51-3.95-4.51-2.24 0-3.95 1.9-3.95 4.51 0 2.58 1.71 4.45 3.95 4.45 2.22.01 3.95-1.87 3.95-4.45zM75 1.17V22.9h-3V1.17h3zm12.5 16.77l2.48 1.68c-.8 1.2-2.73 3.28-6.06 3.28-4.13 0-7.22-3.25-7.22-7.39 0-4.4 3.11-7.39 6.86-7.39 3.78 0 5.62 3.05 6.23 4.7l.31.85-9.71 4.08c.74 1.48 1.9 2.24 3.53 2.24s2.76-.82 3.58-2.05zm-7.63-2.66l6.5-2.74c-.36-.92-1.43-1.57-2.7-1.57-1.62 0-3.88 1.46-3.8 4.31z">
                                            </path>
                                        </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                            xmlns="http://www.w3.org/2000/svg" class="h-5 sm:h-8 md:h-10"
                                            viewBox="0 0 210 59">
                                            <path fill="#B8BCC2" fill-rule="evenodd" clip-rule="evenodd"
                                                d="M206.622 31.928C207.065 31.4116 207.85 31.4352 208.253 31.986H208.25L209.784 34.0834C210.075 34.4864 210.073 35.0425 209.769 35.4349C207.106 38.8893 202.44 42.2143 196.81 42.5359C192.366 42.7887 188.701 41.1051 186.706 37.9221C186.311 37.2925 185.44 37.2557 184.997 37.8511C182.63 41.0286 179.766 43.5134 176.782 43.6845C171.467 43.9876 169.966 40.4228 171.28 32.563C171.412 31.7805 170.726 31.1192 169.987 31.3141C168.885 31.6065 167.715 31.7356 166.528 31.633C166.034 31.5907 165.571 31.8912 165.422 32.3811C163.455 38.8418 158.774 44.8518 152.715 45.1997C148.847 45.421 143.069 43.205 143.647 33.9462C143.695 33.1927 143.019 32.5999 142.323 32.8106C141.11 33.1795 139.804 33.3534 138.474 33.2401C137.981 33.1979 137.52 33.4983 137.371 33.9885C135.404 40.449 130.723 46.4592 124.664 46.8068C120.796 47.0282 115.018 44.8124 115.596 35.5536C115.644 34.7998 114.968 34.207 114.272 34.418C113.059 34.7869 111.753 34.9634 110.423 34.8473C109.93 34.8053 109.469 35.1057 109.32 35.5956C107.352 42.0564 102.672 48.0664 96.6132 48.4142C93.8613 48.5723 90.1398 47.4945 88.4308 43.5264C88.1016 42.7599 87.1144 42.6438 86.6257 43.3105C84.2334 46.5751 81.3193 49.152 78.2762 49.3259C75.1571 49.505 73.4509 48.2535 72.7091 46.0216C72.4458 45.2339 71.4609 45.0467 70.9293 45.6712C68.8002 48.1744 66.3749 50.0082 63.9216 50.1479C60.1393 50.3666 57.9619 47.563 57.7823 44.1667C57.5747 40.204 59.2887 35.564 61.2025 30.4999C61.4684 29.7964 60.9873 29.0348 60.2608 29.0032C59.157 28.956 57.8963 28.8399 56.7113 28.6185C56.1771 28.5159 55.6583 28.8479 55.5063 29.3907C53.243 37.4716 49.7771 45.392 46.8529 50.074C46.5263 50.5984 45.8505 50.7381 45.3593 50.377L43.1264 48.7331C42.6682 48.393 42.5441 47.7397 42.8504 47.247C47.0759 40.478 50.8278 29.8807 52.1215 22.0421C52.2025 21.5415 52.61 21.17 53.0986 21.141L56.0683 20.9697C56.7493 20.9302 57.2861 21.5652 57.162 22.2634L57.1493 22.3372C57.0379 22.959 57.4532 23.5439 58.0532 23.6257C60.7164 23.992 64.6963 24.0366 67.3975 23.9313C68.157 23.9023 68.6938 24.6875 68.4178 25.4226C66.2507 31.1876 63.3469 39.1765 63.5139 42.3382C63.5899 43.7662 64.2204 44.5462 65.3291 44.4829C67.4508 44.3619 70.7141 40.0959 73.1876 35.3455C73.2331 35.261 73.2659 35.169 73.2862 35.0741C74.1196 31.3543 75.3565 27.2068 76.6061 23.0163L76.6837 22.7561C76.8128 22.3188 77.1901 22.0131 77.6306 21.9868L81.1876 21.7839C81.9219 21.7417 82.4712 22.4795 82.2485 23.2093C82.0654 23.8112 81.883 24.409 81.7023 25.0014C78.5723 35.2603 75.9438 43.8759 79.4838 43.6742C81.7978 43.5422 85.0764 39.6164 87.8966 34.0279C87.9421 33.9356 87.9751 33.8381 87.9954 33.7355C88.1372 33.0055 88.3092 32.2416 88.5195 31.4432C90.1639 24.8753 92.0286 18.3691 93.8955 11.855C94.4717 9.8446 95.0481 7.83341 95.6182 5.81945C95.7449 5.37417 96.1245 5.06062 96.57 5.03426L100.221 4.82611C100.963 4.78396 101.512 5.52962 101.279 6.26474C99.8208 10.8388 98.2967 15.7106 96.8487 20.4006C96.5448 21.3887 97.603 22.2107 98.4386 21.6416C99.8791 20.6562 101.545 20.0027 103.158 19.9105C107.267 19.676 110.064 23.0565 110.332 28.1496C110.347 28.4184 110.363 28.7082 110.37 29.0032C110.385 29.5673 110.808 30.023 111.348 30.0704C113.282 30.2417 115.259 29.6673 116.786 28.3051C116.943 28.1654 117.049 27.9757 117.102 27.7701C118.616 21.8916 120.287 16.0568 121.959 10.2147C122.532 8.21455 123.105 6.21353 123.672 4.20956C123.798 3.76427 124.178 3.45072 124.624 3.42438L128.274 3.21623C129.016 3.17408 129.566 3.91972 129.333 4.65484C127.874 9.22892 126.35 14.1007 124.902 18.7907C124.598 19.7788 125.657 20.6008 126.492 20.0317C127.933 19.0463 129.599 18.3929 131.211 18.3006C135.32 18.0662 138.117 21.4466 138.386 26.5399C138.401 26.8084 138.416 27.0985 138.424 27.3935C138.436 27.9573 138.862 28.4132 139.401 28.4607C141.335 28.6318 143.312 28.0573 144.839 26.6951C144.996 26.5557 145.102 26.3659 145.156 26.1604C146.67 20.2818 148.34 14.4471 150.013 8.6051C150.586 6.60484 151.158 4.60372 151.725 2.59968C151.852 2.15439 152.232 1.84085 152.677 1.8145L156.328 1.60635C157.07 1.56419 157.619 2.30985 157.386 3.04497C155.928 7.61902 154.404 12.4908 152.956 17.1808C152.652 18.1689 153.71 18.991 154.546 18.4219C155.986 17.4364 157.652 16.783 159.265 16.6908C163.374 16.4563 166.171 19.8367 166.44 24.9299C166.455 25.2013 166.47 25.4885 166.477 25.7835C166.493 26.3447 166.913 26.8032 167.452 26.8507C169.323 27.0166 171.237 26.4844 172.741 25.2171C172.908 25.0774 173.024 24.8798 173.08 24.6637C174.804 18.0187 177.336 9.31324 179.777 0.981894C179.906 0.541877 180.285 0.236236 180.726 0.209888L184.344 0.0017367C185.078 -0.0404207 185.627 0.692063 185.407 1.42191C182.047 12.5778 179.308 22.3372 177.797 28.0944C175.789 35.9039 175.711 38.1567 177.994 38.025C179.911 37.9143 182.493 35.1952 184.928 31.0847C185.025 30.924 185.075 30.7397 185.083 30.5526C185.402 22.324 190.447 14.8385 197.946 14.409C202.969 14.1218 205.721 17.916 205.918 21.6495C206.293 28.7767 199.248 33.3324 192.42 32.9107C191.625 32.8606 191.047 33.7145 191.397 34.4574C192.351 36.4967 194.359 37.6352 197.787 37.4374C201.048 37.2531 204.468 34.439 206.622 31.928ZM93.7548 33.9278C92.1345 40.4228 94.1017 42.9652 96.646 42.8203C100.823 42.5805 104.864 34.9263 104.553 29.019C104.416 26.4396 102.907 25.0958 101.145 25.1961C98.2106 25.3646 95.0512 28.745 93.7548 33.9278ZM121.808 32.3207C120.188 38.8154 122.155 41.3581 124.7 41.2131H124.697C128.874 40.9734 132.917 33.3192 132.606 27.4119C132.472 24.8324 130.96 23.4886 129.198 23.5887C126.264 23.7574 123.105 27.1379 121.808 32.3207ZM149.862 30.7133C148.242 37.2082 150.209 39.7509 152.753 39.606H152.751C156.925 39.3662 160.971 31.712 160.66 25.8047C160.525 23.2251 159.014 21.8814 157.252 21.9815C154.318 22.1501 151.158 25.5307 149.862 30.7133ZM200.584 22.2239C200.559 20.5218 199.513 19.2887 197.817 19.3862H197.815C194.483 19.5785 191.875 23.1856 191.045 27.562C190.913 28.2577 191.422 28.9058 192.103 28.8899C196.407 28.7821 200.721 25.9416 200.584 22.2239ZM44.3525 25.3837C43.9171 12.1962 35.3423 3.49339 22.6712 3.94658C17.2307 4.19426 11.0052 6.25733 6.32164 9.9461C5.88113 10.2939 5.76719 10.9315 6.06593 11.4163L8.05331 14.6519C8.39254 15.2052 9.11407 15.3185 9.60776 14.9075C13.1724 11.9459 18.0383 10.0041 22.7193 9.79855C31.403 9.43757 37.7828 14.9971 38.1551 25.7367C38.6209 38.2417 30.2157 52.5461 16.7091 53.3207C16.2382 53.3471 15.7471 53.3577 15.2559 53.3577C14.5673 53.3577 14.0585 52.6858 14.2306 51.9901C16.8357 41.4744 19.8763 30.1974 22.9776 19.7029C23.1928 18.973 22.6459 18.2458 21.9143 18.288L17.9648 18.5146C17.5218 18.5409 17.142 18.8492 17.0129 19.2918C14.0331 29.6045 11.0508 40.7895 8.36723 51.284C8.21279 51.89 7.59761 52.2379 7.02544 52.0427C5.62543 51.566 4.34693 51.0232 3.2583 50.3881C2.73677 50.0825 2.07601 50.2987 1.80765 50.8571L0.11142 54.4037C-0.139216 54.9281 0.0455967 55.5709 0.539275 55.8527C4.38489 58.0345 10.223 59.2806 16.0914 58.9462C35.4032 57.8393 44.864 40.0015 44.3525 25.3889V25.3837ZM82.3044 9.18082C79.955 9.31518 77.8713 11.9553 78.0183 14.7377C78.1143 16.5715 79.2917 17.7967 81.1195 17.694C83.4689 17.5596 85.6106 14.7798 85.4714 12.1318C85.3754 10.298 84.0005 9.08333 82.3044 9.18082Z">
                                            </path>
                                        </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 568.291 144"
                                            class="h-5 sm:h-8 md:h-10">
                                            <g>
                                                <g>
                                                    <g>
                                                        <g>
                                                            <g>
                                                                <polygon fill="#231F20"
                                                                    points="0,21.297 22.215,21.297 22.215,102.206 63.342,102.206 63.342,122.703 0,122.703      ">
                                                                </polygon>
                                                                <g>
                                                                    <path fill="#231F20"
                                                                        d="M73.154,53.955h21.352v68.721H73.154V53.955z M83.83,19.794c6.837,0,12.386,5.549,12.386,12.38        c0,6.843-5.549,12.376-12.386,12.376c-6.829,0-12.378-5.534-12.378-12.376C71.451,25.344,77,19.794,83.83,19.794">
                                                                    </path>
                                                                </g>
                                                                <polygon fill="#231F20"
                                                                    points="183.755,21.29 205.099,21.29 205.099,81.897 229.277,53.924 255.444,53.924 227.442,85.742        254.859,122.703 228.029,122.703 205.376,88.732 205.099,88.732 205.099,122.703 183.755,122.703      ">
                                                                </polygon>
                                                                <path fill="#231F20"
                                                                    d="M106.314,53.964h20.495v9.394h0.286c2.844-5.409,9.82-11.1,20.209-11.1       c21.638,0,25.622,14.232,25.622,32.731v37.687h-21.351V89.263c0-7.974-0.146-18.217-11.107-18.217       c-11.1,0-12.802,8.684-12.802,17.647v33.983h-21.351V53.964z">
                                                                </path>
                                                                <path fill="#231F20"
                                                                    d="M300.386,80.326c0.14-7.117-5.411-13.096-13.104-13.096c-9.389,0-14.517,6.405-15.08,13.096H300.386z        M318.457,110.778c-6.833,8.684-18.071,13.671-29.179,13.671c-21.346,0-38.427-14.241-38.427-36.302       c0-22.058,17.082-36.29,38.427-36.29c19.942,0,32.456,14.231,32.456,36.29v6.69h-49.531c1.695,8.114,7.825,13.38,16.082,13.38       c6.977,0,11.67-3.56,15.229-8.399L318.457,110.778z">
                                                                </path>
                                                                <path fill="#231F20"
                                                                    d="M366.199,70.573c-10.667,0-17.07,7.122-17.07,17.503c0,10.399,6.403,17.507,17.07,17.507       c10.683,0,17.09-7.107,17.09-17.507C383.289,77.694,376.882,70.573,366.199,70.573 M402.936,122.665h-19.646v-9.112h-0.277       c-3.284,4.98-11.532,10.819-21.214,10.819c-20.502,0-34.021-14.797-34.021-35.723c0-19.217,11.955-36.866,31.596-36.866       c8.833,0,17.086,2.417,21.927,9.108h0.277V21.29h21.359V122.665z">
                                                                </path>
                                                            </g>
                                                            <path fill="#0077B5"
                                                                d="M557.632,0H434.916c-5.864,0-10.629,4.648-10.629,10.376V133.61c0,5.734,4.765,10.39,10.629,10.39      h122.716c5.874,0,10.659-4.655,10.659-10.39V10.376C568.291,4.648,563.506,0,557.632,0z">
                                                            </path>
                                                            <path fill="#FFFFFF"
                                                                d="M445.638,53.985h21.359v68.722h-21.359V53.985z M456.322,19.825c6.828,0,12.377,5.549,12.377,12.38      c0,6.837-5.549,12.386-12.377,12.386c-6.846,0-12.387-5.549-12.387-12.386C443.936,25.375,449.477,19.825,456.322,19.825">
                                                            </path>
                                                            <path fill="#FFFFFF"
                                                                d="M480.394,53.985h20.485v9.39h0.286c2.852-5.403,9.818-11.099,20.209-11.099      c21.628,0,25.621,14.234,25.621,32.736v37.694h-21.344v-33.42c0-7.969-0.146-18.22-11.099-18.22      c-11.113,0-12.819,8.681-12.819,17.644v33.996h-21.34V53.985z">
                                                            </path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                            class="w-10 sm:w-20 md:w-30 aspect-square" viewBox="0 0 650 80">
                                            <path
                                                d="M 402.46213,152.88634 C 364.76308,180.67353 310.1206,195.499 263.07446,195.499 c -65.9663,0 -125.35289,-24.39849 -170.281138,-64.97793 -3.529868,-3.19103 -0.367119,-7.53982 3.868745,-5.0548 48.486343,28.21077 108.437743,45.18241 170.365863,45.18241 41.76544,0 87.71029,-8.64115 129.95583,-26.5729 6.38201,-2.71096 11.71918,4.17937 5.47837,8.81056"
                                                fill="#B8BCC2"></path>
                                            <path
                                                d="m 418.13478,134.95459 c -4.80065,-6.1561 -31.8536,-2.90862 -43.99636,-1.46843 -3.69932,0.45183 -4.26408,-2.76742 -0.9319,-5.08303 21.54636,-15.16434 56.90159,-10.78729 61.02448,-5.70428 4.12288,5.11127 -1.07308,40.55122 -21.32045,57.46636 -3.10629,2.59798 -6.07138,1.21429 -4.68766,-2.23088 4.54647,-11.35208 14.74076,-36.7954 9.91189,-42.97974"
                                                fill="#B8BCC2"></path>
                                            <path
                                                d="m 374.98559,21.349107 0,-14.740752 c 0,-2.2308798 1.69434,-3.7275435 3.72755,-3.7275435 l 65.99451,0 c 2.11794,0 3.81228,1.5249061 3.81228,3.7275435 l 0,12.622831 c -0.0282,2.117921 -1.8073,4.885342 -4.97008,9.262385 l -34.19741,48.825227 c 12.70756,-0.310612 26.12108,1.58138 37.64259,8.076355 2.59798,1.468422 3.30396,3.614585 3.50163,5.732527 l 0,15.72912 c 0,2.14616 -2.37208,4.65941 -4.85711,3.36043 -20.30384,-10.646108 -47.27208,-11.803906 -69.72208,0.11296 -2.28735,1.24252 -4.68766,-1.24251 -4.68766,-3.38868 l 0,-14.938423 c 0,-2.400313 0.0282,-6.494986 2.42853,-10.137813 l 39.61932,-56.816855 -34.47981,0 c -2.11792,0 -3.81226,-1.496653 -3.81226,-3.699312"
                                                fill="#B8BCC2"></path>
                                            <path
                                                d="m 134.24816,113.32351 -20.07792,0 c -1.92024,-0.14118 -3.44515,-1.58137 -3.58635,-3.41692 l 0,-103.0440642 c 0,-2.061447 1.72258,-3.6993227 3.86874,-3.6993227 l 18.72245,0 c 1.94851,0.084663 3.50163,1.5813802 3.64284,3.4451519 l 0,13.470017 0.36711,0 c 4.88534,-13.0181926 14.063,-19.08957463 26.43169,-19.08957463 12.56635,0 20.41679,6.07138203 26.06459,19.08957463 4.85711,-13.0181926 15.89855,-19.08957463 27.7307,-19.08957463 8.41522,0 17.62113,3.47339423 23.24069,11.26735763 6.35377,8.66937 5.05477,21.263959 5.05477,32.30542 l -0.0282,65.034405 c 0,2.06144 -1.72258,3.72753 -3.86874,3.72753 l -20.0497,0 c -2.00495,-0.14118 -3.61457,-1.7508 -3.61457,-3.72753 l 0,-54.614222 c 0,-4.348801 0.39532,-15.192576 -0.56479,-19.315481 -1.49666,-6.918535 -5.98666,-8.867045 -11.80391,-8.867045 -4.85711,0 -9.94012,3.247487 -12.00157,8.443474 -2.06145,5.195975 -1.86377,13.893566 -1.86377,19.739052 l 0,54.614222 c 0,2.06144 -1.72258,3.72753 -3.86875,3.72753 l -20.04967,0 c -2.03321,-0.14118 -3.6146,-1.7508 -3.6146,-3.72753 l -0.0282,-54.614222 c 0,-11.493265 1.89199,-28.408433 -12.36868,-28.408433 -14.43014,0 -13.86536,16.491565 -13.86536,28.408433 l 0,54.614222 c 0,2.06144 -1.72258,3.72753 -3.86874,3.72753"
                                                fill="#B8BCC2"></path>
                                            <path
                                                d="m 505.33679,0.98879737 c 29.79214,0 45.91659,25.58452763 45.91659,58.11585463 0,31.429992 -17.81882,56.365018 -45.91659,56.365018 -29.25561,0 -45.18241,-25.584526 -45.18241,-57.466342 0,-32.079503 16.12447,-57.01453063 45.18241,-57.01453063 M 505.50621,22.02686 c -14.79722,0 -15.72912,20.162623 -15.72912,32.728991 0,12.594589 -0.19767,39.478105 15.5597,39.478105 15.55968,0 16.2939,-21.68753 16.2939,-34.903408 0,-8.697591 -0.36711,-19.089542 -2.99335,-27.335341 -2.25912,-7.172684 -6.7491,-9.968347 -13.13113,-9.968347"
                                                fill="#B8BCC2"></path>
                                            <path
                                                d="m 589.88433,113.32351 -19.9932,0 c -2.00498,-0.14118 -3.6146,-1.7508 -3.6146,-3.72753 L 566.2483,6.5236386 c 0.16944,-1.8920141 1.83555,-3.3604355 3.86875,-3.3604355 l 18.60951,0 c 1.7508,0.084663 3.19099,1.2707569 3.58635,2.8803795 l 0,15.7573484 0.36709,0 c 5.61958,-14.0912519 13.49823,-20.81213363 27.36358,-20.81213363 9.00824,0 17.79057,3.24748743 23.43837,12.14276463 5.25245,8.245787 5.25245,22.111144 5.25245,32.079491 l 0,64.864987 c -0.2259,1.80728 -1.89201,3.24747 -3.86872,3.24747 l -20.13441,0 c -1.83555,-0.14118 -3.36046,-1.49665 -3.55811,-3.24747 l 0,-55.969688 c 0,-11.267358 1.29899,-27.758945 -12.56637,-27.758945 -4.88533,0 -9.37534,3.275741 -11.60622,8.245788 -2.82389,6.297311 -3.191,12.566368 -3.191,19.513157 l 0,55.489628 c -0.0282,2.06144 -1.77905,3.72753 -3.92521,3.72753"
                                                fill="#B8BCC2"></path>
                                            <path
                                                d="m 322.37634,64.102952 c 0,7.822195 0.19767,14.345402 -3.75579,21.292201 -3.191,5.647811 -8.27402,9.121205 -13.89357,9.121205 -7.70925,0 -12.22749,-5.873728 -12.22749,-14.543098 0,-17.112822 15.33377,-20.219108 29.87685,-20.219108 l 0,4.3488 m 20.24736,48.938168 c -1.32722,1.18604 -3.24749,1.27075 -4.74415,0.48007 -6.66441,-5.53484 -7.87868,-8.10458 -11.52151,-13.38527 -11.01321,11.23911 -18.83542,14.59954 -33.09611,14.59954 -16.91516,0 -30.04628,-10.42019 -30.04628,-31.288784 0,-16.293911 8.81058,-27.391836 21.40517,-32.813719 10.90024,-4.800646 26.12104,-5.6478 37.75552,-6.975031 l 0,-2.597987 c 0,-4.772405 0.36711,-10.420205 -2.45679,-14.543077 -2.42855,-3.699323 -7.11624,-5.224229 -11.26736,-5.224229 -7.65278,0 -14.45836,3.925219 -16.12447,12.058048 -0.33887,1.807308 -1.66609,3.586364 -3.50164,3.67108 l -19.45666,-2.0897 c -1.63787,-0.367087 -3.47339,-1.694339 -2.99333,-4.207589 4.46176,-23.607797 25.81045,-30.72403991022 44.89999,-30.72403991022 9.7707,0 22.53472,2.59798781022 30.24397,9.99660081022 9.77068,9.1211941 8.8388,21.2922011 8.8388,34.5363001 l 0,31.288801 c 0,9.403597 3.89698,13.52647 7.56805,18.609508 1.27077,1.807276 1.55316,3.981682 -0.0848,5.337155 -4.09465,3.416933 -11.38032,9.770703 -15.39025,13.328813 l -0.0282,-0.0566"
                                                id="path28" fill="#B8BCC2" fill-rule="evenodd" stroke="none"
                                                inkscape:connector-curvature="0"></path>
                                            <path
                                                d="m 59.160693,64.102952 c 0,7.822195 0.197653,14.345402 -3.755787,21.292201 -3.191024,5.647811 -8.245799,9.121205 -13.893599,9.121205 -7.709247,0 -12.199238,-5.873728 -12.199238,-14.543098 0,-17.112822 15.333767,-20.219108 29.848624,-20.219108 l 0,4.3488 m 20.247351,48.938168 c -1.327231,1.18604 -3.247499,1.27075 -4.744162,0.48007 -6.664387,-5.53484 -7.850438,-8.10458 -11.521507,-13.38527 -11.01321,11.23911 -18.807152,14.59954 -33.096101,14.59954 C 13.159348,114.73546 -2.141151e-6,104.31527 -2.141151e-6,83.446676 -2.141151e-6,67.152765 8.8388006,56.05484 21.405147,50.632957 c 10.900249,-4.800646 26.121068,-5.6478 37.755546,-6.975031 l 0,-2.597987 c 0,-4.772405 0.367108,-10.420205 -2.428555,-14.543077 -2.456809,-3.699323 -7.144465,-5.224229 -11.26737,-5.224229 -7.652751,0 -14.486602,3.925219 -16.152699,12.058048 -0.338876,1.807308 -1.666107,3.586364 -3.473394,3.67108 L 6.3537716,34.932061 C 4.7159066,34.564974 2.9086089,33.237722 3.3604333,30.724472 7.8504353,7.116675 29.170868,4.3208978e-4 48.260442,4.3208978e-4 c 9.770674,0 22.534695,2.59798781022 30.243942,9.99660081022 9.770705,9.1211941 8.838803,21.2922011 8.838803,34.5363001 l 0,31.288801 c 0,9.403597 3.896998,13.52647 7.568067,18.609508 1.298989,1.807276 1.58138,3.981682 -0.05648,5.337155 -4.094651,3.416933 -11.380305,9.770703 -15.390262,13.328813 l -0.05646,-0.0566"
                                                id="path30" fill="#B8BCC2" fill-rule="evenodd" stroke="none"
                                                inkscape:connector-curvature="0"></path>
                                        </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                            xmlns="http://www.w3.org/2000/svg" class="h-3 sm:h-5 md:h-7"
                                            viewBox="0 0 719 160">
                                            <path fill="#B8BCC2"
                                                d="m174.104 9.734.215-.047V8.02H130.39L89.6 103.89 48.81 8.021H1.472v1.666l.212.047c8.018 1.81 12.09 4.509 12.09 14.242V137.93c0 9.734-4.087 12.433-12.106 14.243l-.212.047v1.671h32.118v-1.665l-.213-.048c-8.018-1.809-12.089-4.509-12.089-14.242V30.586l52.399 123.305h2.972l53.925-126.743V140.75c-.687 7.688-4.721 10.062-11.982 11.701l-.215.05v1.652h55.948v-1.652l-.215-.05c-7.269-1.639-11.4-4.013-12.087-11.701l-.037-116.774h.037c0-9.733 4.071-12.432 12.087-14.242m25.555 75.488c.915-20.474 8.268-35.252 20.606-35.507 3.806.063 6.998 1.312 9.479 3.714 5.272 5.118 7.751 15.812 7.368 31.793zm-.553 5.77h65.573v-.275c-.186-15.656-4.721-27.834-13.466-36.196-7.559-7.227-18.751-11.203-30.507-11.203h-.263c-6.101 0-13.584 1.48-18.909 4.16-6.061 2.807-11.407 7.003-15.855 12.511-7.161 8.874-11.499 20.866-12.554 34.343q-.05.606-.092 1.212a50 50 0 0 0-.065 1.151 85.807 85.807 0 0 0-.094 5.689c.71 30.524 17.198 54.917 46.483 54.917 25.705 0 40.675-18.791 44.407-44.013l-1.886-.664c-6.557 13.556-18.334 21.771-31.738 20.769-18.297-1.369-32.314-19.922-31.042-42.395m139.722 41.359c-2.151 5.101-6.639 7.908-12.653 7.908s-11.513-4.129-15.418-11.63c-4.197-8.053-6.405-19.436-6.405-32.92 0-28.067 8.729-46.22 22.24-46.22 5.657 0 10.111 2.807 12.236 7.704zm43.499 20.008c-8.019-1.897-12.089-4.722-12.089-14.951V1.309l-48.716 14.353v1.757l.299-.024c6.72-.543 11.278.386 13.925 2.83 2.072 1.915 3.082 4.853 3.082 8.987v18.66c-4.803-3.067-10.516-4.56-17.448-4.56-14.059 0-26.909 5.92-36.176 16.672-9.66 11.205-14.767 26.518-14.767 44.278-.003 31.72 15.612 53.039 38.851 53.039 13.595 0 24.533-7.449 29.54-20.013v16.865h43.711v-1.746zM424.1 19.819c0-9.904-7.468-17.374-17.375-17.374-9.859 0-17.573 7.632-17.573 17.374s7.721 17.374 17.573 17.374c9.907 0 17.375-7.47 17.375-17.374m11.499 132.546c-8.019-1.897-12.089-4.722-12.089-14.951h-.035V43.635l-43.714 12.551v1.705l.263.024c9.458.842 12.047 4.1 12.047 15.152v81.086h43.751v-1.746zm112.013 0c-8.018-1.897-12.089-4.722-12.089-14.951V43.635l-41.621 12.137v1.71l.246.026c7.733.813 9.967 4.257 9.967 15.36v59.279c-2.578 5.102-7.415 8.131-13.274 8.336-9.503 0-14.736-6.419-14.736-18.073V43.638l-43.714 12.55v1.703l.262.024c9.459.84 12.05 4.097 12.05 15.152v50.17a56.3 56.3 0 0 0 .91 10.444l.787 3.423c3.701 13.262 13.398 20.197 28.59 20.197 12.868 0 24.147-7.966 29.115-20.43v17.311h43.714v-1.747zm169.818 1.788v-1.749l-.213-.05c-8.7-2.006-12.089-5.789-12.089-13.49v-63.79c0-19.89-11.171-31.761-29.883-31.761-13.64 0-25.141 7.882-29.569 20.16-3.517-13.01-13.639-20.16-28.606-20.16-13.146 0-23.449 6.938-27.869 18.657V43.643L545.487 55.68v1.715l.263.024c9.345.829 12.047 4.181 12.047 14.95v81.784h40.787v-1.746l-.215-.053c-6.941-1.631-9.181-4.606-9.181-12.239V66.998c1.836-4.289 5.537-9.37 12.853-9.37 9.086 0 13.692 6.296 13.692 18.697v77.828h40.797v-1.746l-.215-.053c-6.94-1.631-9.18-4.606-9.18-12.239V75.066a42 42 0 0 0-.578-7.26c1.947-4.661 5.86-10.177 13.475-10.177 9.214 0 13.691 6.114 13.691 18.696v77.828z">
                                            </path>
                                        </svg></span><span class="ps-5 sm:ps-10 md:ps-20"><svg
                                            xmlns="http://www.w3.org/2000/svg" class="h-5 sm:h-8 md:h-10"
                                            viewBox="0 0 559 168">
                                            <path fill="#B8BCC2"
                                                d="m83.996 0.277c-46.249 0-83.743 37.493-83.743 83.742 0 46.251 37.494 83.741 83.743 83.741 46.254 0 83.744-37.49 83.744-83.741 0-46.246-37.49-83.738-83.745-83.738l0.001-0.004zm38.404 120.78c-1.5 2.46-4.72 3.24-7.18 1.73-19.662-12.01-44.414-14.73-73.564-8.07-2.809 0.64-5.609-1.12-6.249-3.93-0.643-2.81 1.11-5.61 3.926-6.25 31.9-7.288 59.263-4.15 81.337 9.34 2.46 1.51 3.24 4.72 1.73 7.18zm10.25-22.802c-1.89 3.072-5.91 4.042-8.98 2.152-22.51-13.836-56.823-17.843-83.448-9.761-3.453 1.043-7.1-0.903-8.148-4.35-1.04-3.453 0.907-7.093 4.354-8.143 30.413-9.228 68.222-4.758 94.072 11.127 3.07 1.89 4.04 5.91 2.15 8.976v-0.001zm0.88-23.744c-26.99-16.031-71.52-17.505-97.289-9.684-4.138 1.255-8.514-1.081-9.768-5.219-1.254-4.14 1.08-8.513 5.221-9.771 29.581-8.98 78.756-7.245 109.83 11.202 3.73 2.209 4.95 7.016 2.74 10.733-2.2 3.722-7.02 4.949-10.73 2.739zm94.56 3.072c-14.46-3.448-17.03-5.868-17.03-10.953 0-4.804 4.52-8.037 11.25-8.037 6.52 0 12.98 2.455 19.76 7.509 0.2 0.153 0.46 0.214 0.71 0.174 0.26-0.038 0.48-0.177 0.63-0.386l7.06-9.952c0.29-0.41 0.21-0.975-0.18-1.288-8.07-6.473-17.15-9.62-27.77-9.62-15.61 0-26.52 9.369-26.52 22.774 0 14.375 9.41 19.465 25.67 23.394 13.83 3.187 16.17 5.857 16.17 10.629 0 5.29-4.72 8.58-12.32 8.58-8.44 0-15.33-2.85-23.03-9.51-0.19-0.17-0.45-0.24-0.69-0.23-0.26 0.02-0.49 0.14-0.65 0.33l-7.92 9.42c-0.33 0.4-0.29 0.98 0.09 1.32 8.96 8 19.98 12.22 31.88 12.22 16.82 0 27.69-9.19 27.69-23.42 0.03-12.007-7.16-18.657-24.77-22.941l-0.03-0.013zm62.86-14.26c-7.29 0-13.27 2.872-18.21 8.757v-6.624c0-0.523-0.42-0.949-0.94-0.949h-12.95c-0.52 0-0.94 0.426-0.94 0.949v73.601c0 0.52 0.42 0.95 0.94 0.95h12.95c0.52 0 0.94-0.43 0.94-0.95v-23.23c4.94 5.53 10.92 8.24 18.21 8.24 13.55 0 27.27-10.43 27.27-30.369 0.02-19.943-13.7-30.376-27.26-30.376l-0.01 0.001zm12.21 30.375c0 10.149-6.25 17.239-15.21 17.239-8.85 0-15.53-7.41-15.53-17.239 0-9.83 6.68-17.238 15.53-17.238 8.81-0.001 15.21 7.247 15.21 17.237v0.001zm50.21-30.375c-17.45 0-31.12 13.436-31.12 30.592 0 16.972 13.58 30.262 30.91 30.262 17.51 0 31.22-13.39 31.22-30.479 0-17.031-13.62-30.373-31.01-30.373v-0.002zm0 47.714c-9.28 0-16.28-7.46-16.28-17.344 0-9.929 6.76-17.134 16.07-17.134 9.34 0 16.38 7.457 16.38 17.351 0 9.927-6.8 17.127-16.17 17.127zm68.27-46.53h-14.25v-14.566c0-0.522-0.42-0.948-0.94-0.948h-12.95c-0.52 0-0.95 0.426-0.95 0.948v14.566h-6.22c-0.52 0-0.94 0.426-0.94 0.949v11.127c0 0.522 0.42 0.949 0.94 0.949h6.22v28.795c0 11.63 5.79 17.53 17.22 17.53 4.64 0 8.49-0.96 12.12-3.02 0.3-0.16 0.48-0.48 0.48-0.82v-10.6c0-0.32-0.17-0.63-0.45-0.8-0.28-0.18-0.63-0.19-0.92-0.04-2.49 1.25-4.9 1.83-7.6 1.83-4.15 0-6.01-1.89-6.01-6.11v-26.76h14.25c0.52 0 0.94-0.426 0.94-0.949v-11.126c0.02-0.523-0.4-0.949-0.93-0.949l-0.01-0.006zm49.64 0.057v-1.789c0-5.263 2.02-7.61 6.54-7.61 2.7 0 4.87 0.536 7.3 1.346 0.3 0.094 0.61 0.047 0.85-0.132 0.25-0.179 0.39-0.466 0.39-0.77v-10.91c0-0.417-0.26-0.786-0.67-0.909-2.56-0.763-5.84-1.546-10.76-1.546-11.95 0-18.28 6.734-18.28 19.467v2.74h-6.22c-0.52 0-0.95 0.426-0.95 0.948v11.184c0 0.522 0.43 0.949 0.95 0.949h6.22v44.405c0 0.53 0.43 0.95 0.95 0.95h12.94c0.53 0 0.95-0.42 0.95-0.95v-44.402h12.09l18.52 44.402c-2.1 4.66-4.17 5.59-6.99 5.59-2.28 0-4.69-0.68-7.14-2.03-0.23-0.12-0.51-0.14-0.75-0.07-0.25 0.09-0.46 0.27-0.56 0.51l-4.39 9.63c-0.21 0.46-0.03 0.99 0.41 1.23 4.58 2.48 8.71 3.54 13.82 3.54 9.56 0 14.85-4.46 19.5-16.44l22.46-58.037c0.12-0.292 0.08-0.622-0.1-0.881-0.17-0.257-0.46-0.412-0.77-0.412h-13.48c-0.41 0-0.77 0.257-0.9 0.636l-13.81 39.434-15.12-39.46c-0.14-0.367-0.49-0.61-0.88-0.61h-22.12v-0.003zm-28.78-0.057h-12.95c-0.52 0-0.95 0.426-0.95 0.949v56.481c0 0.53 0.43 0.95 0.95 0.95h12.95c0.52 0 0.95-0.42 0.95-0.95v-56.477c0-0.523-0.42-0.949-0.95-0.949v-0.004zm-6.4-25.719c-5.13 0-9.29 4.152-9.29 9.281 0 5.132 4.16 9.289 9.29 9.289s9.28-4.157 9.28-9.289c0-5.128-4.16-9.281-9.28-9.281zm113.42 43.88c-5.12 0-9.11-4.115-9.11-9.112s4.04-9.159 9.16-9.159 9.11 4.114 9.11 9.107c0 4.997-4.04 9.164-9.16 9.164zm0.05-17.365c-4.67 0-8.2 3.71-8.2 8.253 0 4.541 3.51 8.201 8.15 8.201 4.67 0 8.2-3.707 8.2-8.253 0-4.541-3.51-8.201-8.15-8.201zm2.02 9.138l2.58 3.608h-2.18l-2.32-3.31h-1.99v3.31h-1.82v-9.564h4.26c2.23 0 3.69 1.137 3.69 3.051 0.01 1.568-0.9 2.526-2.21 2.905h-0.01zm-1.54-4.315h-2.37v3.025h2.37c1.18 0 1.89-0.579 1.89-1.514 0-0.984-0.71-1.511-1.89-1.511z">
                                            </path>
                                        </svg></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex mx-auto justify-center px-2 max-w-218 pb-10 md:pb-25">
                    <div class="w-full h-full cursor-grab">
                        <p class="section-title mb-6 text-center">Testimonial</p>
                        <div class="swiper swiper-initialized swiper-horizontal swiper-backface-hidden"
                            id="testimonialSwiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide swiper-slide-active"
                                    style="width: 856px; margin-right: 30px;">
                                    <div class="flex flex-col h-full">
                                        <div class="text-center mx-auto">
                                            <p
                                                class="text-xs sm:text-[14px] md:text-[18px] pb-5 sm:pb-10 md:pb-14 max-w-144.25 text-soft-dark">
                                                Working with this team was a fantastic experience. Their attention to
                                                detail and commitment to quality exceeded our expectations.</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[14px] sm:text-lg font-medium pb-6">“From the initial
                                                consultation to the final delivery, every step was handled
                                                professionally. The end result was a product that not only met our needs
                                                but also impressed our stakeholders. Highly recommended!”</p>
                                            <div class="mx-auto">
                                                <p class="text-[14px] sm:text-[16px] md:text-lg font-medium mb-2">
                                                    Esther Howard</p>
                                                <p class="text-[14px] md:text-[16px] font-light">Managing Director, ABC
                                                    Company</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide swiper-slide-next" style="width: 856px; margin-right: 30px;">
                                    <div class="flex flex-col h-full">
                                        <div class="text-center mx-auto">
                                            <p
                                                class="text-xs sm:text-[14px] md:text-[18px] pb-5 sm:pb-10 md:pb-14 max-w-144.25 text-soft-dark">
                                                Their expertise in UI/UX design helped us transform our digital presence
                                                and improve user engagement.</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[14px] sm:text-lg font-medium pb-6">“The team demonstrated a
                                                deep understanding of our requirements and delivered a solution that was
                                                both visually appealing and highly functional. Communication was clear
                                                throughout the project.”</p>
                                            <div class="mx-auto">
                                                <p class="text-[14px] sm:text-[16px] md:text-lg font-medium mb-2">Ali
                                                    Haider</p>
                                                <p class="text-[14px] md:text-[16px] font-light">COO, XYZ Company</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide" style="width: 856px; margin-right: 30px;">
                                    <div class="flex flex-col h-full">
                                        <div class="text-center mx-auto">
                                            <p
                                                class="text-xs sm:text-[14px] md:text-[18px] pb-5 sm:pb-10 md:pb-14 max-w-144.25 text-soft-dark">
                                                Professional, reliable, and creative—everything you want in a
                                                development partner.</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[14px] sm:text-lg font-medium pb-6">“They delivered our
                                                project on time and went above and beyond to ensure our satisfaction.
                                                The new features have made a significant difference for our users. We
                                                look forward to working together again.”</p>
                                            <div class="mx-auto">
                                                <p class="text-[14px] sm:text-[16px] md:text-lg font-medium mb-2">Elon
                                                    Max</p>
                                                <p class="text-[14px] md:text-[16px] font-light">Managing Director, KFC
                                                    Company</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal">
                                <span class="swiper-pagination-bullet swiper-pagination-bullet-active"></span><span
                                    class="swiper-pagination-bullet"></span><span
                                    class="swiper-pagination-bullet"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative -bottom-15 -mt-15 z-10 px-2">
                    <div class="content p-4 md:p-10 lg:p-22 bg-white rounded-2xl shadow-[0px_0px_90px_9px_rgba(0,_0,_0,_0.1)]"
                        id="contact">
                        <div class="flex flex-col-reverse lg:gap-5 xl:gap-25.75 lg:flex-row justify-between">
                            <div>
                                <div>
                                    <p class="text-[35px] max-lg:hidden font-semibold text-nowrap text-[#132238]">Let’s
                                        discuss your Project</p>
                                    <p
                                        class="text-[12px] xs:text-[14px] sm:text-lg md:text-lg max-lg:text-center pt-4 font-normal text-soft-dark">
                                        I'm available for freelance work. Drop me a line if you have a project you think
                                        I'd be a good fit for.</p>
                                </div>
                                <div class="my-8.75 sm:max-lg:flex justify-between items-center">
                                    <div
                                        class="max-w-84 p-3 md:p-3.75 lg:p-6 flex xs:not-odd:my-3 rounded-[10px] bg-white  hover:scale-[1] duration-450  cursor-pointer hover:shadow-[0px_0px_37px_5px_rgba(0,_0,_0,_0.1)] shadow-gray-200 max-sm:mx-auto">
                                        <div class="h-10 md:h-12 aspect-square bg-[#EDD8FF80] center rounded-[4px]">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                                data-icon="location-dot"
                                                class="svg-inline--fa fa-location-dot text-lg md:text-xl text-picto-primary"
                                                role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 384 512">
                                                <path fill="currentColor"
                                                    d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ms-3.25">
                                            <p class="text-[12px] md:text-[14px] text-[#424E60] font-normal">Address:
                                            </p>
                                            <p class="text-[14px] md:text-[16px] text-[#132238] font-medium">New
                                                Mexico, 31134</p>
                                        </div>
                                    </div>
                                    <div
                                        class="max-w-84 p-3 md:p-3.75 lg:p-6 flex xs:not-odd:my-3 rounded-[10px] bg-white  hover:scale-[1] duration-450  cursor-pointer hover:shadow-[0px_0px_37px_5px_rgba(0,_0,_0,_0.1)] shadow-gray-200 max-sm:mx-auto">
                                        <div class="h-10 md:h-12 aspect-square bg-[#EDD8FF80] center rounded-[4px]">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                                data-icon="envelope"
                                                class="svg-inline--fa fa-envelope text-lg md:text-xl text-picto-primary"
                                                role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512">
                                                <path fill="currentColor"
                                                    d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ms-3.25">
                                            <p class="text-[12px] md:text-[14px] text-[#424E60] font-normal">My Email:
                                            </p>
                                            <p class="text-[14px] md:text-[16px] text-[#132238] font-medium">
                                                mymail@mail.com</p>
                                        </div>
                                    </div>
                                    <div
                                        class="max-w-84 p-3 md:p-3.75 lg:p-6 flex xs:not-odd:my-3 rounded-[10px] bg-white  hover:scale-[1] duration-450  cursor-pointer hover:shadow-[0px_0px_37px_5px_rgba(0,_0,_0,_0.1)] shadow-gray-200 max-sm:mx-auto">
                                        <div class="h-10 md:h-12 aspect-square bg-[#EDD8FF80] center rounded-[4px]">
                                            <svg aria-hidden="true" focusable="false" data-prefix="fas"
                                                data-icon="phone"
                                                class="svg-inline--fa fa-phone text-lg md:text-xl text-picto-primary"
                                                role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512">
                                                <path fill="currentColor"
                                                    d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3L193.3 167c13.7-11.2 18.4-30 11.6-46.3l-40-96z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="ms-3.25">
                                            <p class="text-[12px] md:text-[14px] text-[#424E60] font-normal">Call Me
                                                Now:</p>
                                            <p class="text-[14px] md:text-[16px] text-[#132238] font-medium">00-123
                                                00000</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full max-lg:text-center max-md:mb-4"><a href="#!"
                                        class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                            aria-hidden="true" focusable="false" data-prefix="fab"
                                            data-icon="facebook-f"
                                            class="svg-inline--fa fa-facebook-f text-xl w-4.5 aspect-square"
                                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                            <path fill="currentColor"
                                                d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z">
                                            </path>
                                        </svg></a><a href="#!"
                                        class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                            aria-hidden="true" focusable="false" data-prefix="fab"
                                            data-icon="dribbble"
                                            class="svg-inline--fa fa-dribbble text-xl w-4.5 aspect-square"
                                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path fill="currentColor"
                                                d="M256 8C119.252 8 8 119.252 8 256s111.252 248 248 248 248-111.252 248-248S392.748 8 256 8zm163.97 114.366c29.503 36.046 47.369 81.957 47.835 131.955-6.984-1.477-77.018-15.682-147.502-6.818-5.752-14.041-11.181-26.393-18.617-41.614 78.321-31.977 113.818-77.482 118.284-83.523zM396.421 97.87c-3.81 5.427-35.697 48.286-111.021 76.519-34.712-63.776-73.185-116.168-79.04-124.008 67.176-16.193 137.966 1.27 190.061 47.489zm-230.48-33.25c5.585 7.659 43.438 60.116 78.537 122.509-99.087 26.313-186.36 25.934-195.834 25.809C62.38 147.205 106.678 92.573 165.941 64.62zM44.17 256.323c0-2.166.043-4.322.108-6.473 9.268.19 111.92 1.513 217.706-30.146 6.064 11.868 11.857 23.915 17.174 35.949-76.599 21.575-146.194 83.527-180.531 142.306C64.794 360.405 44.17 310.73 44.17 256.323zm81.807 167.113c22.127-45.233 82.178-103.622 167.579-132.756 29.74 77.283 42.039 142.053 45.189 160.638-68.112 29.013-150.015 21.053-212.768-27.882zm248.38 8.489c-2.171-12.886-13.446-74.897-41.152-151.033 66.38-10.626 124.7 6.768 131.947 9.055-9.442 58.941-43.273 109.844-90.795 141.978z">
                                            </path>
                                        </svg></a><a href="#!"
                                        class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                            aria-hidden="true" focusable="false" data-prefix="fab"
                                            data-icon="instagram"
                                            class="svg-inline--fa fa-instagram text-xl w-4.5 aspect-square"
                                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path fill="currentColor"
                                                d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z">
                                            </path>
                                        </svg></a><a href="#!"
                                        class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                            aria-hidden="true" focusable="false" data-prefix="fab"
                                            data-icon="linkedin"
                                            class="svg-inline--fa fa-linkedin text-xl w-4.5 aspect-square"
                                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                            <path fill="currentColor"
                                                d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z">
                                            </path>
                                        </svg></a><a href="#!"
                                        class="text-picto-primary hover:bg-picto-primary p-2 pt-3 xs:p-2.5 xs:pt-3.75 sm:pt-4 md:pt-5 sm:p-3 md:p-3.75 hover:text-white rounded-md"><svg
                                            aria-hidden="true" focusable="false" data-prefix="fab"
                                            data-icon="behance"
                                            class="svg-inline--fa fa-behance text-xl w-4.5 aspect-square"
                                            role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                            <path fill="currentColor"
                                                d="M232 237.2c31.8-15.2 48.4-38.2 48.4-74 0-70.6-52.6-87.8-113.3-87.8H0v354.4h171.8c64.4 0 124.9-30.9 124.9-102.9 0-44.5-21.1-77.4-64.7-89.7zM77.9 135.9H151c28.1 0 53.4 7.9 53.4 40.5 0 30.1-19.7 42.2-47.5 42.2h-79v-82.7zm83.3 233.7H77.9V272h84.9c34.3 0 56 14.3 56 50.6 0 35.8-25.9 47-57.6 47zm358.5-240.7H376V94h143.7v34.9zM576 305.2c0-75.9-44.4-139.2-124.9-139.2-78.2 0-131.3 58.8-131.3 135.8 0 79.9 50.3 134.7 131.3 134.7 61.3 0 101-27.6 120.1-86.3H509c-6.7 21.9-34.3 33.5-55.7 33.5-41.3 0-63-24.2-63-65.3h185.1c.3-4.2.6-8.7.6-13.2zM390.4 274c2.3-33.7 24.7-54.8 58.5-54.8 35.4 0 53.2 20.8 56.2 54.8H390.4z">
                                            </path>
                                        </svg></a></div>
                            </div>
                            <div class="w-full overflow-y-scroll py-6.5">
                                <p
                                    class="text-xl mb-2 xs:text-2xl sm:text-2xl md:text-[38px] font-semibold text-[#132238] lg:hidden text-center">
                                    Let’s discuss your Project</p>
                                <div>
                                    <p
                                        class="text-[12px] xs:text-[14px] max-lg:text-center sm:text-lg font-normal text-soft-dark">
                                        I'm always open to discussing product design work or partnership opportunities.
                                    </p>
                                    <div class="mx-2">
                                        <form class="flex flex-col gap-4 mt-4"><input placeholder="Name*"
                                                class="input input-lg border-0 border-b-2 focus:outline-none focus:placeholder:text-picto-primary placeholder:text-[15px] md:placeholder:text-lg focus:border-picto-primary border-[#E6E8EB] w-full rounded-none px-0"
                                                required="" type="text"><input placeholder="Email*"
                                                class="input input-lg border-0 border-b-2 focus:outline-none focus:placeholder:text-picto-primary placeholder:text-[15px] md:placeholder:text-lg focus:border-picto-primary border-[#E6E8EB] w-full rounded-none px-0"
                                                required="" type="email"><input placeholder="Location*"
                                                class="input input-lg border-0 border-b-2 focus:outline-none focus:placeholder:text-picto-primary placeholder:text-[15px] md:placeholder:text-lg focus:border-picto-primary border-[#E6E8EB] w-full rounded-none px-0"
                                                required="" type="text">
                                            <div class="flex max-xs:flex-col max-xs:gap-4"><input
                                                    placeholder="Budget*"
                                                    class="input input-lg border-0 border-b-2 focus:outline-none focus:placeholder:text-picto-primary placeholder:text-[15px] md:placeholder:text-lg focus:border-picto-primary border-[#E6E8EB] w-full rounded-none px-0 xs:w-[50%] me-5"
                                                    required="" type="text"><input placeholder="Subject*"
                                                    class="input input-lg border-0 border-b-2 focus:outline-none focus:placeholder:text-picto-primary placeholder:text-[15px] md:placeholder:text-lg focus:border-picto-primary border-[#E6E8EB] w-full rounded-none px-0"
                                                    required="" type="text"></div><input placeholder="Message*"
                                                class="input input-lg border-0 border-b-2 focus:outline-none focus:placeholder:text-picto-primary placeholder:text-[15px] md:placeholder:text-lg focus:border-picto-primary border-[#E6E8EB] w-full rounded-none px-0"
                                                required="" type="text"><button type="submit"
                                                class="btn gap-3 max-lg:mx-auto btn-primary rounded-sm mt-5 text-[13px] md:text-[16px] w-fit font-semibold lg:mt-12.5 p-2 md:px-4">Submit
                                                <svg class="w-4 md:w-6 aspect-square" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M20.34 9.32013L6.34 2.32013C5.78749 2.04514 5.16362 1.94724 4.55344 2.03978C3.94326 2.13232 3.37646 2.4108 2.93033 2.83724C2.48421 3.26369 2.18046 3.81735 2.0605 4.42274C1.94054 5.02813 2.0102 5.65578 2.26 6.22013L4.66 11.5901C4.71446 11.72 4.74251 11.8593 4.74251 12.0001C4.74251 12.1409 4.71446 12.2803 4.66 12.4101L2.26 17.7801C2.0567 18.2368 1.97076 18.7371 2.00998 19.2355C2.0492 19.7339 2.21235 20.2145 2.48459 20.6338C2.75682 21.0531 3.12953 21.3977 3.56883 21.6363C4.00812 21.875 4.50009 22 5 22.0001C5.46823 21.9955 5.92949 21.8861 6.35 21.6801L20.35 14.6801C20.8466 14.4303 21.264 14.0474 21.5557 13.5742C21.8474 13.101 22.0018 12.556 22.0018 12.0001C22.0018 11.4442 21.8474 10.8993 21.5557 10.4261C21.264 9.95282 20.8466 9.56994 20.35 9.32013H20.34ZM19.45 12.8901L5.45 19.8901C5.26617 19.9784 5.05973 20.0084 4.85839 19.976C4.65705 19.9436 4.47041 19.8504 4.32352 19.709C4.17662 19.5675 4.07648 19.3845 4.03653 19.1846C3.99658 18.9846 4.01873 18.7772 4.1 18.5901L6.49 13.2201C6.52094 13.1484 6.54766 13.075 6.57 13.0001H13.46C13.7252 13.0001 13.9796 12.8948 14.1671 12.7072C14.3546 12.5197 14.46 12.2653 14.46 12.0001C14.46 11.7349 14.3546 11.4806 14.1671 11.293C13.9796 11.1055 13.7252 11.0001 13.46 11.0001H6.57C6.54766 10.9253 6.52094 10.8518 6.49 10.7801L4.1 5.41013C4.01873 5.22309 3.99658 5.01568 4.03653 4.8157C4.07648 4.61572 4.17662 4.43273 4.32352 4.29128C4.47041 4.14982 4.65705 4.05666 4.85839 4.02428C5.05973 3.9919 5.26617 4.02186 5.45 4.11013L19.45 11.1101C19.6138 11.194 19.7513 11.3215 19.8473 11.4786C19.9433 11.6356 19.994 11.8161 19.994 12.0001C19.994 12.1842 19.9433 12.3647 19.8473 12.5217C19.7513 12.6787 19.6138 12.8062 19.45 12.8901Z"
                                                        fill="white"></path>
                                                </svg></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-[#2A374A]">
                <div class="pt-25 md:pt-40 content max-2xl:px-3">
                    <div class="flex max-md:flex-col justify-between mx-0 items-center h-full w-full text-neutral-200">
                        <a href="#" class="flex items-center border-0"><img class="h-8 sm:h-14 rounded-2xl"
                                alt="logo" src="/picto/assets/logo-BZuaoNYZ.png">
                            <p class="text-3xl sm:text-[32px] my-auto ms-[12px] font-semibold">Brooklyn</p>
                        </a>
                        <div class="mx-7 max-md:my-7 text-center"><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#home">Home<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#about">About<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#process">Process<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#portfolio">Portfolio<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#blog">Blog<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#services">Services<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a><a
                                class="mx-2 group inline-block relative w-fit text-[12px] sm:text-[16px]"
                                href="#contact">Contact<span
                                    class="absolute left-0 bottom-0 h-0.5 w-full bg-white scale-x-0 duration-300 group-hover:scale-x-100"></span></a>
                        </div>
                        <p class="text-[12px] sm:text-[16px]">Copyright © 2025 Picto.</p>
                    </div>
                    <p class="text-white text-center max-xs:text-[12px] max-md:text-[14px] w-full py-10">Developed with
                        ❤️ by <a href="https://www.themewagon.com" class="underline font-bold"
                            target="_blank">ThemeWagon</a></p>
                </div>
            </div>
            <div class="flex justify-end relative  sm:me-10 z-10 transition-all"><a
                    class="fixed bottom-10 me-5 w-10 h-10 sm:w-12.5 sm:h-12.5 lg:w-15 lg:h-15 flex justify-center items-center rounded-full transition delay-150 duration-500 ease-in-out hover:scale-120 hover:cursor-pointer bg-picto-primary hover:bg-picto-primary-dark text-white false"><svg
                        aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-up"
                        class="svg-inline--fa fa-angle-up fa-2xl " role="img" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 448 512">
                        <path fill="currentColor"
                            d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z">
                        </path>
                    </svg></a></div>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/cally"></script>


</body>

</html>
