@extends('layouts.space-dashboard')

@section('page-title', 'Eventos')

@section('content')
    <div class="p-4 md:p-6">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Gestión de Eventos</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $events->total() }} eventos en {{ $space->name }}</p>
                </div>
                <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 transition-all shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Evento
                </a>
            </div>

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Lista de Eventos --}}
            @if($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($events as $event)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            {{-- Imagen del evento --}}
                            <div class="relative h-40">
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" alt="{{ $event->name }}"
                                    class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $event->active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                        {{ $event->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                                @if($event->date < now())
                                    <div
                                        class="absolute bottom-0 left-0 right-0 bg-gray-900 bg-opacity-70 text-white text-center text-xs py-1">
                                        Evento pasado
                                    </div>
                                @endif
                            </div>

                            {{-- Contenido --}}
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 truncate">{{ $event->name }}</h3>
                                <div class="mt-1 flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $event->date->format('d M Y, H:i') }}
                                </div>
                                @if($event->type_event)
                                    <div class="mt-1 text-xs text-gray-400">
                                        {{ $event->type_event->name }}
                                    </div>
                                @endif

                                {{-- Estadísticas --}}
                                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-lg font-bold text-pink-600">{{ $event->orders_count }}</p>
                                        <p class="text-xs text-gray-500">Órdenes</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-lg font-bold text-purple-600">{{ $event->tickets_sold }}</p>
                                        <p class="text-xs text-gray-500">Boletos</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <p class="text-sm font-bold text-green-600">
                                            ${{ number_format($event->total_revenue ?? 0, 0) }}</p>
                                        <p class="text-xs text-gray-500">Ingresos</p>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @php
                                        $daysUntil = now()->diffInDays($event->date, false);
                                        // Se bloquea si falta menos de 4 días y el evento aún no ha pasado
                                        $isLocked = $daysUntil < 4 && $event->date > now();
                                        $isPast = $event->date < now();
                                    @endphp

                                    {{-- Editar / Bloqueado --}}
                                    @if($isLocked)
                                        <button disabled
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed text-sm"
                                            title="No se puede editar: faltan menos de 4 días">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                            Bloqueado
                                        </button>
                                    @else
                                        <a href="{{ route('spaces.events.edit', [$space->subdomain, $event->slug]) }}"
                                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Editar
                                        </a>
                                    @endif

                                    {{-- Ver --}}
                                    <a href="/{{ $event->slug }}" target="_blank"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Ver
                                    </a>

                                    {{-- Duplicar (Solo eventos pasados) --}}
                                    @if($isPast)
                                        <form action="{{ route('spaces.events.duplicate', [$space->subdomain, $event->slug]) }}"
                                            method="POST" class="flex-shrink-0"
                                            onsubmit="return confirm('¿Duplicar este evento? Se creará una copia para que asignes una nueva fecha.');">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center justify-center p-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors"
                                                title="Duplicar Evento">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Eliminar --}}
                                    <form action="{{ route('spaces.events.destroy', [$space->subdomain, $event->slug]) }}"
                                        method="POST" class="flex-shrink-0"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                            title="Eliminar Evento">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Paginación --}}
                @if($events->hasPages())
                    <div class="mt-6 bg-white rounded-xl shadow-lg p-4">
                        {{ $events->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay eventos</h3>
                    <p class="text-gray-500 mb-6">Aún no has creado ningún evento para este espacio.</p>
                    <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 transition-all shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear tu primer evento
                    </a>
                </div>
            @endif

        </div>
    </div>
@endsection