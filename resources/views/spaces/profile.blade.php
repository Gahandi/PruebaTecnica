@extends('layouts.app')

@section('title', $space->name)

@section('content')
<div class="min-h-screen" style="background-color: {{ $space->color_primary }}20;">
    <!-- Header con Banner -->
    <div class="relative">
        @if($space->banner)
            <img src="{{ $space->banner }}" alt="{{ $space->name }}" class="w-full h-64 md:h-80 object-cover">
        @else
            <div class="w-full h-64 md:h-80 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                <div class="text-center text-white">
                    <h1 class="text-4xl font-bold">{{ $space->name }}</h1>
                    <p class="text-xl mt-2">{{ $space->description }}</p>
                </div>
            </div>
        @endif
        
        <!-- Logo del Cajón -->
        <div class="absolute bottom-0 left-8 transform translate-y-1/2">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                @if($space->logo)
                    <img src="{{ $space->logo }}" alt="{{ $space->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-2xl font-bold" style="background-color: {{ $space->color_primary }}; color: white;">
                        {{ substr($space->name, 0, 2) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Columna Izquierda - Información del Cajón -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                    <h2 class="text-2xl font-bold mb-4" style="color: {{ $space->color_primary }};">Acerca de {{ $space->name }}</h2>
                    
                    @if($space->about)
                        <p class="text-gray-700 mb-4">{{ $space->about }}</p>
                    @endif
                    
                    <div class="space-y-3">
                        @if($space->location)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $space->location }}</span>
                            </div>
                        @endif
                        
                        @if($space->website)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                <a href="{{ $space->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $space->website }}</a>
                            </div>
                        @endif
                        
                        @if($space->contact_email)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:{{ $space->contact_email }}" class="text-blue-600 hover:underline">{{ $space->contact_email }}</a>
                            </div>
                        @endif
                        
                        @if($space->contact_phone)
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:{{ $space->contact_phone }}" class="text-blue-600 hover:underline">{{ $space->contact_phone }}</a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estadísticas del Cajón -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold mb-4" style="color: {{ $space->color_primary }};">Estadísticas</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold" style="color: {{ $space->color_primary }};">{{ $space->events->count() }}</div>
                            <div class="text-sm text-gray-600">Eventos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold" style="color: {{ $space->color_primary }};">{{ $space->users->count() }}</div>
                            <div class="text-sm text-gray-600">Miembros</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha - Eventos -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold" style="color: {{ $space->color_primary }};">Eventos de {{ $space->name }}</h2>
                        <div class="flex space-x-3">
                            @auth
                                @if(auth()->user()->spaces->contains($space->id))
                                    <a href="{{ route('spaces.edit', $space->subdomain) }}" 
                                       class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium">
                                        Editar Perfil
                                    </a>
                                    <a href="{{ route('spaces.events.create', $space->subdomain) }}" 
                                       class="px-4 py-2 rounded-lg text-white font-medium"
                                       style="background-color: {{ $space->color_primary }};">
                                        Crear Evento
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    @if($space->events->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($space->events as $event)
                                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                    @if($event->banner)
                                        <img src="{{ asset('storage/' . $event->banner) }}" alt="{{ $event->name }}" class="w-full h-48 object-cover">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center">
                                            <span class="text-white text-lg font-medium">{{ $event->name }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg mb-2">{{ $event->name }}</h3>
                                        <p class="text-gray-600 text-sm mb-3">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-700 text-sm mb-4 line-clamp-2">{{ $event->description }}</p>
                                        
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm text-gray-500">{{ $event->address }}</span>
                                                @if($event->coordinates)
                                                    <p class="text-xs text-gray-400 mt-1">Coordenadas: {{ $event->coordinates }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}" 
                                               class="px-4 py-2 rounded text-white text-sm font-medium"
                                               style="background-color: {{ $space->color_primary }};">
                                                Ver Evento
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No hay eventos aún</h3>
                            <p class="mt-2 text-gray-600">Este cajón aún no tiene eventos programados.</p>
                            @auth
                                @if(auth()->user()->spaces->contains($space->id))
                                    <div class="mt-6">
                                        <a href="{{ route('spaces.events.create', $space->subdomain) }}" 
                                           class="inline-flex items-center px-4 py-2 rounded-lg text-white font-medium"
                                           style="background-color: {{ $space->color_primary }};">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Crear Primer Evento
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
