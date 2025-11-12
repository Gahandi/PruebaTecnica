@extends('layouts.app')

@section('title', 'Boleto #' . substr($ticket->id, 0, 8))

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Boleto #{{ substr($ticket->id, 0, 8) }}</h1>
            <p class="text-gray-600">{{ $ticket->eventTicket->name }}</p>
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
                    <h2 class="text-2xl font-bold">{{ $ticket->eventTicket->name }}</h2>
                    <p class="text-sm opacity-90">{{ \Carbon\Carbon::parse($ticket->eventTicket->date)->format('d/m/Y H:i') }}</p>
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
                        <span class="font-semibold">{{ $ticket->eventTicket->address }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600">Estado:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ticket->used ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $ticket->used ? 'Canjeado' : 'Válido' }}
                        </span>
                    </div>

                    @if($ticket->used)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Canjeado el:</span>
                        <span class="font-semibold text-red-600">{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d/m/Y H:i') }}</span>
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
                    <div class="w-48 h-48 mx-auto border-2 border-gray-300 rounded-lg flex items-center justify-center">
                        <div id="qrcode-{{ $ticket->id }}" class="inline-block"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Presenta este código QR en la entrada del evento</p>
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
                        <p class="text-lg">{{ \Carbon\Carbon::parse($ticket->eventTicket->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600 font-medium">Ubicación:</span>
                        <p class="text-lg">{{ $ticket->eventTicket->address }}</p>
                    </div>

                    <div>
                        <span class="text-gray-600 font-medium">Descripción:</span>
                        <p class="text-gray-700">{{ $ticket->eventTicket->description ?? 'Sin descripción disponible.' }}</p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generar QR code para el ticket
    setTimeout(function() {
        try {
            var qrElement = document.getElementById("qrcode-{{ $ticket->id }}");
            if (qrElement) {
                new QRCode(qrElement, {
                    text: "{{ $ticket->id) }}",
                    width: 180,
                    height: 180,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        } catch (error) {
            console.error('Error generating QR code for ticket {{ $ticket->id }}:', error);
        }
    }, 100);
});
</script>
@endsection
