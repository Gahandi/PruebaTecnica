@extends('layouts.app')

@section('title', 'Mis Boletos')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 shadow-lg">
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
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Mis Boletos</h1>
            <p class="text-gray-600 text-lg">Gestiona y descarga tus boletos</p>
        </div>

        @if($orders->count() > 0)
            <!-- Orders List -->
            <div class="space-y-8">
                @foreach($orders as $order)
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-2 border-gray-100 hover:shadow-2xl transition-all duration-300">
                        <!-- Order Header -->
                        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 px-6 py-6 text-white relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M20 20.5V18H0v-2h20v-2H0v-2h20v-2H0V8h20V6H0V4h20V2H0V0h22v20h2V0h2v20h2V0h2v20h2V0h2v20h2V0h2v22H20v-1.5zM0 20h2v20H0V20zm4 0h2v20H4V20zm4 0h2v20H8V20zm4 0h2v20h-2V20zm4 0h2v20h-2V20zm4 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2zm0 4h20v2H20v-2z\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                            <div class="relative z-10 flex justify-between items-start">
                                <div>
                                    <h3 class="text-2xl font-bold mb-2">Orden #{{ substr($order->id, 0, 8) }}</h3>
                                    <div class="flex items-center space-x-4 text-sm opacity-90">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            {{ $order->tickets->count() }} boleto(s)
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $payment = $order->payments->first();
                                        $orderTotal = $payment ? $payment->total : 0;
                                    @endphp
                                    <div class="text-3xl font-bold mb-1">${{ number_format($orderTotal, 2) }}</div>
                                    <div class="text-sm opacity-90">Total pagado</div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="p-6">
                            <!-- Order Status and Event Info -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <!-- Status -->
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                                    <h4 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wide">Estado</h4>
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $order->status === 'completed' ? 'Pagado' : ucfirst($order->status) }}
                                    </span>
                                </div>

                                <!-- Event Info -->
                                @if($order->event)
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                                        <h4 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wide">Evento</h4>
                                        <p class="font-bold text-gray-900 text-lg mb-1">{{ $order->event->name }}</p>
                                        @if($order->event->space)
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                                {{ $order->event->space->name }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Time Until Event -->
                                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200">
                                        <h4 class="font-semibold text-gray-700 mb-2 text-sm uppercase tracking-wide">Tiempo Restante</h4>
                                        @php
                                            $eventDate = \Carbon\Carbon::parse($order->event->date);
                                            $now = \Carbon\Carbon::now();
                                            $diff = $now->diffForHumans($eventDate, true);
                                            $isPast = $eventDate->isPast();
                                        @endphp
                                        @if($isPast)
                                            <p class="text-red-600 font-bold text-lg">Evento Finalizado</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $eventDate->format('d M Y, H:i') }}</p>
                                        @else
                                            <p class="font-bold text-purple-800 text-lg">{{ $diff }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $eventDate->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Tickets Grid -->
                            <div class="mb-6">
                                <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    Tus Boletos
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($order->tickets as $ticket)
                                        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-xl p-5 hover:shadow-xl hover:border-indigo-300 transition-all duration-300 transform hover:-translate-y-1">
                                            <!-- Ticket Header -->
                                            <div class="flex justify-between items-start mb-4">
                                                <div class="flex-1">
                                                    <h5 class="font-bold text-gray-900 text-lg mb-1">{{ $ticket->ticketType->name }}</h5>
                                                    @if(isset($ticket->real_price) && $ticket->real_price > 0)
                                                        <p class="text-xl font-bold text-green-600 mb-2">${{ number_format($ticket->real_price, 2) }}</p>
                                                    @else
                                                        @php
                                                            $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $ticket->ticket_types_id)
                                                                ->where('event_id', $ticket->event_id)
                                                                ->first();
                                                        @endphp
                                                        @if($ticketEvent)
                                                            <p class="text-xl font-bold text-green-600 mb-2">${{ number_format($ticketEvent->price, 2) }}</p>
                                                        @else
                                                            <p class="text-sm text-gray-500 mb-2">Precio no disponible</p>
                                                        @endif
                                                    @endif
                                                    
                                                    @if($ticket->eventTicket)
                                                        <div class="mb-2">
                                                            <p class="text-sm font-semibold text-gray-700 mb-1">Evento:</p>
                                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($ticket->eventTicket->space->subdomain ?? '') }}/{{ $ticket->eventTicket->slug }}" 
                                                               class="text-blue-600 hover:text-blue-800 font-medium text-sm underline">
                                                                {{ $ticket->eventTicket->name }}
                                                            </a>
                                                        </div>
                                                        @if($ticket->eventTicket->space)
                                                            <div class="mb-2">
                                                                <p class="text-sm font-semibold text-gray-700 mb-1">Espacio:</p>
                                                                <p class="text-sm text-gray-600">{{ $ticket->eventTicket->space->name }}</p>
                                                            </div>
                                                        @endif
                                                        @if($ticket->eventTicket->date)
                                                            <div class="mb-2">
                                                                <p class="text-sm font-semibold text-gray-700 mb-1">Fecha:</p>
                                                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($ticket->eventTicket->date)->format('d M Y, H:i') }}</p>
                                                            </div>
                                                            @if($ticket->eventTicket->address)
                                                                <div class="mb-2">
                                                                    <p class="text-sm font-semibold text-gray-700 mb-1">Ubicación:</p>
                                                                    <p class="text-sm text-gray-600">{{ $ticket->eventTicket->address }}</p>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $ticket->used || ($ticket->checkin && $ticket->checkin->count() > 0) ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                    @if($ticket->used || ($ticket->checkin && $ticket->checkin->count() > 0))
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8 10.414l-1.293 1.293a1 1 0 101.414 1.414L9.414 12l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586l-1.293-1.293z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Canjeado
                                                    @else
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Válido
                                                    @endif
                                                </span>
                                            </div>

                                            <!-- QR Code Preview -->
                                            <div class="mb-4 text-center bg-white p-3 rounded-lg border-2 border-gray-200">
                                                <div id="qrcode-{{ $ticket->id }}" class="inline-block"></div>
                                            </div>
                                            
                                            <!-- Ticket ID -->
                                            <div class="text-center mb-4">
                                                <p class="text-xs text-gray-500 font-mono">ID: {{ substr($ticket->id, 0, 8) }}</p>
                                            </div>

                                            <!-- Actions -->
                                            <div class="grid grid-cols-2 gap-2">
                                                <a href="{{ route('tickets.show', $ticket->id) }}"
                                                   class="bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center px-4 py-2.5 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-semibold text-sm flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    Ver
                                                </a>
                                                <a href="{{ route('tickets.pdf', $ticket->id) }}"
                                                   class="bg-gradient-to-r from-red-600 to-red-700 text-white text-center px-4 py-2.5 rounded-lg hover:from-red-700 hover:to-red-800 transition-all duration-200 font-semibold text-sm flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    PDF
                                                </a>
                                            </div>
                                            
                                            <!-- Event Link -->
                                            @if($ticket->eventTicket)
                                                <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($ticket->eventTicket->space->subdomain ?? '') }}/{{ $ticket->eventTicket->slug }}" 
                                                   class="mt-2 w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white text-center px-4 py-2.5 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 font-semibold text-sm block">
                                                    Ver Evento Completo
                                                </a>
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
            <div class="text-center py-16 bg-white rounded-2xl shadow-xl">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No tienes boletos</h3>
                <p class="text-gray-600 mb-6">Compra algunos boletos para ver tus órdenes aquí.</p>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Ver Eventos
                </a>
            </div>
        @endif
    </div>
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
                            width: 150,
                            height: 150,
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
