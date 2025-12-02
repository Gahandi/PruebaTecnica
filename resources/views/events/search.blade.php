@extends('layouts.app')

@section('title', 'Búsqueda de Eventos')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-purple-900 via-blue-900 to-indigo-900 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-black opacity-50"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-2 sm:px-4 lg:px-8 py-8 sm:py-12">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4">
                Búsqueda de
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
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
                                   class="w-full px-6 py-4 text-lg rounded-2xl border-0 shadow-xl focus:ring-4 focus:ring-yellow-400 focus:outline-none bg-white text-gray-900 placeholder-gray-400">
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
                                class="bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-black px-8 py-4 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2">
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

<!-- Main Content with Sidebar -->
<div class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Sidebar Filters - Mejorado tipo E-commerce -->
            <aside class="lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-4">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filtros
                        </h2>
                        @if($tagId || $categoryId || $minPrice || $maxPrice)
                            <a href="{{ route('events.search', ['q' => $search]) }}" 
                               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
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
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                Categorías
                            </h3>
                            <div class="space-y-2 max-h-56 overflow-y-auto custom-scrollbar">
                                <label class="flex items-center p-3 rounded-xl hover:bg-indigo-50 cursor-pointer transition-all duration-200 {{ !$categoryId ? 'bg-indigo-50 border-2 border-indigo-200' : 'border-2 border-transparent' }}">
                                    <input type="radio" name="category" value="" {{ !$categoryId ? 'checked' : '' }} 
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700">Todas las categorías</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="flex items-center justify-between p-3 rounded-xl hover:bg-indigo-50 cursor-pointer transition-all duration-200 {{ $categoryId == $category['id'] ? 'bg-indigo-50 border-2 border-indigo-200' : 'border-2 border-transparent' }}">
                                        <div class="flex items-center">
                                            <input type="radio" name="category" value="{{ $category['id'] }}" {{ $categoryId == $category['id'] ? 'checked' : '' }}
                                                   onchange="document.getElementById('filterForm').submit()"
                                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $category['name'] }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-2.5 py-1 rounded-full">{{ $category['count'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tags -->
                        @if(isset($tags) && $tags && $tags->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Etiquetas
                            </h3>
                            <div class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                                <label class="flex items-center p-3 rounded-xl hover:bg-purple-50 cursor-pointer transition-all duration-200 {{ !$tagId ? 'bg-purple-50 border-2 border-purple-200' : 'border-2 border-transparent' }}">
                                    <input type="radio" name="tag" value="" {{ !$tagId ? 'checked' : '' }}
                                           onchange="document.getElementById('filterForm').submit()"
                                           class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700">Todas las etiquetas</span>
                                </label>
                                @foreach($tags as $tag)
                                    <label class="flex items-center justify-between p-3 rounded-xl hover:bg-purple-50 cursor-pointer transition-all duration-200 {{ $tagId == $tag->id ? 'bg-purple-50 border-2 border-purple-200' : 'border-2 border-transparent' }}">
                                        <div class="flex items-center">
                                            <input type="radio" name="tag" value="{{ $tag->id }}" {{ $tagId == $tag->id ? 'checked' : '' }}
                                                   onchange="document.getElementById('filterForm').submit()"
                                                   class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">{{ $tag->name }}</span>
                                        </div>
                                        <span class="text-xs font-semibold text-purple-600 bg-purple-100 px-2.5 py-1 rounded-full">{{ $tag->events_count }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

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
                                        class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium transition-all"
                                        onchange="document.getElementById('filterForm').submit()">

                                    <input type="number" name="max_price" value="{{ $maxPrice ?? '' }}" placeholder="Máx" 
                                        class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-medium transition-all"
                                        onchange="document.getElementById('filterForm').submit()">
                                </div>
                                <div class="space-y-2">
                                    @foreach($priceRanges as $range)
                                        <label class="flex items-center p-3 rounded-xl hover:bg-green-50 cursor-pointer transition-all duration-200 {{ ($minPrice == $range['min'] && $maxPrice == $range['max']) ? 'bg-green-50 border-2 border-green-200' : 'border-2 border-transparent' }}">
                                            <input type="radio" name="price_range" value="{{ $range['min'] }}_{{ $range['max'] ?? '999999' }}"
                                                   {{ ($minPrice == $range['min'] && $maxPrice == $range['max']) ? 'checked' : '' }}
                                                   onchange="setPriceRange({{ $range['min'] }}, {{ $range['max'] ?? '999999' }})"
                                                   class="w-4 h-4 text-green-600 focus:ring-green-500">
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

            <!-- Results Section - Mejorado -->
            <main class="flex-1">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">
                            @if($search || $tagId || $categoryId || $minPrice || $maxPrice)
                                Resultados de Búsqueda
                            @else
                                Todos los Eventos
                            @endif
                        </h2>
                        <p class="text-gray-600 mt-2 text-sm sm:text-base">
                            @if($events->total() > 0)
                                <span class="font-semibold text-indigo-600">{{ $events->total() }}</span> evento(s) encontrado(s)
                            @else
                                No se encontraron eventos
                            @endif
                        </p>
                    </div>
                </div>

                @if($events->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        @foreach($events as $event)
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border-2 border-transparent hover:border-indigo-200 group">
                                <div class="relative overflow-hidden">
                                    @if($event->banner && $event->banner !== 'test.jpg')
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                                            alt="{{ $event->name }}"
                                            class="w-full h-56 object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-56 bg-gradient-to-br from-blue-500 via-purple-500 to-indigo-500 flex items-center justify-center relative overflow-hidden">
                                            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M20 20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v22H20v-1.5zM0 20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2z\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                                            <div class="text-center text-white relative z-10">
                                                <svg class="w-16 h-16 mx-auto mb-3 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <p class="font-bold drop-shadow-lg text-lg">{{ $event->name }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="absolute top-4 right-4">
                                        @if($event->ticketTypes->count() > 0)
                                            <span class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-lg backdrop-blur-sm">
                                                Desde ${{ number_format($event->ticketTypes->min('pivot.price'), 0) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $event->name }}</h3>
                                    
                                    <div class="flex items-center text-gray-600 mb-3 text-sm">
                                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div class="flex items-start text-gray-600 mb-4 text-sm">
                                        <svg class="w-5 h-5 mr-2 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="line-clamp-2">{{ $event->address }}</span>
                                    </div>

                                    <!-- Tags del Evento -->
                                    @if($event->tags && $event->tags->count() > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($event->tags->take(3) as $tag)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}"
                                           class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                                            {{ $event->space->name }}
                                        </a>
                                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                                           class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 shadow-md hover:shadow-xl transform hover:scale-105">
                                            Ver Evento
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                            <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold underline">ver todos los eventos</a>
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
</script>
@endsection
