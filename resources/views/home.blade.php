@extends('layouts.app')

@section('title', 'Boletos - Encuentra los Mejores Eventos')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-purple-900 via-blue-900 to-indigo-900 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-12 sm:py-16 lg:py-24">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 sm:mb-6">
                Encuentra los Mejores
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                    Eventos
                </span>
            </h1>
            <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-gray-200 mb-6 sm:mb-8 max-w-3xl mx-auto px-2">
                Descubre conciertos, deportes, teatro, comedia y más. Compra boletos de forma segura y fácil.
            </p>

            <!-- Search Bar Mejorado -->
            <div class="max-w-4xl mx-auto mb-8">
                <form method="GET" action="{{ route('events.search') }}" class="relative">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search ?? '' }}"
                                   placeholder="¿Qué evento buscas? Ej: concierto, rock, teatro..."
                                   class="w-full px-6 py-4 text-lg rounded-2xl border-0 shadow-xl focus:ring-4 focus:ring-yellow-400 focus:outline-none bg-white text-gray-900 placeholder-gray-400">
                            <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <button type="submit" 
                                class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black px-8 py-4 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Buscar</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filtros por Tags -->
            @if(isset($tags) && $tags && $tags->count() > 0)
            <div class="max-w-6xl mx-auto mb-8">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                    <h3 class="text-white font-bold mb-4 text-center text-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
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

<!-- Categories Section -->
<div class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="text-center mb-6 sm:mb-8 lg:mb-12">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2 sm:mb-4">Explora por Categoría</h2>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600">Encuentra eventos que te interesen</p>
        </div>

        <!-- Filtro por Categoría -->
        @if($categories && $categories->count() > 0)
        <div class="mb-8 flex flex-wrap gap-3 justify-center">
            <a href="{{ route('events.search', array_merge(request()->except('category'), ['category' => null])) }}" 
               class="px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-105 {{ !isset($categoryId) || !$categoryId ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-xl scale-105' : 'bg-white text-gray-700 hover:bg-gray-100 shadow-md hover:shadow-lg' }}">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Todas las Categorías
                </span>
            </a>
            @foreach($categories as $category)
                <a href="{{ route('events.search', array_merge(request()->except('category'), ['category' => $category['id']])) }}" 
                   class="px-6 py-3 rounded-xl text-sm font-semibold transition-all duration-300 transform hover:scale-105 {{ (isset($categoryId) && $categoryId == $category['id']) ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-xl scale-105' : 'bg-white text-gray-700 hover:bg-gray-100 shadow-md hover:shadow-lg' }}">
                    {{ $category['name'] }} <span class="ml-1 opacity-75">({{ $category['count'] }})</span>
                </a>
            @endforeach
        </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-6">
            @php
                // intenta obtener el subdominio desde la ruta (si usas rutas con {subdomain})
                $subdomain = request()->route('subdomain') ?? null;

                // si no existe, derivarlo del host (ej: miSubdominio.tu-dominio.test)
                if (!$subdomain) {
                    $host = request()->getHost(); // puede devolver "subdominio.tu-dominio.test"
                    $parts = explode('.', $host);
                    $subdomain = count($parts) ? $parts[0] : null;
                }
            @endphp
            @foreach($categories as $category)
                <div class="group cursor-pointer">
                    <a href="{{ route('events.search', ['category' => $category['id']]) }}">
                        <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg sm:rounded-xl p-4 sm:p-6 lg:p-8 text-center hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-110 hover:shadow-2xl">
                            <div class="text-white">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto mb-2 sm:mb-3 lg:mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm sm:text-base lg:text-lg font-semibold mb-1">{{ $category['name'] }}</h3>
                                <p class="text-xs sm:text-sm opacity-90">{{ $category['count'] }} eventos</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>


<!-- Featured Events Carousel -->
@if($featuredEvents->count() > 0)
<div class="py-8 sm:py-12 lg:py-16 bg-gradient-to-br from-white via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="text-center mb-6 sm:mb-8 lg:mb-12">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2 sm:mb-4">Eventos Destacados</h2>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600">Los eventos más populares del momento</p>
        </div>

        <!-- Carousel -->
        <div class="relative">
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" id="carousel">
                    @foreach($featuredEvents as $event)
                        <div class="w-full sm:w-1/2 lg:w-1/3 flex-shrink-0 px-2 sm:px-4">
                            <div class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-purple-200">
                                <div class="relative overflow-hidden">
                                    @if($event->banner && $event->banner !== 'test.jpg')
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                                            alt="{{ $event->name }}"
                                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-64 bg-gradient-to-br from-purple-500 via-pink-500 to-indigo-500 flex items-center justify-center relative overflow-hidden">
                                            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M20 20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v22H20v-1.5zM0 20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2z\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                                            <div class="text-center text-white relative z-10">
                                                <svg class="w-16 h-16 mx-auto mb-4 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="text-lg font-bold drop-shadow-lg">{{ $event->name }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm">
                                            ⭐ Destacado
                                        </span>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $event->name }}</h3>
                                    @if($event->ticketTypes->count() > 0)
                                        <div class="flex items-baseline mb-3 text-sm">
                                            <span class="text-gray-600 font-medium mr-2">Entradas Desde:</span>
                                            <span class="text-lg font-bold text-green-600">
                                                ${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}
                                            </span>
                                        </div>
                                    @endif
                                    <p class="text-gray-600 mb-4">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</p>
                                    <p class="text-gray-500 mb-4">{{ $event->address }}</p>
                                    
                                    <!-- Tags del Evento -->
                                    @if($event->tags && $event->tags->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($event->tags as $tag)
                                                <a href="{{ route('events.search', ['tag' => $tag->id]) }}" 
                                                   class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}"
                                               target="_blank"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                                    {{ $event->space->name }}
                                            </a>
                                        </div>
                                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                                        class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black px-6 py-2 rounded-full font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                            Ver Evento
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Carousel Controls -->
            <button onclick="previousSlide()"
                    class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button onclick="nextSlide()"
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
@endif

<!-- All Events Section -->
<div class="py-8 sm:py-12 lg:py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="text-center mb-6 sm:mb-8 lg:mb-12">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-2 sm:mb-4">Todos los Eventos</h2>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600">Descubre todos los eventos disponibles</p>
        </div>

        @if($allEvents->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
        @foreach($allEvents as $event)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-transparent hover:border-blue-200 group">
                <div class="relative overflow-hidden">
                    @if($event->banner && $event->banner !== 'test.jpg')
                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                            alt="{{ $event->name }}"
                            class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-500 via-purple-500 to-indigo-500 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M20 20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v22H20v-1.5zM0 20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2z\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                            <div class="text-center text-white relative z-10">
                                <svg class="w-12 h-12 mx-auto mb-2 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="font-bold drop-shadow-lg">{{ $event->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="p-4 sm:p-5 lg:p-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-1">{{ $event->name }}</h3>
                    @if($event->ticketTypes->count() > 0)
                        <div class="flex items-baseline mb-3 text-sm">
                            <span class="text-gray-600 font-medium mr-2">Entradas Desde:</span>
                            <span class="text-lg font-bold text-green-600">
                                ${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}
                            </span>
                        </div>
                    @endif
                    <p class="text-gray-600 mb-2">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</p>
                    <p class="text-gray-500 mb-4">{{ $event->address }}</p>

                    <!-- Tags del Evento -->
                    @if($event->tags && $event->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($event->tags as $tag)
                                <a href="{{ route('events.search', ['tag' => $tag->id]) }}" 
                                   class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($event->ticketTypes->count() > 0)
                        @php
                            $totalTickets = $event->ticketTypes->sum('pivot.quantity');
                            $availableTickets = $event->ticketTypes->sum('pivot.quantity');

                            $ticketCount = \App\Models\Ticket::where('event_id', $event->id)->get();
                            $vendidos = 0;
                            // Se ha comentado el log para evitar ruido en producción
                            // \Log::info('=== SIMPLE TEST ===');
                            // \Log::info('Datos de prueba: '.json_encode($ticketCount));
                            if ($ticketCount->count() > 0) {
                                foreach ($ticketCount as $item => $value) {
                                    $vendidos++;
                                    // \Log::info('Datos de prueba contador: '.$vendidos);
                                }
                            }
                            $disponibles = $availableTickets - $vendidos;
                            // Se asegura que no haya división por cero
                            $porcentaje = $availableTickets > 0 ? ($disponibles * 100) / $availableTickets : 0;

                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                <span>Boletos disponibles</span>
                                <span>{{ $disponibles }} disponibles de {{ $availableTickets }} </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}"
                            target="_blank" {{-- Es una buena práctica abrir los enlaces externos en una nueva pestaña --}}
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                {{ $event->space->name }}
                        </a>
                        </div>
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                        class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black px-4 py-2 rounded-full font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 text-sm">
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
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No se encontraron eventos</h3>
                <p class="text-lg text-gray-600 mb-6">
                    @if((isset($search) && $search) || (isset($tagId) && $tagId) || (isset($categoryId) && $categoryId))
                        Intenta ajustar tus filtros de búsqueda o 
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-semibold">ver todos los eventos</a>
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

<!-- CTA Section -->
<div class="bg-gradient-to-r from-yellow-400 to-orange-500 py-16">
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
let currentSlide = 0;
const totalSlides = {{ $featuredEvents->count() }};
const slidesToShow = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;

function nextSlide() {
    currentSlide = (currentSlide + 1) % Math.max(1, totalSlides - slidesToShow + 1);
    updateCarousel();
}

function previousSlide() {
    currentSlide = currentSlide === 0 ? Math.max(0, totalSlides - slidesToShow) : currentSlide - 1;
    updateCarousel();
}

function updateCarousel() {
    const carousel = document.getElementById('carousel');
    const slideWidth = 100 / slidesToShow;
    carousel.style.transform = `translateX(-${currentSlide * slideWidth}%)`;
}

// Auto-advance carousel
setInterval(nextSlide, 5000);

// Update slides on resize
window.addEventListener('resize', () => {
    const newSlidesToShow = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
    if (newSlidesToShow !== slidesToShow) {
        currentSlide = 0;
        updateCarousel();
    }
});
</script>
@endpush
