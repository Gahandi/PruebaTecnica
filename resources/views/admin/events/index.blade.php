@extends('layouts.admin')
@section('title', 'Eventos')

@section('admin-content')
<div class="p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        {{-- Encabezado --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Gestión de Eventos</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $totalEvents }} eventos en {{ $totalSpaces }} espacios</p>
        </div>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Lista de Espacios con sus Eventos --}}
        @forelse($spaces as $space)
            <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
                {{-- Header del Espacio --}}
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-4 md:px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center">
                            @if($space->logo)
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" 
                                     alt="{{ $space->name }}"
                                     class="w-10 h-10 rounded-lg object-cover mr-3">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-pink-500 flex items-center justify-center mr-3">
                                    <span class="text-white font-bold">{{ substr($space->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg font-bold text-white">{{ $space->name }}</h2>
                                <p class="text-gray-300 text-sm">{{ $space->events_count }} eventos</p>
                            </div>
                        </div>
                        @if($space->subdomain)
                            <a href="http://{{ $space->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                               target="_blank"
                               class="mt-2 md:mt-0 inline-flex items-center text-sm text-gray-300 hover:text-white">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Ir al espacio
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Eventos del Espacio --}}
                @if($space->events->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4 md:p-6">
                        @foreach($space->events as $event)
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                {{-- Imagen y estado --}}
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center">
                                        @if($event->banner)
                                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" 
                                                 alt="{{ $event->name }}"
                                                 class="w-12 h-12 rounded-lg object-cover mr-3">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center mr-3">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $event->name }}</h3>
                                            <p class="text-xs text-gray-500">
                                                {{ $event->date->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($event->active)
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Activo</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactivo</span>
                                    @endif
                                </div>

                                {{-- Estadísticas rápidas --}}
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        {{ $event->orders_count ?? 0 }} órdenes
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        {{ $event->tickets_events_count ?? 0 }} tipos
                                    </span>
                                </div>

                                {{-- Acciones --}}
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.events.show', $event) }}"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-pink-500 text-white text-sm font-medium rounded-lg hover:bg-pink-600 transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Detalles
                                    </a>
                                    <a href="{{ route('events.show', $event) }}" target="_blank"
                                       class="inline-flex items-center justify-center p-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                                       title="Ver público">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Ver más si hay más de 10 eventos --}}
                    @if($space->events_count > 10)
                        <div class="px-6 py-3 bg-gray-50 border-t text-center">
                            <span class="text-sm text-gray-500">
                                Mostrando 10 de {{ $space->events_count }} eventos
                            </span>
                        </div>
                    @endif
                @else
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>Este espacio no tiene eventos</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="text-lg font-medium text-gray-500">No hay espacios registrados</p>
                <p class="text-sm text-gray-400 mt-1">Los espacios y sus eventos aparecerán aquí</p>
            </div>
        @endforelse

    </div>
</div>
@endsection