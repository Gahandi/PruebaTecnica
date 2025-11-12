@extends('layouts.app')

@section('title', 'Mis Boletos')

@section('content')
<script src=
"https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js">
    </script>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Boletos</h1>
            <p class="text-gray-600">Gestiona y descarga tus boletos</p>
        </div>
    </div>

    @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                    <!-- Order Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4 text-white">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-bold">Orden #{{ substr($order->id, 0, 8) }}</h3>
                                <p class="text-sm opacity-90">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold">${{ number_format($order->total, 2) }}</div>
                                <div class="text-sm opacity-90">{{ $order->tickets->count() }} boleto(s)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Order Status -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Estado</h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $order->status === 'completed' ? 'Pagado' : ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Tickets Grid -->
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-4">Tus Boletos</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($order->tickets as $index => $ticket)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <!-- Ticket Header -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h5 class="font-medium text-gray-900">{{ $ticket->ticketType->name }}</h5>
                                                @php
                                                    $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $ticket->ticket_types_id)
                                                        ->where('event_id', $ticket->event_id)
                                                        ->first();
                                                @endphp
                                                @if($ticketEvent)
                                                    <p class="text-sm text-gray-500">${{ number_format($ticketEvent->price, 2) }}</p>
                                                @else
                                                    <p class="text-sm text-gray-500">${{ number_format($ticket->getPrice(), 2) }}</p>
                                                @endif
                                                @if($ticket->eventTicket)
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        <a href="{{ route('events.show', $ticket->eventTicket->id) }}" 
                                                           class="text-blue-600 hover:text-blue-800 underline">
                                                            {{ $ticket->eventTicket->name }}
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $ticket->used ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $ticket->used ? 'Canjeado' : 'Válido' }}
                                            </span>
                                        </div>

                                        <!-- QR Code Preview -->
                                        <div class="mb-3 text-center">
                                            <div id="qrcode-{{ $ticket->id }}" class="inline-block p-2 border-2 border-gray-300 rounded-lg"></div>
                                        </div>
                                        
                                        <!-- Ticket ID -->
                                        <div class="text-center mb-3">
                                            <p class="text-xs text-gray-500">ID: {{ substr($ticket->id, 0, 8) }}</p>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tickets.show', $ticket->id) }}"
                                               class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-xs">
                                                Ver
                                            </a>
                                            <a href="{{ route('tickets.pdf', $ticket->id) }}"
                                               class="flex-1 bg-red-600 text-white text-center px-3 py-2 rounded-md hover:bg-red-700 transition-colors text-xs">
                                                PDF
                                            </a>
                                        </div>
                                        
                                        <!-- Event Link -->
                                        @if($ticket->eventTicket)
                                            <div class="mt-2">
                                                <a href="{{ route('events.show', $ticket->eventTicket->id) }}" 
                                                   class="w-full bg-green-600 text-white text-center px-3 py-2 rounded-md hover:bg-green-700 transition-colors text-xs block">
                                                    Ver Evento
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes órdenes</h3>
            <p class="text-gray-500 mb-4">Compra algunos boletos para ver tus órdenes aquí.</p>
            <a href="{{ route('events.public') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Ver Eventos
            </a>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generar QR codes únicos para cada ticket
    @foreach($orders as $order)
        @foreach($order->tickets as $ticket)
            setTimeout(function() {
                try {
                    var qrElement = document.getElementById("qrcode-{{ $ticket->id }}");
                    if (qrElement) {
                        new QRCode(qrElement, {
                            text: "{{ $ticket->id }}",
                            width: 120,
                            height: 120,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    }
                } catch (error) {
                    console.error('Error generating QR code for ticket {{ $ticket->id }}:', error);
                }
            }, {{ $loop->index * 100 }});
        @endforeach
    @endforeach
});
</script>
@endsection
