@extends('layouts.space')

@section('title', $event->name . ' - ' . $space->name)

@section('content')

    {{-- Estilos para el diseño futurista --}}
    <style>
        /* Animación de gradiente */
        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        /* Animación de flotación para partículas */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.6;
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 1;
            }
        }

        @keyframes floatReverse {

            0%,
            100% {
                transform: translateY(-20px) rotate(0deg);
                opacity: 1;
            }

            50% {
                transform: translateY(0px) rotate(-180deg);
                opacity: 0.6;
            }
        }

        /* Animación de pulso suave */
        @keyframes subtlePulse {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 0.6;
            }
        }

        /* Animación de entrada */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Hero container con parallax */
        .hero-parallax-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Contenedor del banner - centrado y limitado */
        .banner-16-9 {
            position: relative;
            width: 100%;
            max-height: 80vh;
            /* No más alto que 80% del viewport */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .banner-16-9 .banner-image {
            width: 100%;
            height: auto;
            max-height: 80vh;
            /* Limita la altura al viewport */
            object-fit: contain;
            /* Muestra la imagen completa sin cortes */
            object-position: center center;
            will-change: transform;
            transition: transform 0.15s ease-out;
        }

        /* En móvil, ajustes adicionales */
        @media (max-width: 640px) {
            .banner-16-9 {
                max-height: 60vh;
            }

            .banner-16-9 .banner-image {
                max-height: 60vh;
            }
        }

        /* Overlay futurista muy sutil */
        .futuristic-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg,
                    rgba(15, 23, 42, 0.15) 0%,
                    rgba(88, 28, 135, 0.1) 25%,
                    rgba(15, 23, 42, 0.1) 50%,
                    rgba(236, 72, 153, 0.08) 75%,
                    rgba(15, 23, 42, 0.15) 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            pointer-events: none;
        }

        /* Grid de líneas futuristas */
        .futuristic-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: subtlePulse 8s ease-in-out infinite;
        }

        /* Partículas flotantes */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.6), rgba(167, 139, 250, 0.6));
            filter: blur(1px);
            pointer-events: none;
        }

        .particle-1 {
            width: 4px;
            height: 4px;
            top: 20%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }

        .particle-2 {
            width: 6px;
            height: 6px;
            top: 40%;
            left: 80%;
            animation: floatReverse 8s ease-in-out infinite;
        }

        .particle-3 {
            width: 3px;
            height: 3px;
            top: 60%;
            left: 30%;
            animation: float 7s ease-in-out infinite 1s;
        }

        .particle-4 {
            width: 5px;
            height: 5px;
            top: 30%;
            left: 60%;
            animation: floatReverse 5s ease-in-out infinite 0.5s;
        }

        .particle-5 {
            width: 4px;
            height: 4px;
            top: 70%;
            left: 85%;
            animation: float 9s ease-in-out infinite 2s;
        }

        .particle-6 {
            width: 3px;
            height: 3px;
            top: 15%;
            left: 45%;
            animation: floatReverse 6s ease-in-out infinite 1.5s;
        }

        .particle-7 {
            width: 5px;
            height: 5px;
            top: 80%;
            left: 15%;
            animation: float 7s ease-in-out infinite 0.8s;
        }

        .particle-8 {
            width: 4px;
            height: 4px;
            top: 50%;
            left: 90%;
            animation: floatReverse 8s ease-in-out infinite 1.2s;
        }

        /* Líneas de escaneo */
        .scan-lines {
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.01) 2px,
                    rgba(255, 255, 255, 0.01) 4px);
            pointer-events: none;
        }

        /* Esquinas decorativas */
        .corner-decoration {
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(236, 72, 153, 0.5);
            z-index: 10;
        }

        .corner-tl {
            top: 1rem;
            left: 1rem;
            border-right: none;
            border-bottom: none;
        }

        .corner-tr {
            top: 1rem;
            right: 1rem;
            border-left: none;
            border-bottom: none;
        }

        .corner-bl {
            bottom: 1rem;
            left: 1rem;
            border-right: none;
            border-top: none;
        }

        .corner-br {
            bottom: 1rem;
            right: 1rem;
            border-left: none;
            border-top: none;
        }

        @media (min-width: 640px) {
            .corner-decoration {
                width: 30px;
                height: 30px;
            }

            .corner-tl,
            .corner-tr {
                top: 1.5rem;
            }

            .corner-bl,
            .corner-br {
                bottom: 1.5rem;
            }

            .corner-tl,
            .corner-bl {
                left: 1.5rem;
            }

            .corner-tr,
            .corner-br {
                right: 1.5rem;
            }
        }

        /* ================================ */
        /* Estilos para Markdown/Prose     */
        /* ================================ */
        .markdown-content {
            font-size: 1rem;
            line-height: 1.75;
            color: #374151;
        }

        .markdown-content h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ec4899;
        }

        .markdown-content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-top: 1.75rem;
            margin-bottom: 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid #f3e8ff;
        }

        .markdown-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content h4,
        .markdown-content h5,
        .markdown-content h6 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #4b5563;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .markdown-content p {
            margin-bottom: 1rem;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .markdown-content strong {
            font-weight: 700;
            color: #1f2937;
        }

        .markdown-content em {
            font-style: italic;
        }

        .markdown-content a {
            color: #ec4899;
            text-decoration: underline;
            transition: color 0.2s;
        }

        .markdown-content a:hover {
            color: #be185d;
        }

        .markdown-content ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .markdown-content ol {
            list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .markdown-content li {
            margin-bottom: 0.5rem;
            padding-left: 0.25rem;
        }

        .markdown-content li::marker {
            color: #ec4899;
        }

        .markdown-content code {
            background-color: #f3e8ff;
            color: #7c3aed;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .markdown-content pre {
            background-color: #1f2937;
            color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .markdown-content pre code {
            background-color: transparent;
            color: inherit;
            padding: 0;
            font-size: inherit;
        }

        .markdown-content blockquote {
            border-left: 4px solid #ec4899;
            background-color: #fdf2f8;
            padding: 1rem 1rem 1rem 1.5rem;
            margin: 1rem 0;
            border-radius: 0 0.5rem 0.5rem 0;
            font-style: italic;
            color: #4b5563;
        }

        .markdown-content blockquote p:last-child {
            margin-bottom: 0;
        }

        .markdown-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.875rem;
        }

        .markdown-content th {
            background-color: #fce7f3;
            color: #831843;
            font-weight: 600;
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #fbcfe8;
        }

        .markdown-content td {
            padding: 0.75rem;
            border: 1px solid #f3e8ff;
        }

        .markdown-content tr:nth-child(even) {
            background-color: #fdf4ff;
        }

        .markdown-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .markdown-content hr {
            border: none;
            height: 1px;
            background: linear-gradient(to right, transparent, #ec4899, transparent);
            margin: 2rem 0;
        }

        /* Listas de tareas (checkboxes) */
        .markdown-content input[type="checkbox"] {
            margin-right: 0.5rem;
            accent-color: #ec4899;
        }

        /* Responsivo */
        @media (max-width: 640px) {
            .markdown-content {
                font-size: 0.9375rem;
            }

            .markdown-content h1 {
                font-size: 1.5rem;
            }

            .markdown-content h2 {
                font-size: 1.25rem;
            }

            .markdown-content h3 {
                font-size: 1.125rem;
            }

            .markdown-content pre {
                font-size: 0.8rem;
                padding: 0.75rem;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">

    {{-- Hero Section con Banner Futurista --}}
    @if($event->banner)
        <div class="hero-parallax-container" id="hero-container">
            {{-- Banner 16:9 --}}
            <div class="banner-16-9">
                {{-- Imagen principal con parallax --}}
                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" alt="{{ $event->name }}" id="hero-image"
                    class="banner-image" loading="eager">

                {{-- Overlays futuristas sutiles --}}
                <div class="futuristic-overlay"></div>
                <div class="futuristic-grid"></div>
                <div class="scan-lines"></div>

                {{-- Partículas flotantes --}}
                <div class="particle particle-1"></div>
                <div class="particle particle-2"></div>
                <div class="particle particle-3"></div>
                <div class="particle particle-4"></div>
                <div class="particle particle-5"></div>
                <div class="particle particle-6"></div>
                <div class="particle particle-7"></div>
                <div class="particle particle-8"></div>

                {{-- Esquinas decorativas --}}
                <div class="corner-decoration corner-tl"></div>
                <div class="corner-decoration corner-tr"></div>
                <div class="corner-decoration corner-bl"></div>
                <div class="corner-decoration corner-br"></div>
            </div>
        </div>
    @endif

    {{-- Contenido Principal --}}
    <div
        class="relative backdrop-blur-xs  min-h-screen bg-gradient-to-br opacity-50 from-slate-50 via-indigo-50/50 to-purple-50">
        {{-- Fondo decorativo con blur --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-0 -left-1/4 w-1/2 h-1/2 bg-gradient-to-br from-pink-200/30 to-purple-200/30 rounded-full blur-3xl">
            </div>
            <div
                class="absolute bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-gradient-to-br from-blue-200/30 to-cyan-200/30 rounded-full blur-3xl">
            </div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-1/2 bg-gradient-to-br from-violet-100/40 to-pink-100/40 rounded-full blur-3xl">
            </div>
        </div>

        <div
            class="relative z-10 max-w-4xl mx-auto px-2 max-h-[calc(100vh-10vh)] sm:px-4 lg:px-6 py-6 sm:py-8 lg:py-12 space-y-4 sm:space-y-6 lg:space-y-8 {{ $event->banner ? '' : 'pt-20' }}">

            {{-- Título del evento con diseño bonito --}}
            <div class="text-center mb-2" id="event-title-card">
                <h1
                    class="text-3xl sm:text-4xl lg:text-5xl font-extrabold bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent leading-tight pb-2">
                    {{ $event->name }}
                </h1>
                @if($event->space && $event->space->name)
                    <p class="text-sm sm:text-base text-gray-500 mt-2 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        <span>Presentado por <strong class="text-gray-700">{{ $event->space->name }}</strong></span>
                    </p>
                @endif
            </div>

            <div id="main-content-card"
                class="bg-white/80 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 border border-white/30 shadow-2xl shadow-purple-500/10">

                <h2 class="text-xl sm:text-2xl font-semibold text-pink-600 mb-4 sm:mb-6 lg:mb-8 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Información del Evento
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-4 sm:mb-6 lg:mb-8">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Fecha y Hora</h3>
                            <p class="text-gray-700 text-lg">
                                {{ \Carbon\Carbon::parse($event->date)->format('l, d F Y \a \l\a\s H:i') }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($event->date)->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Ubicación</h3>
                            <p class="text-gray-700 text-lg">{{ $event->address }}</p>
                            @if($event->coordinates)
                                <button onclick="scrollToMapSection()"
                                    class="text-sm text-pink-600 hover:text-pink-800 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    Ver en mapa
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Keywords del Space -->
                    @if($event->space && $event->space->keywords)
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Palabras Clave
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $event->space->keywords) as $keyword)
                                    @if(trim($keyword))
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ trim($keyword) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @if($event->description)
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            Descripción
                        </h3>
                        <div
                            class="bg-gradient-to-br from-white/70 via-white/60 to-pink-50/40 backdrop-blur-md rounded-xl p-3 sm:p-6 border border-pink-100/50 shadow-lg overflow-hidden">
                            <div class="markdown-content">
                                {!! \App\Helpers\MarkdownHelper::render($event->description) !!}
                            </div>
                        </div>
                    </div>
                @endif

                @if($event->agenda && $event->agenda !== 'N/A')
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            Temario
                        </h3>
                        <div
                            class="bg-gradient-to-br from-white/70 via-purple-50/40 to-pink-50/30 backdrop-blur-md rounded-xl p-3 sm:p-6 border border-purple-100/50 shadow-lg overflow-hidden">
                            <div class="markdown-content">
                                {!! \App\Helpers\MarkdownHelper::render($event->agenda) !!}
                            </div>
                        </div>
                    </div>
                @endif

                @if($event->coordinates)
                    <div id="map-section-container" class="bg-gradient-to-r from-pink-50 to-pink-100 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ubicación del Evento
                        </h2>
                        <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg relative">
                            <div id="map" style="height: 400px; width: 100%;"></div>
                            <!-- Botones de control del mapa -->
                            <div class="absolute top-4 right-4 flex flex-col gap-2 z-10">
                                <button id="center-map-btn" onclick="centerMap()"
                                    class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg shadow-lg border border-gray-200 flex items-center space-x-2 transition-all duration-200 hover:shadow-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Centrar</span>
                                </button>
                                <button id="directions-btn" onclick="showDirections()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-200 hover:shadow-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                        </path>
                                    </svg>
                                    <span class="text-sm font-medium">Rutas</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @php
                // Calcular si hay algún boleto disponible en total
                $totalAvailableTickets = 0;
                foreach ($event->ticketTypes as $ticketType) {
                    $totalAsignado = $ticketType->pivot->quantity;
                    $vendidos = \App\Models\Ticket::where('event_id', $event->id)
                        ->where('ticket_types_id', $ticketType->id)
                        ->count();
                    $disponibles = max(0, $totalAsignado - $vendidos);
                    $totalAvailableTickets += $disponibles;
                }
                // Determinar si debemos mostrar el formulario de compra o el mensaje de agotado
                $showPurchaseSection = $event->ticketTypes->count() > 0 && $totalAvailableTickets > 0;
            @endphp
            @if($showPurchaseSection)
                {{-- TODO: Contenido del Formulario de Compra (Tu código actual de formulario va aquí) --}}
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-8 border border-white/30 shadow-xl">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-8 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                            </path>
                        </svg> Compra de Boletos
                    </h2>
                    <form id="purchaseForm" class="space-y-8">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($event->ticketTypes as $ticketType)
                                {{-- Recalculamos disponibilidad para cada boleto dentro del loop --}}
                                @php
                                    // Total asignado desde la tabla pivot
                                    $totalAsignado = $ticketType->pivot->quantity;
                                    // Boletos ya vendidos
                                    $vendidos = \App\Models\Ticket::where('event_id', $event->id)
                                        ->where('ticket_types_id', $ticketType->id)
                                        ->count();
                                    // Disponibles reales
                                    $disponibles = max(0, $totalAsignado - $vendidos);
                                @endphp
                                <div
                                    class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-xl p-6 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        hover:border-pink-300 transition-all duration-300 hover:shadow-lg hover:scale-105 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                                    <!-- Columna de nombre y disponibilidad -->
                                    <div class="flex flex-col items-start justify-center flex-1">
                                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $ticketType->name }}</h4>

                                        <div class="flex flex-wrap items-center gap-2">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-green-600">{{ $disponibles }}
                                                    disponibles</span>
                                            </div>

                                            @if($disponibles <= 5 && $disponibles > 0)
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">¡Últimos!</span>
                                            @elseif($disponibles <= 20 && $disponibles > 0)
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pocos
                                                    disponibles</span>
                                            @elseif($disponibles === 0)
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Agotado</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Columna del precio -->
                                    <div class="flex items-center justify-center md:justify-end text-center flex-1">
                                        <div>
                                            <p class="text-3xl font-bold text-green-600 mb-1">
                                                ${{ number_format($ticketType->pivot->price, 2) }}</p>
                                            <p class="text-sm text-gray-500">por boleto</p>
                                        </div>
                                    </div>

                                    <!-- Controles de cantidad -->
                                    <div class="flex items-center justify-center gap-4 flex-1 md:justify-end">
                                        <button type="button"
                                            class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300"
                                            onclick="decreaseQuantity({{ $ticketType->id }})" @if($disponibles == 0) disabled @endif>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                                </path>
                                            </svg>
                                        </button>

                                        <input type="number" id="quantity_{{ $ticketType->id }}"
                                            name="tickets[{{ $loop->index }}][quantity]" value="0" min="0" max="{{ $disponibles }}"
                                            class="w-16 h-12 text-center text-xl font-bold border-2 border-gray-200 rounded-lg"
                                            @if($disponibles == 0) disabled @endif oninput="enforceMaxValue(this, {{ $disponibles }})"
                                            onchange="updateTotal()">

                                        <button type="button"
                                            class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300"
                                            onclick="increaseQuantity({{ $ticketType->id }}, {{ $disponibles }})"
                                            @if($disponibles == 0) disabled @endif>
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <input type="hidden" name="tickets[{{ $loop->index }}][ticket_type_id]"
                                        value="{{ $ticketType->id }}">
                                </div>

                            @endforeach
                        </div>

                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                            <h4 class="text-xl font-semibold text-gray-900 mb-6">Resumen de la Orden</h4>
                            <div id="order_summary" class="space-y-4">
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span id="subtotal" class="font-semibold">$0.00</span>
                                </div>
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-600">Descuento:</span>
                                    <span id="discount" class="font-semibold text-green-600">$0.00</span>
                                </div>
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-600">IVA (16%):</span>
                                    <span id="taxes" class="font-semibold">$0.00</span>
                                </div>
                                <div class="border-t pt-4">
                                    <div class="flex justify-between font-bold text-2xl">
                                        <span>Total:</span>
                                        <span id="total" class="text-green-600">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="button" id="add_to_cart_button" disabled onclick="addToCart()"
                                class="flex-1 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 to-pink-500 text-white px-8 py-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-lg flex items-center justify-center space-x-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                                    </path>
                                </svg>
                                <span>Agregar al Carrito</span>
                            </button>
                            <button type="button" id="view_cart_button"
                                onclick="window.location.href='{{ config('app.url') }}/cart'"
                                class="flex-1 bg-gray-600 text-white px-8 py-4 rounded-xl hover:bg-gray-700 transition-all duration-300 font-semibold text-lg flex items-center justify-center space-x-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                                </svg>
                                <span>Ver Carrito</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- TODO: Mensaje de Agotado o Sin Boletos Configurados --}}
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-12 text-center border border-white/30 shadow-xl">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                            </path>
                        </svg>
                    </div>
                    @if ($event->ticketTypes->count() > 0)
                        <h3 class="text-2xl font-semibold text-gray-900 mb-3">¡Accesos Agotados! 😥</h3>
                        <p class="text-gray-600">Lamentablemente, todos los tipos de boletos para este evento se han agotado.</p>
                    @else
                        <h3 class="text-2xl font-semibold text-gray-900 mb-3">Boletos no disponibles</h3>
                        <p class="text-gray-600">Este evento no tiene tipos de boletos configurados o listados para la venta.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    @if($event->coordinates)
        <script async
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&loading=async&libraries=places,directions&callback=initGoogleMap">
            </script>
    @endif

    @push('scripts')
        <script>
            console.log('JavaScript starting...');

            let ticketPrices = {
                @foreach($event->ticketTypes as $ticketType)
                    {{ $ticketType->id }}: {{ $ticketType->pivot->price }}{{ $loop->last ? '' : ',' }}
                @endforeach
                                                                                                                                                                                                                                                                                                                    };

            console.log('Ticket prices:', ticketPrices);

            let appliedCoupon = null;
            let map = null;
            let marker = null;
            let directionsService = null;
            let directionsRenderer = null;
            let eventLocation = null;

            // Funciones para manejo de cantidades
            function increaseQuantity(ticketTypeId, maxQuantity) {
                console.log('increaseQuantity called:', ticketTypeId, maxQuantity);
                const input = document.getElementById(`quantity_${ticketTypeId}`);
                if (!input) {
                    console.error('Input not found for ticket type:', ticketTypeId);
                    return;
                }
                const currentValue = parseInt(input.value);
                if (currentValue < maxQuantity) {
                    input.value = currentValue + 1;
                    updateTotal();
                    updateAvailableCount(ticketTypeId, maxQuantity);
                }
            }

            function decreaseQuantity(ticketTypeId) {
                console.log('decreaseQuantity called:', ticketTypeId);
                const input = document.getElementById(`quantity_${ticketTypeId}`);
                if (!input) {
                    console.error('Input not found for ticket type:', ticketTypeId);
                    return;
                }
                const currentValue = parseInt(input.value);
                if (currentValue > 0) {
                    input.value = currentValue - 1;
                    updateTotal();
                    // Obtener el maxQuantity desde el atributo max del input
                    const maxQuantity = parseInt(input.getAttribute('max')) || 0;
                    updateAvailableCount(ticketTypeId, maxQuantity);
                }
            }

            // Función para actualizar el contador de boletos disponibles
            function updateAvailableCount(ticketTypeId, initialQuantity) {
                const input = document.getElementById(`quantity_${ticketTypeId}`);
                const availableElement = document.getElementById(`available_${ticketTypeId}`);

                if (!input || !availableElement) {
                    return;
                }

                const selectedQuantity = parseInt(input.value) || 0;
                const available = Math.max(0, initialQuantity - selectedQuantity);

                // Actualizar el max del input para reflejar la disponibilidad real
                input.setAttribute('max', available);

                // Actualizar el texto con animación
                availableElement.textContent = available;

                // Cambiar color según disponibilidad
                const parentSpan = availableElement.parentElement;
                if (available <= 0) {
                    parentSpan.classList.remove('text-green-600', 'text-yellow-600');
                    parentSpan.classList.add('text-red-600');
                    availableElement.textContent = '0';
                    // Deshabilitar botón de incrementar si no hay disponibles
                    const increaseBtn = input.nextElementSibling;
                    if (increaseBtn && increaseBtn.tagName === 'BUTTON') {
                        increaseBtn.disabled = true;
                        increaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else if (available <= 5) {
                    parentSpan.classList.remove('text-green-600', 'text-red-600');
                    parentSpan.classList.add('text-yellow-600');
                    // Habilitar botón de incrementar
                    const increaseBtn = input.nextElementSibling;
                    if (increaseBtn && increaseBtn.tagName === 'BUTTON') {
                        increaseBtn.disabled = false;
                        increaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    parentSpan.classList.remove('text-red-600', 'text-yellow-600');
                    parentSpan.classList.add('text-green-600');
                    // Habilitar botón de incrementar
                    const increaseBtn = input.nextElementSibling;
                    if (increaseBtn && increaseBtn.tagName === 'BUTTON') {
                        increaseBtn.disabled = false;
                        increaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }

                // Animación de cambio
                availableElement.style.transform = 'scale(1.2)';
                availableElement.style.transition = 'transform 0.2s ease';
                setTimeout(() => {
                    availableElement.style.transform = 'scale(1)';
                }, 200);
            }

            function updateTotal() {
                let subtotal = 0;
                let hasTickets = false;

                // Calculate subtotal
                @foreach($event->ticketTypes as $ticketType)
                    const quantity{{ $ticketType->id }} = parseInt(document.getElementById('quantity_{{ $ticketType->id }}').value) || 0;
                    if (quantity{{ $ticketType->id }} > 0) hasTickets = true;
                    subtotal += quantity{{ $ticketType->id }} * {{ $ticketType->pivot->price }};
                @endforeach

                // Apply coupon discount
                let discount = 0;
                if (appliedCoupon) {
                    discount = (subtotal * appliedCoupon.discount_percentage) / 100;
                }

                // Calculate taxes (16% IVA)
                const taxableAmount = subtotal - discount;
                const taxes = taxableAmount * 0.16;
                const total = taxableAmount + taxes;

                // Update display
                document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
                document.getElementById('discount').textContent = '$' + discount.toFixed(2);
                document.getElementById('taxes').textContent = '$' + taxes.toFixed(2);
                document.getElementById('total').textContent = '$' + total.toFixed(2);

                // Enable/disable add to cart button
                document.getElementById('add_to_cart_button').disabled = !hasTickets;
            }

            // Función mejorada para agregar al carrito
            async function addToCart() {
                const tickets = [];
                @foreach($event->ticketTypes as $ticketType)
                    const quantity{{ $ticketType->id }} = parseInt(document.getElementById('quantity_{{ $ticketType->id }}').value) || 0;
                    if (quantity{{ $ticketType->id }} > 0) {
                        tickets.push({
                            ticket_type_id: {{ $ticketType->id }},
                            quantity: quantity{{ $ticketType->id }}
                        });
                    }
                @endforeach

                                                                                                                                                                                                                                                                                                                        if (tickets.length === 0) {
                    showNotification('Por favor selecciona al menos un boleto.', 'error');
                    return;
                }

                // Disable button to prevent multiple submissions
                const button = document.getElementById('add_to_cart_button');
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `
                                                                                                                                                                                                                                                                                                                            <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                                                                                                                                                                                                                                                                            </svg>
                                                                                                                                                                                                                                                                                                                            <span>Agregando...</span>
                                                                                                                                                                                                                                                                                                                        `;

                try {
                    // Obtener token CSRF del dominio base si estamos en un subdominio
                    let csrfToken = '{{ csrf_token() }}';
                    const currentHost = window.location.host;
                    const baseUrl = window.CartConfig?.baseUrl || '{{ config("app.url") }}';
                    const baseHost = new URL(baseUrl).host;

                    // Si estamos en un subdominio, obtener el token del dominio base
                    if (currentHost !== baseHost && currentHost.includes('.')) {
                        try {
                            const tokenResponse = await fetch(baseUrl + '/cart/csrf-token', {
                                method: 'GET',
                                credentials: 'include'
                            });
                            if (tokenResponse.ok) {
                                const tokenData = await tokenResponse.json();
                                csrfToken = tokenData.token;
                            }
                        } catch (e) {
                            console.warn('Could not fetch CSRF token from base domain, using local token');
                        }
                    }

                    // Add each ticket type to cart sequentially
                    for (const ticket of tickets) {
                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('ticket_type_id', ticket.ticket_type_id);
                        formData.append('event_id', {{ $event->id }});
                        formData.append('quantity', ticket.quantity);

                        const response = await fetch(window.CartConfig?.cartAddUrl || '{{ \App\Helpers\CartHelper::getCartAddRoute() }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            credentials: 'include'
                        });

                        const responseData = await response.json();

                        if (!response.ok) {
                            console.error('Error response:', response.status, responseData);
                            throw new Error(responseData.message || 'Error al agregar al carrito');
                        }

                        // El servidor ya guardó el item en la sesión, solo actualizar UI
                    }

                    // Mostrar notificación de éxito
                    showNotification('¡Boletos agregados al carrito exitosamente!', 'success');

                    // Actualizar contadores de disponibilidad después de agregar al carrito
                    @foreach($event->ticketTypes as $ticketType)
                        const quantity{{ $ticketType->id }} = parseInt(document.getElementById('quantity_{{ $ticketType->id }}').value) || 0;
                        if (quantity{{ $ticketType->id }} > 0) {
                            // Resetear el input después de agregar al carrito
                            document.getElementById('quantity_{{ $ticketType->id }}').value = 0;
                            // Actualizar contador de disponibles
                            updateAvailableCount({{ $ticketType->id }}, {{ $ticketType->pivot->quantity }});
                        }
                    @endforeach

                    // Actualizar total después de resetear
                    updateTotal();

                    // Invalidar cache y actualizar contador y dropdown desde el servidor
                    if (typeof window.invalidateCartCache === 'function') {
                        window.invalidateCartCache();
                    }

                    if (typeof window.updateCartCount === 'function') {
                        await window.updateCartCount();
                    }

                    if (typeof window.updateCartDropdown === 'function') {
                        window.updateCartDropdown();
                    }

                    // Disparar evento de carrito actualizado
                    document.dispatchEvent(new CustomEvent('cartUpdated'));

                } catch (error) {
                    console.error('Error:', error);
                    showNotification(error.message || 'Error al agregar boletos al carrito. Inténtalo de nuevo.', 'error');
                } finally {
                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            // Función para mostrar notificaciones
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
                const icon = type === 'success' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                    type === 'error' ?
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';

                notification.className = `fixed top-20 right-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-[9999] transform translate-x-[120%] transition-all duration-300 ease-out`;
                notification.innerHTML = `
                                                                                                                                                                                                                                                                                                                            <div class="flex items-center">
                                                                                                                                                                                                                                                                                                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                                                                                                                                                    ${icon}
                                                                                                                                                                                                                                                                                                                                </svg>
                                                                                                                                                                                                                                                                                                                                ${message}
                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                        `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.classList.remove('translate-x-[120%]');
                    notification.classList.add('translate-x-0');
                }, 100);

                setTimeout(() => {
                    notification.classList.remove('translate-x-0');
                    notification.classList.add('translate-x-[120%]');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Función para actualizar contador del carrito (local, pero usa la global si está disponible)
            function updateCartCount() {
                // Si existe la función global, usarla
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount();
                    return;
                }

                // Fallback local (siempre dominio base)
                fetch('{{ \App\Helpers\CartHelper::getCartCountRoute() }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Actualizar el contador visual en el header
                        const cartButton = document.querySelector('#cart-dropdown button');
                        let cartCount = document.querySelector('#cart-dropdown .bg-red-500');

                        if (data.count > 0) {
                            // Si el badge no existe, crearlo
                            if (!cartCount && cartButton) {
                                cartCount = document.createElement('span');
                                cartCount.className = 'absolute -top-0.5 -right-0.5 inline-flex items-center justify-center bg-red-500 text-white text-xs font-bold min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg';
                                cartButton.appendChild(cartCount);
                            }

                            if (cartCount) {
                                cartCount.textContent = data.count;
                                cartCount.style.display = 'inline-flex';
                                cartCount.classList.add('animate-pulse');
                                setTimeout(() => cartCount.classList.remove('animate-pulse'), 1000);
                            }
                        } else {
                            if (cartCount) {
                                cartCount.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error updating cart count:', error);
                    });
            }

            // Función para inicializar Google Maps
            window.initGoogleMap = function () {
                @if($event->coordinates)
                    const coordinates = "{{ $event->coordinates }}".split(',').map(Number);
                    if (coordinates.length === 2) {
                        eventLocation = { lat: coordinates[0], lng: coordinates[1] };

                        // Inicializar el mapa
                        map = new google.maps.Map(document.getElementById('map'), {
                            center: eventLocation,
                            zoom: 15,
                            mapTypeControl: true,
                            streetViewControl: true,
                            fullscreenControl: true
                        });

                        // Crear marcador
                        const eventName = @json($event->name);
                        const eventAddress = @json($event->address);

                        marker = new google.maps.Marker({
                            position: eventLocation,
                            map: map,
                            title: eventName,
                            animation: google.maps.Animation.DROP
                        });

                        // Info window con información del evento
                        const infoWindow = new google.maps.InfoWindow({
                            content: `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="p-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <h3 class="font-bold text-lg mb-1">${eventName}</h3>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <p class="text-gray-600 text-sm">${eventAddress}</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    `
                        });

                        marker.addListener('click', function () {
                            infoWindow.open(map, marker);
                        });

                        // Abrir info window automáticamente
                        infoWindow.open(map, marker);

                        // Inicializar servicios de direcciones
                        directionsService = new google.maps.DirectionsService();
                        directionsRenderer = new google.maps.DirectionsRenderer({
                            map: map,
                            suppressMarkers: false
                        });
                    }
                @endif
                                                                                                                                                                                                                                                                                                                    }

            // Función para centrar el mapa en la ubicación del evento
            function centerMap() {
                if (map && eventLocation) {
                    map.setCenter(eventLocation);
                    map.setZoom(15);

                    // Animación suave
                    if (marker) {
                        marker.setAnimation(google.maps.Animation.BOUNCE);
                        setTimeout(() => {
                            marker.setAnimation(null);
                        }, 2000);
                    }
                }
            }

            // Función para mostrar rutas
            function showDirections() {
                if (!map || !eventLocation) return;

                // Intentar obtener la ubicación actual del usuario
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            const userLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };

                            // Calcular ruta
                            directionsService.route({
                                origin: userLocation,
                                destination: eventLocation,
                                travelMode: google.maps.TravelMode.DRIVING
                            }, function (response, status) {
                                if (status === 'OK') {
                                    directionsRenderer.setDirections(response);

                                    // Cambiar el botón para ocultar rutas
                                    const btn = document.getElementById('directions-btn');
                                    btn.onclick = hideDirections;
                                    btn.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                                                                                                                                                                                                                                                                                                </svg>
                                                                                                                                                                                                                                                                                                                                                <span class="text-sm font-medium">Ocultar Rutas</span>
                                                                                                                                                                                                                                                                                                                                            `;
                                    btn.className = 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-200 hover:shadow-xl';
                                } else {
                                    alert('No se pudo calcular la ruta: ' + status);
                                }
                            });
                        },
                        function (error) {
                            // Si no se puede obtener la ubicación, abrir Google Maps en nueva pestaña
                            const url = `https://www.google.com/maps/dir/?api=1&destination=${eventLocation.lat},${eventLocation.lng}`;
                            window.open(url, '_blank');
                        }
                    );
                } else {
                    // Si el navegador no soporta geolocalización, abrir Google Maps
                    const url = `https://www.google.com/maps/dir/?api=1&destination=${eventLocation.lat},${eventLocation.lng}`;
                    window.open(url, '_blank');
                }
            }

            // Función para ocultar rutas
            function hideDirections() {
                if (directionsRenderer) {
                    directionsRenderer.setDirections({ routes: [] });

                    // Restaurar el botón
                    const btn = document.getElementById('directions-btn');
                    btn.onclick = showDirections;
                    btn.innerHTML = `
                                                                                                                                                                                                                                                                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                                                                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                                                                                                                                                                                                                                                                                                                </svg>
                                                                                                                                                                                                                                                                                                                                <span class="text-sm font-medium">Rutas</span>
                                                                                                                                                                                                                                                                                                                            `;
                    btn.className = 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2 transition-all duration-200 hover:shadow-xl';
                }
            }

            // --- Efecto Parallax ---
            // Hace que la imagen y la card se muevan con diferentes velocidades al scroll
            (function initParallax() {
                const heroImage = document.getElementById('hero-image');
                const heroContainer = document.getElementById('hero-container');
                const contentCard = document.getElementById('main-content-card');
                const titleCard = document.getElementById('event-title-card');

                function updateParallax() {
                    const scrollPosition = window.scrollY;

                    // Parallax para el banner
                    if (heroImage && heroContainer) {
                        const containerRect = heroContainer.getBoundingClientRect();

                        // Solo aplicar parallax si el banner está visible
                        if (containerRect.bottom > 0) {
                            // Efecto parallax: la imagen se mueve a 50% de la velocidad del scroll
                            const translateY = scrollPosition * 0.5;
                            heroImage.style.transform = `translateY(${translateY}px)`;
                        }
                    }

                    // Parallax sutil para la card de contenido
                    if (contentCard) {
                        // Calcula el offset basado en la posición de la card
                        const cardRect = contentCard.getBoundingClientRect();
                        const windowHeight = window.innerHeight;

                        // Solo aplicar cuando la card está visible
                        if (cardRect.top < windowHeight && cardRect.bottom > 0) {
                            // Efecto sutil: la card se mueve ligeramente hacia arriba
                            const cardOffset = (windowHeight - cardRect.top) * 0.03;
                            contentCard.style.transform = `translateY(${-cardOffset}px)`;
                        }
                    }

                    // Parallax para el título
                    if (titleCard) {
                        const titleRect = titleCard.getBoundingClientRect();
                        const windowHeight = window.innerHeight;

                        if (titleRect.top < windowHeight && titleRect.bottom > 0) {
                            const titleOffset = (windowHeight - titleRect.top) * 0.02;
                            titleCard.style.transform = `translateY(${-titleOffset}px)`;
                        }
                    }
                }

                // Usar requestAnimationFrame para performance óptima
                let ticking = false;
                window.addEventListener('scroll', function () {
                    if (!ticking) {
                        requestAnimationFrame(function () {
                            updateParallax();
                            ticking = false;
                        });
                        ticking = true;
                    }
                });

                // Ejecutar una vez al cargar
                updateParallax();
            })();
            // --- FIN Parallax ---

            // Inicializar mapa automáticamente
            document.addEventListener('DOMContentLoaded', function () {
                // El mapa se inicializa automáticamente cuando Google Maps API carga (callback initGoogleMap)

                // Inicializar highlight.js para código en markdown
                hljs.highlightAll();

                // Inicializar contadores de disponibilidad
                @foreach($event->ticketTypes as $ticketType)
                    updateAvailableCount({{ $ticketType->id }}, {{ $ticketType->pivot->quantity }});
                @endforeach

                updateTotal();
            });

            // Escuchar eventos de carrito actualizado
            document.addEventListener('cartUpdated', function () {
                // Usar función global si está disponible
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount();
                } else {
                    updateCartCount();
                }

                // Actualizar dropdown también
                if (typeof window.updateCartDropdown === 'function') {
                    window.updateCartDropdown();
                }
            });

            // Initialize
            console.log('JavaScript loaded successfully');
            updateTotal();

            function scrollToMapSection() {
                const mapSection = document.getElementById('map-section-container');
                if (mapSection) {
                    mapSection.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start' // Asegura que el elemento se alinee con la parte superior
                    });
                }
            }
            function enforceMaxValue(input, max) {
                // Obtener el valor y eliminar cualquier decimal
                let value = input.value;

                // Quitar punto o coma decimal
                value = value.replace(/[.,].*$/, "");

                // Convertir a número entero
                value = parseInt(value);

                // Validaciones
                if (isNaN(value) || value < 0) {
                    input.value = 0;
                }
                else if (value > max) {
                    input.value = max;
                }
                else {
                    input.value = value; // Asignar el valor entero corregido
                }
            }
        </script>
    @endpush
@endsection