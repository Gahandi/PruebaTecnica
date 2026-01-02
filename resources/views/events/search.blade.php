@extends('layouts.app')

@section('title', 'Búsqueda de Eventos')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-pink-500 to-pink-600 overflow-hidden">
    <div class="relative max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-8 sm:py-12">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4">
                Búsqueda de
                <span class="text-transparent bg-clip-text bg-pink-300">
                    Eventos
                </span>
            </h1>

            <!-- Search Bar -->
            <div class="max-w-4xl mx-auto">
                <form method="GET" action="{{ route('events.search') }}" class="relative">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $search ?? '' }}"
                                   placeholder="¿Qué evento buscas? Ej: concierto, rock, teatro..."
                                   class="w-full px-6 py-4 text-lg rounded-2xl border-0 shadow-xl focus:ring-4 focus:ring-pink-300 focus:outline-none bg-white text-gray-900 placeholder-gray-400">
                            <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            @if($tagId)
                                <input type="hidden" name="tag" value="{{ $tagId }}">
                            @endif
                            @if($categoryId)
                                <input type="hidden" name="category" value="{{ $categoryId }}">
                            @endif
                            @if($minPrice)
                                <input type="hidden" name="min_price" value="{{ $minPrice }}">
                            @endif
                            @if($maxPrice)
                                <input type="hidden" name="max_price" value="{{ $maxPrice }}">
                            @endif
                            @if($sortBy)
                                <input type="hidden" name="sort" value="{{ $sortBy }}">
                            @endif
                        </div>
                        <button type="submit" 
                                class="bg-gradient-to-r from-pink-500 to-pink-400 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Buscar</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Filter Toggle Button -->
<div class="lg:hidden fixed bottom-4 right-4 z-50">
    <button onclick="toggleMobileFilters()" 
            class="bg-gradient-to-r from-pink-500 to-pink-600 text-white p-4 rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 flex items-center justify-center">
        <svg id="filterIconOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
        </svg>
        <svg id="filterIconClose" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<!-- Mobile Filter Sidebar Overlay -->
<div id="mobileFilterOverlay" class="lg:hidden fixed inset-0 bg-black/50 z-40 hidden" onclick="toggleMobileFilters()"></div>

<!-- Mobile Filter Sidebar -->
<div id="mobileFilterSidebar" class="lg:hidden fixed top-0 right-0 h-full w-80 max-w-[85vw] bg-white z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-2xl overflow-y-auto">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#e24972]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filtros
            </h2>
            <button onclick="toggleMobileFilters()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        @if($categoryId || $minPrice || $maxPrice)
            <a href="{{ route('events.search', ['q' => $search]) }}" 
               class="block w-full text-center py-2 mb-4 text-sm text-[#e24972] hover:text-pink-800 font-medium border border-pink-200 rounded-xl">
                Limpiar Filtros
            </a>
        @endif

        <form method="GET" action="{{ route('events.search') }}" id="mobileFilterForm">
            @if($search)
                <input type="hidden" name="q" value="{{ $search }}">
            @endif

            <!-- Categorías -->
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Categorías</h3>
                <div class="space-y-2">
                    <label class="flex items-center p-3 rounded-xl hover:bg-pink-50 cursor-pointer {{ !$categoryId ? 'bg-pink-50 border-2 border-pink-200' : 'border-2 border-transparent' }}">
                        <input type="radio" name="category" value="" {{ !$categoryId ? 'checked' : '' }} 
                               onchange="document.getElementById('mobileFilterForm').submit()"
                               class="w-4 h-4 text-[#e24972] focus:ring-[#e24972]">
                        <span class="ml-3 text-sm font-medium text-gray-700">Todas</span>
                    </label>
                    @foreach($categories as $category)
                        <label class="flex items-center justify-between p-3 rounded-xl hover:bg-pink-50 cursor-pointer {{ $categoryId == $category['id'] ? 'bg-pink-50 border-2 border-pink-200' : 'border-2 border-transparent' }}">
                            <div class="flex items-center">
                                <input type="radio" name="category" value="{{ $category['id'] }}" {{ $categoryId == $category['id'] ? 'checked' : '' }}
                                       onchange="document.getElementById('mobileFilterForm').submit()"
                                       class="w-4 h-4 text-[#e24972] focus:ring-indigo-500">
                                <span class="ml-3 text-sm font-medium text-gray-700">{{ $category['name'] }}</span>
                            </div>
                            <span class="text-xs font-semibold text-[#e24972] bg-indigo-100 px-2 py-0.5 rounded-full">{{ $category['count'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Precio -->
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Precio</h3>
                <div class="space-y-2">
                    @foreach($priceRanges as $range)
                        <label class="flex items-center p-3 rounded-xl hover:bg-pink-50 cursor-pointer {{ ($minPrice == $range['min'] && $maxPrice == $range['max']) ? 'bg-pink-50 border-2 border-pink-200' : 'border-2 border-transparent' }}">
                            <input type="radio" name="price_range" value="{{ $range['min'] }}_{{ $range['max'] ?? '999999' }}"
                                   {{ ($minPrice == $range['min'] && $maxPrice == $range['max']) ? 'checked' : '' }}
                                   onchange="setMobilePriceRange({{ $range['min'] }}, {{ $range['max'] ?? '999999' }})"
                                   class="w-4 h-4 text-[#e24972] focus:ring-pink-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $range['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="hidden" name="min_price" id="mobileMinPrice" value="{{ $minPrice ?? '' }}">
                <input type="hidden" name="max_price" id="mobileMaxPrice" value="{{ $maxPrice ?? '' }}">
            </div>

            <!-- Ordenar por -->
            <div class="mb-6">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Ordenar por</h3>
                <select name="sort" onchange="document.getElementById('mobileFilterForm').submit()" 
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium transition-all bg-white">
                    <option value="date_asc" {{ $sortBy == 'date_asc' ? 'selected' : '' }}>Más Próximos</option>
                    <option value="date_desc" {{ $sortBy == 'date_desc' ? 'selected' : '' }}>Más Lejanos</option>
                    <option value="price_asc" {{ $sortBy == 'price_asc' ? 'selected' : '' }}>Precio: Menor</option>
                    <option value="price_desc" {{ $sortBy == 'price_desc' ? 'selected' : '' }}>Precio: Mayor</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Main Content with Sidebar -->
<div class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Desktop Sidebar Filters -->
            <aside class="hidden lg:block lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-4">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-[#e24972]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtros
                        </h2>
                        @if($categoryId || $minPrice || $maxPrice)
                            <a href="{{ route('events.search', ['q' => $search]) }}" 
                               class="text-xs text-[#e24972] hover:text-indigo-800 font-medium">
                                Limpiar
                            </a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('events.search') }}" id="filterForm">
                        @if($search)
                            <input type="hidden" name="q" value="{{ $search }}">
                        @endif

                        <!-- Categorías -->
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-2 text-[#e24972]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                Categorías
                            </h3>
                            <div class="space-y-2 max-h-56 overflow-y-auto custom-scrollbar">
                                <label class="flex items-center p-3 rounded-xl hover:bg-pink-50 cursor-pointer transition-all duration-200 {{ !$categoryId ? 'bg-pink-50 border-2 border-pink-200' : 'border-2 border-transparent' }}">
                                    <input type="radio" name="category" value="" {{ !$categoryId ? 'checked' : '' }} 
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="w-4 h-4 text-[#e24972] focus:ring-[#e24972]">
                                    <span class="ml-3 text-sm font-medium text-gray-700">Todas las categorías</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="flex items-center justify-between p-3 rounded-xl hover:bg-pink-50 cursor-pointer transition-all duration-200 {{ $categoryId == $category['id'] ? 'bg-pink-50 border-2 border-pink-200' : 'border-2 border-transparent' }}">
                                        <div class="flex items-center">
                                            <input type="radio" name="category" value="{{ $category['id'] }}" {{ $categoryId == $category['id'] ? 'checked' : '' }}
                                                   onchange="document.getElementById('filterForm').submit()"
                                                   class="w-4 h-4 text-[#e24972] focus:ring-indigo-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $category['name'] }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-[#e24972] bg-indigo-100 px-2.5 py-1 rounded-full">{{ $category['count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Precio -->
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Rango de Precio
                            </h3>
                            <div class="space-y-3">
                                <div class="flex flex-col gap-2"> 
                                    <input type="number" name="min_price" value="{{ $minPrice ?? '' }}" placeholder="Mín" 
                                        class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e24972] focus:border-[#e24972] text-sm font-medium transition-all"
                                        onchange="document.getElementById('filterForm').submit()">

                                    <input type="number" name="max_price" value="{{ $maxPrice ?? '' }}" placeholder="Máx" 
                                        class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#e24972] focus:border-[#e24972] text-sm font-medium transition-all"
                                        onchange="document.getElementById('filterForm').submit()">
                                </div>
                                <div class="space-y-2">
                                    @foreach($priceRanges as $range)
                                        <label class="flex items-center p-3 rounded-xl hover:bg-pink-50 cursor-pointer transition-all duration-200 {{ ($minPrice == $range['min'] && $maxPrice == $range['max']) ? 'bg-pink-50 border-2 border-pink-200' : 'border-2 border-transparent' }}">
                                            <input type="radio" name="price_range" value="{{ $range['min'] }}_{{ $range['max'] ?? '999999' }}"
                                                   {{ ($minPrice == $range['min'] && $maxPrice == $range['max']) ? 'checked' : '' }}
                                                   onchange="setPriceRange({{ $range['min'] }}, {{ $range['max'] ?? '999999' }})"
                                                   class="w-4 h-4 text-[#e24972] focus:ring-pink-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $range['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Ordenar por -->
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                </svg>
                                Ordenar por
                            </h3>
                            <select name="sort" onchange="document.getElementById('filterForm').submit()" 
                                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm font-medium transition-all bg-white">
                                <option value="date_asc" {{ $sortBy == 'date_asc' ? 'selected' : '' }}>Fecha: Más Próximos</option>
                                <option value="date_desc" {{ $sortBy == 'date_desc' ? 'selected' : '' }}>Fecha: Más Lejanos</option>
                                <option value="price_asc" {{ $sortBy == 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                                <option value="price_desc" {{ $sortBy == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                            </select>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Results Section -->
            <main class="flex-1">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                            @if($search || $categoryId || $minPrice || $maxPrice)
                                Resultados de Búsqueda
                            @else
                                Todos los Eventos
                            @endif
                        </h2>
                        <p class="text-gray-600 mt-2 text-sm sm:text-base">
                            @if($events->total() > 0)
                                <span class="font-semibold text-[#e24972]">{{ $events->total() }}</span> evento(s) encontrado(s)
                            @else
                                No se encontraron eventos
                            @endif
                        </p>
                    </div>
                </div>

                @if($events->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                        @foreach($events as $event)
                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}" 
                               class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-transparent hover:border-pink-200 group block">
                                <div class="relative aspect-square overflow-hidden">
                                    @if($event->icon && $event->icon !== 'test.jpg')
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}"
                                            alt="{{ $event->name }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    @elseif($event->banner && $event->banner !== 'test.jpg')
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                                            alt="{{ $event->name }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <!-- Price Badge -->
                                    @if($event->ticketTypes->count() > 0)
                                        <div class="absolute bottom-2 left-2">
                                            <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-lg">
                                                ${{ number_format($event->ticketTypes->min('pivot.price'), 0) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-[#e24972] transition-colors">{{ $event->name }}</h3>
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-[#e24972]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        {{ $events->links() }}
                    </div>
                @else
                    <!-- Mensaje cuando no hay eventos -->
                    <div class="text-center py-20 bg-white rounded-2xl shadow-xl border-2 border-gray-100">
                        <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-bold text-gray-900 mb-3">No se encontraron eventos</h3>
                        <p class="text-lg text-gray-600 mb-8">
                            Intenta ajustar tus filtros de búsqueda o 
                            <a href="{{ route('home') }}" class="text-[#e24972] hover:text-[#e24972] font-semibold underline">ver todos los eventos</a>
                        </p>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

<script>
function toggleMobileFilters() {
    const sidebar = document.getElementById('mobileFilterSidebar');
    const overlay = document.getElementById('mobileFilterOverlay');
    const iconOpen = document.getElementById('filterIconOpen');
    const iconClose = document.getElementById('filterIconClose');
    
    if (sidebar.classList.contains('translate-x-full')) {
        sidebar.classList.remove('translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        iconOpen.classList.add('hidden');
        iconClose.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.add('translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function setPriceRange(min, max) {
    const form = document.getElementById('filterForm');
    const minInput = form.querySelector('input[name="min_price"]');
    const maxInput = form.querySelector('input[name="max_price"]');
    
    if (minInput && maxInput) {
        minInput.value = min;
        maxInput.value = max === 999999 ? '' : max;
        form.submit();
    }
}

function setMobilePriceRange(min, max) {
    document.getElementById('mobileMinPrice').value = min;
    document.getElementById('mobileMaxPrice').value = max === 999999 ? '' : max;
    document.getElementById('mobileFilterForm').submit();
}
</script>
@endsection
