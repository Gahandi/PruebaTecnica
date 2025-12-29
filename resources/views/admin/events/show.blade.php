@extends('layouts.admin')
@section('title', $event->name . ' - Detalles')

@section('admin-content')
<div class="p-4 md:p-6">
    <div class="max-w-7xl mx-auto">

        {{-- Navegación --}}
        <div class="mb-6">
            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver a Eventos
            </a>
        </div>

        {{-- Header del Evento --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="md:flex">
                {{-- Imagen del evento --}}
                <div class="md:w-1/3">
                    @if($event->banner)
                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" 
                             alt="{{ $event->name }}"
                             class="w-full h-48 md:h-full object-cover">
                    @else
                        <div class="w-full h-48 md:h-full bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Información del evento --}}
                <div class="md:w-2/3 p-6">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-4">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
                            <div class="flex items-center mt-2 text-gray-600">
                                @if($event->space)
                                    <span class="flex items-center mr-4">
                                        @if($event->space->logo)
                                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->space->logo) }}" class="w-5 h-5 rounded mr-1">
                                        @endif
                                        {{ $event->space->name }}
                                    </span>
                                @endif
                                @if($event->type_event)
                                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                        {{ $event->type_event->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($event->active)
                                <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700 font-medium">
                                    Activo
                                </span>
                            @else
                                <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-700 font-medium">
                                    Inactivo
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-pink-600">{{ $stats['total_orders'] }}</p>
                            <p class="text-xs text-gray-500">Órdenes</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_tickets'] }}</p>
                            <p class="text-xs text-gray-500">Boletos Vendidos</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">${{ number_format($stats['total_revenue'], 2) }}</p>
                            <p class="text-xs text-gray-500">Ingresos</p>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-indigo-600">{{ $stats['checked_in'] }}</p>
                            <p class="text-xs text-gray-500">Check-ins</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Fecha:</span> 
                            {{ $event->date->format('d/m/Y H:i') }}
                        </div>
                        @if($event->location)
                            <span class="text-gray-300">|</span>
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Ubicación:</span> 
                                {{ $event->location }}
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('events.show', $event) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Ver Público
                        </a>
                        @if($event->space && $event->space->subdomain && $event->slug)
                            <a href="http://{{ $event->space->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}/eventos/{{ $event->slug }}/editar" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar Evento
                            </a>
                        @endif
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este evento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Tipos de Boletos --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-4 py-3">
                        <h2 class="text-lg font-bold text-white">Tipos de Boletos</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($ticketTypes as $ticket)
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-900">{{ $ticket['name'] }}</h3>
                                    <span class="text-sm font-bold text-pink-600">${{ number_format($ticket['price'], 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Vendidos: {{ $ticket['sold'] }} / {{ $ticket['quantity'] }}</span>
                                    <span class="{{ $ticket['available'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $ticket['available'] }} disponibles
                                    </span>
                                </div>
                                <div class="mt-2 bg-gray-200 rounded-full h-2">
                                    @php
                                        $percent = $ticket['quantity'] > 0 ? ($ticket['sold'] / $ticket['quantity']) * 100 : 0;
                                    @endphp
                                    <div class="bg-pink-500 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-4">
                                No hay tipos de boletos configurados
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Órdenes Recientes --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-pink-500 to-rose-500 px-4 py-3">
                        <h2 class="text-lg font-bold text-white">Órdenes del Evento</h2>
                    </div>

                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                #{{ $order->id }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $order->user->name ?? 'N/A' }} {{ $order->user->last_name ?? '' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                ${{ number_format($order->total, 2) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @switch($order->status)
                                                    @case('completed')
                                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Completada</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pendiente</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Cancelada</span>
                                                        @break
                                                    @default
                                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">{{ $order->status }}</span>
                                                @endswitch
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-500">
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-4 border-t bg-gray-50">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>Este evento no tiene órdenes aún</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection