@extends('layouts.app')

@section('title', 'Boletos - Encuentra los Mejores Eventos')

@push('styles')
    <style>
        /* Ocultar scrollbar en carousel */
        #carouselContainer::-webkit-scrollbar,
        #categoriesCarouselContainer::-webkit-scrollbar {
            display: none;
        }

        #carouselContainer,
        #categoriesCarouselContainer {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-pink-500 to-pink-600 overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-16 sm:py-20 lg:py-32">
            <div class="text-center">
                <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 sm:mb-6">
                    Eventos para cursos de
                    <span class="text-transparent bg-clip-text bg-pink-300">
                        Belleza
                    </span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-white mb-6 sm:mb-8 max-w-3xl mx-auto px-2">
                    Descubre los mejores eventos. Compra boletos de forma segura y fácil.
                </p>

                <!-- Search Bar Mejorado -->
                <div class="max-w-4xl mx-auto mb-8">
                    <form method="GET" action="{{ route('events.search') }}" class="relative">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="flex-1 relative">
                                <input type="text" name="q" value="{{ $search ?? '' }}" placeholder="¿Qué evento buscas?"
                                    class="w-full px-6 py-4 text-lg rounded-2xl border-0 shadow-xl focus:ring-4 focus:ring-pink-300 focus:outline-none bg-white text-gray-900 placeholder-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <button type="submit"
                                class="bg-white text-pink-900 px-6 sm:px-8 py-4 rounded-2xl font-bold text-base sm:text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2 w-full sm:w-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>Buscar</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Filtros por Tags -->
                @if(isset($tags) && $tags && $tags->count() > 0)
                    <div class="max-w-6xl mx-auto mb-8 hidden sm:block">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                            <h3 class="text-white font-bold mb-4 text-center text-lg flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                                Filtrar por Etiquetas
                            </h3>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <a href="{{ route('events.search', array_merge(request()->except('tag'), ['tag' => null])) }}"
                                    class="px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 transform hover:scale-105 {{ !isset($tagId) || !$tagId ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-black shadow-xl scale-105' : 'bg-white/20 text-white hover:bg-white/30 shadow-md' }}">
                                    Todas
                                </a>
                                @foreach($tags as $tag)
                                    <a href="{{ route('events.search', array_merge(request()->except('tag'), ['tag' => $tag->id])) }}"
                                        class="px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300 transform hover:scale-105 {{ (isset($tagId) && $tagId == $tag->id) ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-black shadow-xl scale-105' : 'bg-white/20 text-white hover:bg-white/30 shadow-md' }}">
                                        {{ $tag->name }} <span class="ml-1 opacity-75">({{ $tag->events_count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Categories Section Carousel -->
    <div class="bg-gray-50 py-12 sm:py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-[#e24972] mb-2 sm:mb-4">Explora por
                    categoría</h2>
                <p class="text-sm sm:text-base lg:text-lg text-gray-600">Encuentra eventos que te interesen</p>
            </div>

            <!-- Carousel de Categorías (Desktop) y Grid 4x4 (Móvil) -->
            @if($categories && $categories->count() > 0)

                {{-- Lógica PHP previa --}}
                @php
                    // MÓVIL: Agrupar en chunks de 16 para el grid 4x4 (4 filas x 4 columnas)
                    $mobileCategoryChunks = $categories->chunk(16);

                    // DESKTOP: Usamos la colección completa sin chunk para scroll continuo
                    $allCategories = $categories;
                @endphp

                {{-- ========================================== --}}
                {{-- MÓVIL: Carousel con Grid 4x4 (lg:hidden) --}}
                {{-- ========================================== --}}
                <div class="lg:hidden relative group">

                    <div class="overflow-x-auto overflow-y-hidden scrollbar-hide scroll-smooth snap-x snap-mandatory pb-6"
                        id="mobileCategoriesCarousel" style="scrollbar-width: none; -ms-overflow-style: none;">

                        <div class="flex">
                            @foreach($mobileCategoryChunks as $chunkIndex => $chunk)
                                {{-- Cada Slide del Carousel Móvil --}}
                                <div class="w-full flex-shrink-0 snap-center px-2">
                                    {{-- Grid 4x4 (4 columnas) --}}
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($chunk as $category)
                                            @php
                                                $catId = is_array($category) ? ($category['id'] ?? null) : ($category->id ?? null);
                                                $catName = is_array($category) ? ($category['name'] ?? 'Categoría') : ($category->name ?? 'Categoría');
                                                $catImage = is_array($category) ? ($category['image'] ?? null) : ($category->image ?? null);
                                                $catImageUrl = $catImage ? \App\Helpers\ImageHelper::getImageUrl($catImage) : asset('images/categories/Poster7.jpeg');
                                            @endphp

                                            <a href="{{ route('categories.show', ['category' => $catId]) }}" class="block group/item">
                                                <div class="relative rounded-lg overflow-hidden aspect-square shadow-sm">
                                                    <img src="{{ $catImageUrl }}" alt="{{ $catName }}"
                                                        class="w-full h-full object-cover">
                                                    {{-- Overlay sutil --}}
                                                    <div
                                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end justify-center p-1">
                                                        <span
                                                            class="text-white text-[9px] font-bold text-center leading-tight line-clamp-2">
                                                            {{ $catName }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Indicadores (Puntos) Móvil --}}
                    @if($mobileCategoryChunks->count() > 1)
                        <div class="absolute bottom-0 left-0 right-0 flex justify-center gap-1.5 pb-1">
                            @foreach($mobileCategoryChunks as $index => $chunk)
                                <button onclick="scrollToMobileSlide({{ $index }})"
                                    class="h-1.5 rounded-full transition-all duration-300 mobile-indicator {{ $index === 0 ? 'bg-pink-500 w-4' : 'bg-gray-300 w-1.5' }}"
                                    data-index="{{ $index }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ========================================== --}}
                {{-- DESKTOP: Carousel Continuo (hidden lg:block) --}}
                {{-- ========================================== --}}
                <div class="hidden lg:block relative -mx-8 px-8 group/desktop">

                    {{-- Botón Anterior --}}
                    <button onclick="scrollDesktop('left')"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-sm p-3 rounded-full shadow-lg text-gray-700 opacity-0 group-hover/desktop:opacity-100 transition-opacity duration-300 hover:bg-pink-50 hover:text-pink-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    {{-- Contenedor de Scroll --}}
                    <div class="overflow-x-auto scrollbar-hide scroll-smooth py-4" id="desktopCategoriesContainer"
                        style="scrollbar-width: none;">

                        {{-- Flex container continuo (sin chunks) --}}
                        <div class="flex gap-4">
                            @foreach($allCategories as $category)
                                @php
                                    $catId = is_array($category) ? ($category['id'] ?? null) : ($category->id ?? null);
                                    $catName = is_array($category) ? ($category['name'] ?? 'Categoría') : ($category->name ?? 'Categoría');
                                    $catCount = is_array($category) ? ($category['count'] ?? 0) : ($category->count ?? $category->events_count ?? 0);
                                    $catImage = is_array($category) ? ($category['image'] ?? null) : ($category->image ?? null);
                                    $catImageUrl = $catImage ? \App\Helpers\ImageHelper::getImageUrl($catImage) : asset('images/categories/Poster7.jpeg');
                                @endphp

                                {{-- Item individual (ancho fijo o flexible) --}}
                                <div
                                    class="flex-shrink-0 w-[200px] xl:w-[240px] select-none transition-transform duration-300 hover:-translate-y-1">
                                    <a href="{{ route('categories.show', ['category' => $catId]) }}" class="block h-full">
                                        <div
                                            class="relative rounded-xl overflow-hidden aspect-[5/5] group cursor-pointer shadow-md hover:shadow-xl transition-shadow">
                                            <img src="{{ $catImageUrl }}" alt="{{ $catName }}"
                                                class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110">

                                            {{-- Overlay Desktop --}}
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-4">
                                                <h3
                                                    class="text-white font-bold text-lg leading-tight mb-1 group-hover:text-pink-300 transition-colors">
                                                    {{ $catName }}
                                                </h3>
                                                <p class="text-gray-300 text-xs font-medium">
                                                    {{ $catCount }} eventos
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Botón Siguiente --}}
                    <button onclick="scrollDesktop('right')"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-sm p-3 rounded-full shadow-lg text-gray-700 opacity-0 group-hover/desktop:opacity-100 transition-opacity duration-300 hover:bg-pink-50 hover:text-pink-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- Fade lateral para indicar continuidad --}}
                    <div
                        class="absolute top-0 right-0 h-full w-24 bg-gradient-to-l from-white via-white/50 to-transparent pointer-events-none z-10 lg:block hidden">
                    </div>
                    <div
                        class="absolute top-0 left-0 h-full w-24 bg-gradient-to-r from-white via-white/50 to-transparent pointer-events-none z-10 lg:block hidden">
                    </div>
                </div>

                {{-- Ver todas --}}
                <div class="text-center mt-6 mb-8">
                    <a href="{{ route('categories.index') }}"
                        class="text-sm font-semibold text-pink-600 hover:text-pink-700 hover:underline">
                        Ver todas las categorías &rarr;
                    </a>
                </div>

            @endif
        </div>
    </div>


    {{-- Eventos Urgentes - Próximos a comenzar (menos de 48 horas) --}}
    @if(isset($urgentEvents) && $urgentEvents->count() > 0)
        <div class="py-8 sm:py-12 bg-gradient-to-br from-orange-500 via-red-500 to-pink-500" id="urgent-events-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-6 sm:mb-8">
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-4">
                        <span class="animate-pulse">🔥</span>
                        <span class="text-white font-bold text-sm uppercase tracking-wide">¡No te los pierdas!</span>
                    </div>
                    <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2">
                        ¡Próximos a Comenzar!
                    </h2>
                    <p class="text-white/80 text-sm sm:text-base">Eventos que empiezan en menos de 48 horas</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($urgentEvents as $urgentEvent)
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($urgentEvent->space->subdomain) }}/{{ $urgentEvent->slug }}"
                            class="group relative bg-white/10 backdrop-blur-md rounded-2xl overflow-hidden border border-white/20 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-2xl">

                            {{-- Countdown Badge --}}
                            <div class="absolute top-3 right-3 z-10">
                                <div class="countdown-badge bg-black/70 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 animate-pulse"
                                    data-countdown="{{ \Carbon\Carbon::parse($urgentEvent->date)->toIso8601String() }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="countdown-text">--:--</span>
                                </div>
                            </div>

                            {{-- Image --}}
                            <div class="aspect-video overflow-hidden">
                                @if($urgentEvent->icon && $urgentEvent->icon !== 'test.jpg')
                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($urgentEvent->icon) }}"
                                        alt="{{ $urgentEvent->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full bg-gradient-to-br from-white/20 to-white/5 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <h3
                                    class="text-white font-bold text-lg mb-2 line-clamp-1 group-hover:text-yellow-200 transition-colors">
                                    {{ $urgentEvent->name }}
                                </h3>
                                <div class="flex items-center gap-2 text-white/70 text-sm mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($urgentEvent->date)->format('d M, H:i') }}</span>
                                </div>
                                @if($urgentEvent->ticketTypes->count() > 0)
                                    <div class="flex items-center justify-between">
                                        <span class="text-white/60 text-xs">Desde</span>
                                        <span class="text-yellow-300 font-bold text-lg">
                                            ${{ number_format($urgentEvent->ticketTypes->min('pivot.price'), 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    <!-- Featured Events Carousel -->
    @if($featuredEvents->count() > 0)
        <div class="py-12 sm:py-16 lg:py-24 bg-pink-50">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                    <h2 class="font-serif text-2xl sm:text-3xl md:text-4xl font-bold text-[#e24972] mb-2 sm:mb-4">Eventos
                        Destacados</h2>
                    <p class="text-sm sm:text-base lg:text-lg text-gray-600">Los eventos más populares del momento</p>
                </div>

                {{-- Carousel --}}
                <div class="relative -mx-2 sm:-mx-4 lg:-mx-8 px-2 sm:px-4 lg:px-8">
                    <div class="overflow-x-auto overflow-y-visible scrollbar-hide scroll-smooth snap-x snap-mandatory py-4"
                        id="carouselContainer" style="scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="flex gap-4 px-2" id="carousel">
                            @foreach($featuredEvents as $event)
                                <div class="w-full sm:w-1/2 lg:w-1/3 flex-shrink-0 snap-start">
                                    <div
                                        class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 hover:scale-105 border-2 border-transparent hover:border-pink-200 h-full flex flex-col">
                                        <div class="relative overflow-hidden flex-shrink-0">
                                            @if($event->icon && $event->icon !== 'test.jpg')
                                                <div class="aspect-square w-full">
                                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}"
                                                        alt="{{ $event->name }}"
                                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                </div>
                                            @else
                                                <div
                                                    class="aspect-square w-full bg-gradient-to-br from-purple-500 via-pink-500 to-indigo-500 flex items-center justify-center relative overflow-hidden">
                                                    <div class="absolute inset-0 opacity-20"
                                                        style="background-image: url('data:image/svg+xml,%3Csvg width=\" 40\"
                                                        height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg
                                                        fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M20
                                                        20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v22H20v-1.5zM0
                                                        20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4
                                                        4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2zm0
                                                        4h20v2H20v-2z\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                                                    <div class="text-center text-white relative z-10">
                                                        <svg class="w-16 h-16 mx-auto mb-4 drop-shadow-lg" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                                clip-rule="evenodd"></path>
                                                        </svg>
                                                        <p class="text-lg font-bold drop-shadow-lg">{{ $event->name }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="absolute top-4 right-4">
                                                <span
                                                    class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-3 sm:px-4 py-1.5 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm">
                                                    ⭐ Destacado
                                                </span>
                                            </div>
                                        </div>
                                        <div class="p-4 sm:p-6 flex flex-col flex-grow">
                                            <h3 class="text-lg sm:text-xl font-bold text-[#e24972] mb-2 line-clamp-2">
                                                {{ $event->name }}
                                            </h3>
                                            @if($event->ticketTypes->count() > 0)
                                                <div class="flex items-baseline mb-3 text-sm">
                                                    <span class="text-gray-600 font-medium mr-2">Entradas Desde:</span>
                                                    <span class="text-lg font-bold text-green-600">
                                                        ${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <p class="text-gray-600 mb-2 text-sm sm:text-base">
                                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                                            </p>
                                            <p class="text-gray-500 mb-4 text-sm line-clamp-2">{{ $event->address }}</p>

                                            {{-- Tags del Evento --}}
                                            @if($event->tags && $event->tags->count() > 0)
                                                <div class="flex flex-wrap gap-2 mb-4">
                                                    @foreach($event->tags->take(3) as $tag)
                                                        <a href="{{ route('events.search', ['tag' => $tag->id]) }}"
                                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                                </path>
                                                            </svg>
                                                            {{ $tag->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                                <div class="flex items-center">
                                                    <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 hover:bg-pink-200 transition-colors">
                                                        {{ $event->space->name }}
                                                    </a>
                                                </div>
                                                <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                                                    class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-4 sm:px-6 py-2 rounded-full font-semibold text-sm transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                                    Ver Evento
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Carousel Controls --}}
                    <button onclick="scrollCarousel('prev')"
                        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg hover:shadow-xl transition-all hover:bg-white z-10">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <button onclick="scrollCarousel('next')"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg hover:shadow-xl transition-all hover:bg-white z-10">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- All Events Section -->
    <div class="py-12 sm:py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#e24972] mb-2 sm:mb-4">Todos los Eventos</h2>
                <p class="text-sm sm:text-base lg:text-lg text-gray-600">Descubre todos los eventos disponibles</p>
            </div>

            @if($allEvents->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                    @foreach($allEvents as $event)
                        <div
                            class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-transparent hover:border-pink-200 group h-full flex flex-col">
                            <div class="relative overflow-hidden flex-shrink-0">
                                @if($event->icon && $event->icon !== 'test.jpg')
                                    <div class="aspect-square w-full">
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}" alt="{{ $event->name }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                @else
                                    <div
                                        class="aspect-square w-full bg-gradient-to-br from-blue-500 via-purple-500 to-indigo-500 flex items-center justify-center relative overflow-hidden">
                                        <div class="absolute inset-0 opacity-20"
                                            style="background-image: url('data:image/svg+xml,%3Csvg width=\" 40\" height=\"40\"
                                            viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\"
                                            fill-opacity=\"0.4\"%3E%3Cpath d=\"M20
                                            20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v22H20v-1.5zM0
                                            20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4
                                            4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2z\"/%3E%3C/g%3E%3C/svg%3E');">
                                        </div>
                                        <div class="text-center text-white relative z-10">
                                            <svg class="w-12 h-12 mx-auto mb-2 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="font-bold drop-shadow-lg">{{ $event->name }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 sm:p-5 lg:p-6 flex flex-col flex-grow">
                                {{-- Título --}}
                                <h3 class="text-lg sm:text-xl font-bold text-[#e24972] mb-3 line-clamp-2">{{ $event->name }}</h3>

                                {{-- Descripción (fecha, countdown y dirección) --}}
                                <div class="mb-4">
                                    <p class="text-gray-600 mb-2 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                                    </p>

                                    {{-- Countdown Badge --}}
                                    @php
                                        $eventDate = \Carbon\Carbon::parse($event->date);
                                        $now = \Carbon\Carbon::now();
                                        $hoursRemaining = $now->diffInHours($eventDate, false);
                                        $minutesRemaining = $now->diffInMinutes($eventDate, false);
                                        $daysRemaining = $now->diffInDays($eventDate, false);
                                        $monthsRemaining = $now->diffInMonths($eventDate, false);
                                    @endphp

                                    @if($hoursRemaining > 0)
                                        <div class="mb-2">
                                            @if($hoursRemaining <= 48)
                                                {{-- Menos de 48 horas - mostrar contador en tiempo real --}}
                                                <div class="inline-flex items-center bg-gradient-to-r from-red-500 to-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md animate-pulse"
                                                    data-countdown="{{ $eventDate->toIso8601String() }}" data-event-id="{{ $event->id }}">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="countdown-text">
                                                        {{ floor($hoursRemaining) }}h {{ ($minutesRemaining % 60) }}m
                                                    </span>
                                                </div>
                                            @elseif($daysRemaining <= 7)
                                                {{-- Entre 2 y 7 días --}}
                                                <div
                                                    class="inline-flex items-center bg-gradient-to-r from-amber-500 to-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    Faltan {{ $daysRemaining }} {{ $daysRemaining == 1 ? 'día' : 'días' }}
                                                </div>
                                            @elseif($daysRemaining <= 30)
                                                {{-- Entre 8 y 30 días --}}
                                                <div
                                                    class="inline-flex items-center bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    Faltan {{ $daysRemaining }} días
                                                </div>
                                            @elseif($monthsRemaining < 12)
                                                {{-- Entre 1 y 12 meses --}}
                                                <div
                                                    class="inline-flex items-center bg-gradient-to-r from-purple-500 to-indigo-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    @if($monthsRemaining == 1)
                                                        Falta 1 mes
                                                    @else
                                                        Faltan {{ $monthsRemaining }} meses
                                                    @endif
                                                </div>
                                            @else
                                                {{-- Más de un año --}}
                                                @php $yearsRemaining = floor($monthsRemaining / 12); @endphp
                                                <div
                                                    class="inline-flex items-center bg-gradient-to-r from-gray-500 to-slate-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    @if($yearsRemaining == 1)
                                                        Falta 1 año
                                                    @else
                                                        Faltan {{ $yearsRemaining }} años
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <p class="text-gray-500 text-sm line-clamp-2 flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-pink-500 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $event->address }}
                                    </p>
                                </div>

                                <!-- Tags del Evento -->
                                @if($event->tags && $event->tags->count() > 0)
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        @foreach($event->tags->take(3) as $tag)
                                            <a href="{{ route('events.search', ['tag' => $tag->id]) }}"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                    </path>
                                                </svg>
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Espaciador para empujar contenido al final --}}
                                <div class="flex-grow"></div>

                                {{-- Precio --}}
                                @if($event->ticketTypes->count() > 0)
                                    <div class="mb-3">
                                        <div class="flex items-baseline">
                                            <span class="text-gray-600 font-medium mr-2 text-sm">Desde:</span>
                                            <span class="text-xl font-bold text-green-600">
                                                ${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Barra de disponibles --}}
                                @if($event->ticketTypes->count() > 0)
                                    @php
                                        $totalTickets = $event->ticketTypes->sum('pivot.quantity');
                                        $availableTickets = $event->ticketTypes->sum('pivot.quantity');
                                        $ticketCount = \App\Models\Ticket::where('event_id', $event->id)->get();
                                        $vendidos = 0;
                                        if ($ticketCount->count() > 0) {
                                            foreach ($ticketCount as $item => $value) {
                                                $vendidos++;
                                            }
                                        }
                                        $disponibles = $availableTickets - $vendidos;
                                        $porcentaje = $availableTickets > 0 ? ($disponibles * 100) / $availableTickets : 0;
                                    @endphp
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                            <span class="font-medium">Disponibilidad</span>
                                            <span class="font-semibold">{{ $disponibles }} de {{ $availableTickets }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2.5 rounded-full transition-all duration-300"
                                                style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Botón Ver Evento --}}
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div class="flex items-center">
                                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}"
                                            target="_blank"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 hover:bg-pink-200 transition-colors">
                                            {{ $event->space->name }}
                                        </a>
                                    </div>
                                    <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                                        class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-5 py-2.5 rounded-full font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-sm">
                                        Ver Evento
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Mensaje cuando no hay eventos -->
                <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No se encontraron eventos</h3>
                    <p class="text-lg text-gray-600 mb-6">
                        @if((isset($search) && $search) || (isset($tagId) && $tagId) || (isset($categoryId) && $categoryId))
                            Intenta ajustar tus filtros de búsqueda o
                            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-semibold">ver todos los
                                eventos</a>
                        @else
                            No hay eventos disponibles en este momento.
                        @endif
                    </p>
                </div>
            @endif

            @if($allEvents->count() > 6)
                <div class="text-center mt-12">
                    <a href="{{ route('events.public') }}"
                        class="bg-gray-900 hover:bg-gray-800 text-white px-8 py-3 rounded-full font-semibold transition-colors">
                        Ver Todos los Eventos
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Past Events Section -->
    @if(isset($pastEvents) && $pastEvents->count() > 0)
        <div class="py-12 sm:py-16 lg:py-24 bg-pink-50">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#e24972] mb-2 sm:mb-4">
                        Eventos Pasados
                    </h2>
                    <p class="text-sm sm:text-base lg:text-lg text-gray-600">Revive nuestros eventos anteriores</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                    @foreach($pastEvents as $event)
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                            class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-transparent hover:border-pink-200 group h-full flex flex-col">
                            <div class="relative overflow-hidden flex-shrink-0">
                                @if($event->icon && $event->icon !== 'test.jpg')
                                    <div class="aspect-square w-full">
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}" alt="{{ $event->name }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 grayscale-[30%]">
                                    </div>
                                @else
                                    <div
                                        class="aspect-square w-full bg-gradient-to-br from-gray-400 via-gray-500 to-gray-600 flex items-center justify-center relative overflow-hidden">
                                        <div class="text-center text-white relative z-10">
                                            <svg class="w-12 h-12 mx-auto mb-2 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="font-bold drop-shadow-lg">{{ $event->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                {{-- Badge Finalizado --}}
                                <div class="absolute top-4 right-4">
                                    <span
                                        class="bg-gradient-to-r from-gray-600 to-gray-500 text-white px-3 sm:px-4 py-1.5 rounded-full text-xs font-bold shadow-lg">
                                        ✓ Finalizado
                                    </span>
                                </div>
                            </div>
                            <div class="p-4 sm:p-5 lg:p-6 flex flex-col flex-grow">
                                {{-- Título --}}
                                <h3 class="text-lg sm:text-xl font-bold text-[#e24972] mb-3 line-clamp-2">{{ $event->name }}</h3>

                                {{-- Fecha --}}
                                <div class="mb-4">
                                    <p class="text-gray-600 mb-2 text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                                    </p>

                                    <p class="text-gray-500 text-sm line-clamp-2 flex items-start">
                                        <svg class="w-4 h-4 mr-2 text-pink-500 mt-0.5 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $event->address }}
                                    </p>
                                </div>

                                {{-- Espaciador --}}
                                <div class="flex-grow"></div>

                                {{-- Footer con Space --}}
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span
                                        onclick="event.preventDefault(); event.stopPropagation(); window.open('{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}', '_blank');"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 hover:bg-pink-200 transition-colors cursor-pointer z-10">
                                        {{ $event->space->name }}
                                    </span>
                                    <span class="text-gray-400 text-sm font-medium">
                                        Ver evento →
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Spaces/Cajones Section -->
    @if(isset($spaces) && $spaces->count() > 0)
        <div class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-pink-50 via-white to-purple-50">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="text-center mb-6 sm:mb-8 lg:mb-12">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 mb-2 sm:mb-4">
                        Descubre Nuestros Cajones
                    </h2>
                    <p class="text-sm sm:text-base lg:text-lg text-gray-600">Organizadores de eventos que crean experiencias
                        increíbles</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($spaces as $space)
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}" target="_blank"
                            class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-pink-200 transform hover:-translate-y-1">
                            <!-- Logo/Banner -->
                            <div
                                class="aspect-square w-full relative overflow-hidden bg-gradient-to-br from-pink-100 to-purple-100">
                                @if($space->logo)
                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" alt="{{ $space->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @elseif($space->banner)
                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->banner) }}" alt="{{ $space->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-pink-400 to-purple-500">
                                        <span class="text-white text-4xl font-bold">{{ substr($space->name, 0, 1) }}</span>
                                    </div>
                                @endif

                                <!-- Overlay con gradiente -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>

                                <!-- Badge de eventos -->
                                <div class="absolute top-3 right-3">
                                    <span
                                        class="bg-white/90 backdrop-blur-sm text-pink-600 px-2.5 py-1 rounded-full text-xs font-bold shadow-md">
                                        {{ $space->events_count }} {{ $space->events_count == 1 ? 'evento' : 'eventos' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-4">
                                <h3
                                    class="font-bold text-gray-800 group-hover:text-pink-600 transition-colors line-clamp-1 text-sm sm:text-base">
                                    {{ $space->name }}
                                </h3>
                                @if($space->description)
                                    <p class="text-gray-500 text-xs sm:text-sm line-clamp-2 mt-1">{{ $space->description }}</p>
                                @endif

                                <!-- Ver más -->
                                <div
                                    class="mt-3 flex items-center text-pink-500 text-xs sm:text-sm font-medium group-hover:text-pink-600">
                                    <span>Ver cajón</span>
                                    <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-pink-500 to-pink-600 py-20 sm:py-24 lg:py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                ¿Tienes un evento que promocionar?
            </h2>
            <p class="text-xl text-white mb-8">
                Crea tu propio espacio de eventos y vende boletos de forma fácil y segura
            </p>
            <a href="{{ route('user.spaces.create') }}"
                class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition-colors">
                Crear Mi Espacio
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const carouselContainer = document.getElementById('carouselContainer');
        const carousel = document.getElementById('carousel');
        let autoScrollInterval;
        let isScrolling = false;

        // Función para hacer scroll del carousel
        function scrollCarousel(direction) {
            if (!carouselContainer) return;

            const scrollAmount = carouselContainer.offsetWidth * 0.9;

            if (direction === 'next') {
                carouselContainer.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                carouselContainer.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            }

            resetAutoScroll();
        }

        // Auto-scroll
        function startAutoScroll() {
            stopAutoScroll();
            autoScrollInterval = setInterval(() => {
                if (!carouselContainer || isScrolling) return;

                const maxScroll = carouselContainer.scrollWidth - carouselContainer.clientWidth;

                // Si llegamos al final, volver al inicio
                if (carouselContainer.scrollLeft >= maxScroll - 10) {
                    carouselContainer.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    scrollCarousel('next');
                }
            }, 5000);
        }

        function stopAutoScroll() {
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
            }
        }

        function resetAutoScroll() {
            isScrolling = true;
            stopAutoScroll();
            setTimeout(() => {
                isScrolling = false;
                startAutoScroll();
            }, 3000);
        }

        // Detectar scroll manual del usuario
        if (carouselContainer) {
            let scrollTimeout;
            carouselContainer.addEventListener('scroll', () => {
                isScrolling = true;
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    isScrolling = false;
                    resetAutoScroll();
                }, 150);
            }, { passive: true });

            // Manejar rueda del mouse - SIMPLIFICADO
            carouselContainer.addEventListener('wheel', (e) => {
                // Solo interceptar si es scroll horizontal nativo (shift+scroll o trackpad horizontal)
                if (Math.abs(e.deltaX) > 0) {
                    // Dejar que el navegador maneje el scroll horizontal nativo
                    resetAutoScroll();
                }
                // Para scroll vertical, NO hacer nada - dejar que la página haga scroll normalmente
            }, { passive: true });
        }

        // Iniciar auto-scroll al cargar
        if (carouselContainer) {
            startAutoScroll();
        }

        // Navegación con teclado
        document.addEventListener('keydown', (e) => {
            // Solo si el carousel está visible en viewport
            if (!carouselContainer) return;

            const rect = carouselContainer.getBoundingClientRect();
            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;

            if (isVisible) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    scrollCarousel('prev');
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    scrollCarousel('next');
                }
            }
        });
    </script>

    {{-- Script para el Carousel de Categorías --}}
    <script>
        const categoriesContainer = document.getElementById('categoriesCarouselContainer');
        const categoriesCarousel = document.getElementById('categoriesCarousel');
        const categoriesIndicators = document.getElementById('categoriesIndicators');
        let categoriesAutoScrollInterval;
        let categoriesIsScrolling = false;

        // Función para hacer scroll del carousel de categorías
        function scrollCategoriesCarousel(direction) {
            if (!categoriesContainer) return;

            const scrollAmount = categoriesContainer.offsetWidth;

            if (direction === 'next') {
                categoriesContainer.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                categoriesContainer.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            }

            resetCategoriesAutoScroll();
        }

        // Ir a un slide específico
        function goToCategorySlide(index) {
            if (!categoriesContainer) return;

            const scrollAmount = categoriesContainer.offsetWidth * index;
            categoriesContainer.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });

            resetCategoriesAutoScroll();
        }

        // Actualizar indicadores
        function updateCategoriesIndicators() {
            if (!categoriesContainer || !categoriesIndicators) return;

            const slideWidth = categoriesContainer.offsetWidth;
            const currentIndex = Math.round(categoriesContainer.scrollLeft / slideWidth);

            const indicators = categoriesIndicators.querySelectorAll('button');
            indicators.forEach((indicator, index) => {
                if (index === currentIndex) {
                    indicator.classList.remove('bg-gray-300', 'w-3');
                    indicator.classList.add('bg-pink-500', 'w-8');
                } else {
                    indicator.classList.remove('bg-pink-500', 'w-8');
                    indicator.classList.add('bg-gray-300', 'w-3');
                }
            });
        }
        // --- Lógica MÓVIL ---
        function scrollToMobileSlide(index) {
            const container = document.getElementById('mobileCategoriesCarousel');
            const slideWidth = container.offsetWidth; // Ancho de un slide completo

            container.scrollTo({
                left: slideWidth * index,
                behavior: 'smooth'
            });

            updateMobileIndicators(index);
        }

        // Detectar scroll manual en móvil para actualizar puntitos
        const mobileContainer = document.getElementById('mobileCategoriesCarousel');
        if (mobileContainer) {
            mobileContainer.addEventListener('scroll', () => {
                const index = Math.round(mobileContainer.scrollLeft / mobileContainer.offsetWidth);
                updateMobileIndicators(index);
            });
        }

        function updateMobileIndicators(activeIndex) {
            const dots = document.querySelectorAll('.mobile-indicator');
            dots.forEach((dot, idx) => {
                if (idx === activeIndex) {
                    dot.classList.remove('bg-gray-300', 'w-1.5');
                    dot.classList.add('bg-pink-500', 'w-4');
                } else {
                    dot.classList.remove('bg-pink-500', 'w-4');
                    dot.classList.add('bg-gray-300', 'w-1.5');
                }
            });
        }


        // --- Lógica DESKTOP ---
        function scrollDesktop(direction) {
            const container = document.getElementById('desktopCategoriesContainer');
            const scrollAmount = 600; // Cantidad de pixeles a mover

            if (direction === 'left') {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }

        // Auto-scroll para categorías
        function startCategoriesAutoScroll() {
            stopCategoriesAutoScroll();
            categoriesAutoScrollInterval = setInterval(() => {
                if (!categoriesContainer || categoriesIsScrolling) return;

                const maxScroll = categoriesContainer.scrollWidth - categoriesContainer.clientWidth;

                // Si llegamos al final, volver al inicio
                if (categoriesContainer.scrollLeft >= maxScroll - 10) {
                    categoriesContainer.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                } else {
                    scrollCategoriesCarousel('next');
                }
            }, 5000);
        }

        function stopCategoriesAutoScroll() {
            if (categoriesAutoScrollInterval) {
                clearInterval(categoriesAutoScrollInterval);
            }
        }

        function resetCategoriesAutoScroll() {
            categoriesIsScrolling = true;
            stopCategoriesAutoScroll();
            setTimeout(() => {
                categoriesIsScrolling = false;
                startCategoriesAutoScroll();
            }, 3000);
        }

        // Detectar scroll manual del usuario en categorías
        if (categoriesContainer) {
            let categoriesScrollTimeout;
            categoriesContainer.addEventListener('scroll', () => {
                categoriesIsScrolling = true;
                clearTimeout(categoriesScrollTimeout);
                updateCategoriesIndicators();
                categoriesScrollTimeout = setTimeout(() => {
                    categoriesIsScrolling = false;
                    resetCategoriesAutoScroll();
                }, 150);
            }, { passive: true });

            // Manejar rueda del mouse
            categoriesContainer.addEventListener('wheel', (e) => {
                if (Math.abs(e.deltaX) > 0) {
                    resetCategoriesAutoScroll();
                }
            }, { passive: true });

            // Iniciar auto-scroll al cargar
            startCategoriesAutoScroll();
        }
        // === MOBILE CATEGORIES CAROUSEL ===
        const mobileCategoriesCarousel = document.getElementById('mobileCategoriesCarousel');
        const mobileCategoriesIndicators = document.getElementById('mobileCategoriesIndicators');

        function goToMobileCategorySlide(index) {
            if (!mobileCategoriesCarousel) return;

            const slideWidth = mobileCategoriesCarousel.offsetWidth;
            mobileCategoriesCarousel.scrollTo({
                left: slideWidth * index,
                behavior: 'smooth'
            });
        }

        function updateMobileCategoriesIndicators() {
            if (!mobileCategoriesCarousel || !mobileCategoriesIndicators) return;

            const slideWidth = mobileCategoriesCarousel.offsetWidth;
            const currentIndex = Math.round(mobileCategoriesCarousel.scrollLeft / slideWidth);

            const indicators = mobileCategoriesIndicators.querySelectorAll('button');
            indicators.forEach((indicator, index) => {
                if (index === currentIndex) {
                    indicator.classList.remove('bg-gray-300', 'w-2');
                    indicator.classList.add('bg-pink-500', 'w-6');
                } else {
                    indicator.classList.remove('bg-pink-500', 'w-6');
                    indicator.classList.add('bg-gray-300', 'w-2');
                }
            });
        }

        // Detectar scroll manual en el carousel móvil
        if (mobileCategoriesCarousel) {
            mobileCategoriesCarousel.addEventListener('scroll', () => {
                updateMobileCategoriesIndicators();
            }, { passive: true });
        }

        // === COUNTDOWN TIMER para eventos próximos (menos de 48 horas) ===
        function updateCountdowns() {
            const countdownElements = document.querySelectorAll('[data-countdown]');
            const now = new Date();

            countdownElements.forEach(el => {
                const eventDate = new Date(el.getAttribute('data-countdown'));
                const diff = eventDate - now;
                const countdownText = el.querySelector('.countdown-text');

                if (!countdownText) return;

                if (diff <= 0) {
                    // El evento ya pasó o está ocurriendo
                    countdownText.textContent = '¡Ahora!';
                    el.classList.remove('animate-pulse', 'bg-black/70');
                    el.classList.add('bg-green-500');
                    return;
                }

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                if (hours < 1) {
                    // Menos de 1 hora - mostrar minutos y segundos
                    countdownText.textContent = `${minutes}m ${seconds}s`;
                    el.classList.remove('bg-black/70');
                    el.classList.add('bg-red-600');
                } else if (hours < 48) {
                    // Menos de 48 horas - mostrar horas y minutos
                    countdownText.textContent = `${hours}h ${minutes}m`;
                }
            });
        }

        // Actualizar countdowns cada segundo
        setInterval(updateCountdowns, 1000);

        // Primera actualización inmediata
        updateCountdowns();
    </script>
@endpush