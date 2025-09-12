@extends('layouts.app')

@section('title', 'Orden #' . substr($order->id, 0, 8))

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">¡Orden Confirmada!</h1>
        <p class="text-gray-600">Tu orden ha sido procesada exitosamente</p>
    </div>

    <!-- Order Summary Card -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Orden #{{ substr($order->id, 0, 8) }}</h2>
                <p class="text-gray-600">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-green-600">${{ number_format($order->total, 2) }}</div>
                <div class="text-sm text-gray-500">Total pagado</div>
            </div>
        </div>

        <!-- Event Information -->
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Evento</h3>
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $order->event->name }}</h4>
                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($order->event->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
                    <p class="text-gray-500">{{ $order->event->location }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Items -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Boletos Comprados</h3>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">{{ $item->ticketType->name }}</h4>
                                <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Cantidad:</span>
                                        <span class="font-medium">{{ $item->quantity }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Precio unitario:</span>
                                        <span class="font-medium">${{ number_format($item->price, 2) }}</span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-500">Subtotal:</span>
                                        <span class="font-medium text-green-600">${{ number_format($item->quantity * $item->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Pago</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">${{ number_format($order->total - $order->taxes, 2) }}</span>
                </div>
                @if($order->coupon)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Descuento ({{ $order->coupon->code }}):</span>
                        <span class="font-medium text-red-600">-${{ number_format(($order->total - $order->taxes) * ($order->coupon->discount_percentage / 100), 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">IVA (16%):</span>
                    <span class="font-medium">${{ number_format($order->taxes, 2) }}</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between font-semibold text-lg">
                        <span>Total:</span>
                        <span class="text-green-600">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets -->
    <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tus Tickets</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($order->tickets as $ticket)
                <div class="border border-gray-200 rounded-lg p-4 text-center">
                    <div class="mb-4">
                        <img src="{{ $ticket->qr_url }}" alt="QR Code" class="w-32 h-32 mx-auto">
                    </div>
                    <div class="text-sm font-medium text-gray-900 font-mono">#{{ substr($ticket->id, 0, 8) }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        @if($ticket->used)
                            <span class="text-red-600">Usado</span>
                        @else
                            <span class="text-green-600">Válido</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        <a href="{{ $ticket->qr_url }}" target="_blank" 
                           class="text-blue-600 hover:text-blue-900 text-sm">
                            Descargar QR
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">Instrucciones</h3>
        <ul class="space-y-2 text-blue-800">
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Guarda este código de orden para futuras referencias
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Presenta el código QR en la entrada del evento
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Cada ticket es válido para una sola persona
            </li>
            <li class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Llega al evento con anticipación
            </li>
        </ul>
    </div>

    <!-- Actions -->
    <div class="flex justify-center space-x-4 mt-8">
        <a href="{{ route('events.public') }}" 
           class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
            Ver Más Eventos
        </a>
        <button onclick="window.print()" 
                class="bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-gray-700 transition-colors">
            Imprimir Orden
        </button>
    </div>
</div>
@endsection
