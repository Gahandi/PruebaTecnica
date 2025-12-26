@extends('layouts.app')

@section('title', $category->name . ' - Categoría de Eventos')

@section('content')
<div class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <nav class="flex justify-center mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#e24972] transition-colors">Inicio</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li>
                        <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-[#e24972] transition-colors">Categorías</a>
                    </li>
                    <li>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </li>
                    <li>
                        <span class="text-[#e24972] font-medium">{{ $category->name }}</span>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-bold text-[#e24972] mb-4">{{ $category->name }}</h1>
            <p class="text-lg sm:text-xl text-gray-600">
                {{ $category->events_count }} {{ $category->events_count == 1 ? 'evento disponible' : 'eventos disponibles' }}
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar de Categorías -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#e24972]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        Categorías
                    </h3>
                    <ul class="space-y-2">
                        @foreach($allCategories as $cat)
                            <li>
                                <a href="{{ route('categories.show', $cat->id) }}"
                                   class="flex items-center justify-between py-2 px-3 rounded-lg transition-all duration-200
                                          {{ $cat->id == $category->id 
                                              ? 'bg-gradient-to-r from-pink-500 to-pink-400 text-white shadow-md' 
                                              : 'text-gray-600 hover:bg-gray-100' }}">
                                    <span class="font-medium">{{ $cat->name }}</span>
                                    <span class="text-sm {{ $cat->id == $category->id ? 'text-white/80' : 'text-gray-400' }}">
                                        {{ $cat->events_count }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="flex-1">
                @if($events->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($events as $event)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-2 border-transparent hover:border-pink-200 group h-full flex flex-col">
                                <div class="relative overflow-hidden flex-shrink-0">
                                    @if($event->icon && $event->icon !== 'test.jpg')
                                        <div class="aspect-square w-full">
                                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}" alt="{{ $event->name }}"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        </div>
                                    @else
                                        <div class="aspect-square w-full bg-gradient-to-br from-blue-500 via-purple-500 to-indigo-500 flex items-center justify-center relative overflow-hidden">
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
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="text-lg font-bold text-[#e24972] mb-3 line-clamp-2">{{ $event->name }}</h3>
                                    
                                    <div class="mb-4">
                                        <p class="text-gray-600 mb-2 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                                        </p>
                                        <p class="text-gray-500 text-sm line-clamp-2 flex items-start">
                                            <svg class="w-4 h-4 mr-2 text-pink-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
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

                                    <div class="flex-grow"></div>

                                    <!-- Precio -->
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

                                    <!-- Botón Ver Evento -->
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

                    <!-- Paginación -->
                    @if($events->hasPages())
                        <div class="mt-8">
                            {{ $events->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">No hay eventos en esta categoría</h3>
                        <p class="text-lg text-gray-600 mb-6">Pronto tendremos eventos de {{ $category->name }} para ti.</p>
                        <a href="{{ route('categories.index') }}"
                            class="inline-flex items-center bg-gradient-to-r from-pink-500 to-pink-400 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:from-pink-600 hover:to-pink-500">
                            Ver Otras Categorías
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Back to Categories -->
        <div class="text-center mt-12">
            <a href="{{ route('categories.index') }}"
                class="inline-flex items-center text-gray-600 hover:text-[#e24972] font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver a Categorías
            </a>
        </div>
    </div>
</div>
@endsection
