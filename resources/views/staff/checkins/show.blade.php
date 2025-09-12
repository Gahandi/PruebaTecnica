@extends('layouts.app')

@section('title', 'Detalle del Check-in - Staff')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Check-in #{{ $checkin->id }}</h1>
            <p class="text-gray-600">Detalles del check-in registrado</p>
        </div>
        <a href="{{ route('staff.checkins.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
            ← Volver
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Check-in Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Información del Check-in</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID del Check-in</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $checkin->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->scanned_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Escaneado por</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->scanned_by ? \App\Models\User::find($checkin->scanned_by)->name : 'Sistema' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Verificado
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Ticket Information -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Información del Boleto</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID del Ticket</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $checkin->ticket->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Código QR</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $checkin->ticket->qr_code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo de Boleto</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->ticket->ticketType->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Precio</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($checkin->ticket->ticketType->price, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Event Information -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Información del Evento</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre del Evento</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->ticket->ticketType->event->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha y Hora</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($checkin->ticket->ticketType->event->date)->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ubicación</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->ticket->ticketType->event->location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID del Evento</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $checkin->ticket->ticketType->event->id }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Información del Cliente</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->ticket->order->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->ticket->order->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID de Usuario</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $checkin->ticket->order->user->id }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Order Information -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Información de la Orden</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID de la Orden</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $checkin->ticket->order->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Compra</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $checkin->ticket->order->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Pagado</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($checkin->ticket->order->total, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completada
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- QR Code -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Código QR</h3>
                </div>
                <div class="p-6 text-center">
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <div class="w-32 h-32 mx-auto bg-white rounded border-2 border-gray-200 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $checkin->ticket->qr_code }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 space-y-3">
                @can('delete checkins')
                <form method="POST" action="{{ route('staff.checkins.destroy', $checkin) }}" 
                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este check-in?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                        Eliminar Check-in
                    </button>
                </form>
                @endcan
                <button onclick="window.print()" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Imprimir Detalles
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
