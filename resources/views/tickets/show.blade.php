@extends('layouts.app')

@section('title', 'Boleto #' . substr($ticket->id, 0, 8))

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Boleto #{{ substr($ticket->id, 0, 8) }}</h1>
            <p class="text-gray-600">{{ $ticket->eventTicket->event->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tickets.my') }}"
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                ← Mis Boletos
            </a>
            <a href="{{ route('tickets.pdf', $ticket->id) }}"
               class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                📄 Descargar PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ticket Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
            <!-- Event Header -->
            <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white">
                <div class="text-center">
                    <h2 class="text-2xl font-bold">{{ $ticket->ticketType->event->name }}</h2>
                    <p class="text-sm opacity-90">{{ \Carbon\Carbon::parse($ticket->ticketType->event->date)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Ticket Details -->
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tipo de Boleto:</span>
                        <span class="font-semibold">{{ $ticket->ticketType->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Precio:</span>
                        <span class="font-semibold">${{ number_format($ticket->getPrice(), 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Ubicación:</span>
                        <span class="font-semibold">{{ $ticket->ticketType->event->location }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->checkin ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $ticket->checkin ? 'Canjeado' : 'Válido' }}
                        </span>
                    </div>

                    @if($ticket->checkin)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Canjeado el:</span>
                        <span class="font-semibold text-red-600">{{ \Carbon\Carbon::parse($ticket->checkin->scanned_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-gray-600">Comprado:</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="mt-6 text-center">
                    <h3 class="text-lg font-semibold mb-4">Código QR</h3>
                    @if($ticket->qr_url && file_exists(public_path($ticket->qr_url)))
                        @if(pathinfo($ticket->qr_url, PATHINFO_EXTENSION) === 'svg')
                            <div class="w-48 h-48 mx-auto border border-gray-200 rounded-lg flex items-center justify-center">
                                {!! file_get_contents(public_path($ticket->qr_url)) !!}
                            </div>
                        @elseif(pathinfo($ticket->qr_url, PATHINFO_EXTENSION) === 'txt')
                            <div class="w-48 h-48 mx-auto bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                <div class="text-center">
                                    <div class="text-2xl mb-2">📱</div>
                                    <div class="text-sm">QR Code</div>
                                </div>
                            </div>
                        @else
                            <img src="{{ asset($ticket->qr_url) }}" alt="QR Code" class="w-48 h-48 mx-auto border border-gray-200 rounded-lg object-contain">
                        @endif
                        <p class="text-sm text-gray-500 mt-2">Presenta este código QR en la entrada del evento</p>
                    @else
                        <div class="w-48 h-48 mx-auto bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                                <p class="text-sm">QR no disponible</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Event Details -->
        <div class="space-y-6">
            <!-- Event Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold mb-4">Información del Evento</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600 font-medium">Fecha y Hora:</span>
                        <p class="text-lg">{{ \Carbon\Carbon::parse($ticket->ticketType->event->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600 font-medium">Ubicación:</span>
                        <p class="text-lg">{{ $ticket->ticketType->event->location }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600 font-medium">Descripción:</span>
                        <p class="text-gray-700">{{ $ticket->ticketType->event->description ?? 'Sin descripción disponible.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Info -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold mb-4">Información de la Compra</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Orden #:</span>
                        <span class="font-mono text-sm">{{ substr($ticket->order->id, 0, 8) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Comprador:</span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="text-sm">{{ auth()->user()->email }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Fecha de compra:</span>
                        <span>{{ \Carbon\Carbon::parse($ticket->order->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Instrucciones</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Presenta este boleto en la entrada del evento</li>
                    <li>• El código QR será escaneado para validar tu entrada</li>
                    <li>• Llega con anticipación al evento</li>
                    <li>• Guarda una copia de este boleto en tu dispositivo</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
