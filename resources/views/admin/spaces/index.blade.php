@extends('layouts.admin')
@section('title', 'Espacios')

@section('admin-content')
<div class="p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        {{-- Encabezado --}}
        <div class="mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Gestión de Espacios</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $spaces->total() }} espacios registrados</p>
        </div>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Lista de Espacios --}}
        <div class="space-y-4">
            @forelse($spaces as $space)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden" x-data="{ open: false }">
                    {{-- Header del Espacio (clickeable) --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 md:p-6 cursor-pointer hover:bg-gray-50 transition-colors"
                         @click="open = !open">
                        <div class="flex items-center">
                            @if($space->logo)
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" 
                                     alt="{{ $space->name }}"
                                     class="w-14 h-14 rounded-xl object-cover mr-4 shadow">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center mr-4 shadow">
                                    <span class="text-white font-bold text-xl">{{ substr($space->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg md:text-xl font-bold text-gray-900">{{ $space->name }}</h2>
                                <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                    @if($space->subdomain)
                                        <span class="flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                            </svg>
                                            {{ $space->subdomain }}
                                        </span>
                                    @endif
                                    <span>•</span>
                                    <span>{{ $space->events_count }} eventos</span>
                                    <span>•</span>
                                    <span>{{ $space->users_count }} usuarios</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center mt-3 md:mt-0 space-x-3">
                            {{-- Estadísticas rápidas --}}
                            <div class="hidden md:flex items-center space-x-4 mr-4">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-pink-600">{{ $space->orders_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-500">Órdenes</p>
                                </div>
                            </div>
                            
                            @if($space->subdomain)
                                <a href="http://{{ $space->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                                   target="_blank"
                                   @click.stop
                                   class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Visitar</span>
                                </a>
                            @endif

                            {{-- Icono de dropdown --}}
                            <div class="text-gray-400">
                                <svg class="w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Contenido desplegable --}}
                    <div x-show="open" x-collapse class="border-t border-gray-200">
                        {{-- Información del espacio --}}
                        <div class="p-4 md:p-6 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                {{-- Info general --}}
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h3 class="font-semibold text-gray-700 mb-2">Información</h3>
                                    <div class="space-y-2 text-sm">
                                        @if($space->location)
                                            <p class="flex items-center text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                </svg>
                                                {{ $space->location }}
                                            </p>
                                        @endif
                                        @if($space->contact_email)
                                            <p class="flex items-center text-gray-600">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $space->contact_email }}
                                            </p>
                                        @endif
                                        <p class="flex items-center text-gray-500">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Creado: {{ $space->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Usuarios --}}
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h3 class="font-semibold text-gray-700 mb-2">Usuarios ({{ $space->users->count() }})</h3>
                                    @if($space->users->count() > 0)
                                        <div class="space-y-2 max-h-32 overflow-y-auto">
                                            @foreach($space->users->take(5) as $user)
                                                <div class="flex items-center text-sm">
                                                    <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                        <span class="text-xs text-gray-600">{{ substr($user->name, 0, 1) }}</span>
                                                    </div>
                                                    <span class="text-gray-700">{{ $user->name }}</span>
                                                </div>
                                            @endforeach
                                            @if($space->users->count() > 5)
                                                <p class="text-xs text-gray-500">+{{ $space->users->count() - 5 }} más</p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">Sin usuarios asignados</p>
                                    @endif
                                </div>

                                {{-- Estadísticas --}}
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <h3 class="font-semibold text-gray-700 mb-2">Estadísticas</h3>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="text-center p-2 bg-gray-50 rounded">
                                            <p class="text-lg font-bold text-pink-600">{{ $space->events_count }}</p>
                                            <p class="text-xs text-gray-500">Eventos</p>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded">
                                            <p class="text-lg font-bold text-purple-600">{{ $space->orders_count ?? 0 }}</p>
                                            <p class="text-xs text-gray-500">Órdenes</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Eventos del espacio --}}
                            @if($space->events->count() > 0)
                                <div class="mt-4">
                                    <h3 class="font-semibold text-gray-700 mb-3">Eventos Recientes</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($space->events->take(6) as $event)
                                            <div class="bg-white rounded-lg p-3 shadow-sm flex items-center justify-between">
                                                <div class="flex items-center min-w-0">
                                                    @if($event->banner)
                                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" 
                                                             class="w-10 h-10 rounded-lg object-cover mr-3 flex-shrink-0">
                                                    @else
                                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center mr-3 flex-shrink-0">
                                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0">
                                                        <p class="font-medium text-gray-900 text-sm truncate">{{ $event->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $event->date->format('d/m/Y') }}</p>
                                                    </div>
                                                </div>
                                                <a href="{{ route('admin.events.show', $event) }}"
                                                   class="ml-2 p-2 text-pink-600 hover:bg-pink-50 rounded-lg transition-colors flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($space->events_count > 6)
                                        <p class="text-center text-sm text-gray-500 mt-3">
                                            +{{ $space->events_count - 6 }} eventos más
                                        </p>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-6 text-gray-500">
                                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm">Este espacio no tiene eventos</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No hay espacios registrados</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        @if($spaces->hasPages())
            <div class="mt-6 bg-white rounded-xl shadow-lg p-4">
                {{ $spaces->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
