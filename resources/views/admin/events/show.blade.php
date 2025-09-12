@extends('layouts.app')

@section('title', 'Detalle del Evento')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
            <p class="text-gray-600">Detalles del evento</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.events.edit', $event) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Editar
            </a>
            <a href="{{ route('admin.events.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                ← Volver
            </a>
        </div>
    </div>

    <!-- Event Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Información del Evento</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $event->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ubicación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $event->location }}</dd>
                        </div>
                        @if($event->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $event->description }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Creado</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $event->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="space-y-6">
            <!-- Estadísticas Generales -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Estadísticas</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Tipos de Boletos</dt>
                            <dd class="text-sm text-gray-900">{{ $event->ticketTypes->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Órdenes</dt>
                            <dd class="text-sm text-gray-900">{{ $event->orders->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Tickets Vendidos</dt>
                            <dd class="text-sm text-gray-900">{{ $event->tickets->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Ingresos</dt>
                            <dd class="text-sm text-gray-900">
                                ${{ number_format($event->orders->sum('total'), 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Acciones</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.ticket-types.index', ['event_id' => $event->id]) }}" 
                       class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-center block">
                        Gestionar Tipos de Boletos
                    </a>
                    <a href="{{ route('admin.orders.index', ['event_id' => $event->id]) }}" 
                       class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors text-center block">
                        Ver Órdenes
                    </a>
                    <a href="{{ route('admin.checkins.index', ['event_id' => $event->id]) }}" 
                       class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors text-center block">
                        Check-ins
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tipos de Boletos -->
    @if($event->ticketTypes->count() > 0)
    <div class="mt-6">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tipos de Boletos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendidos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($event->ticketTypes as $ticketType)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $ticketType->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($ticketType->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticketType->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $ticketType->tickets->count() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection