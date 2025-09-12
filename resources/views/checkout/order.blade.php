@extends('layouts.app')

@section('title', 'Detalles de la Orden')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Orden #{{ $order->id }}</h1>
            <p class="text-gray-600">Detalles completos de tu compra</p>
        </div>
        <a href="{{ route('dashboard') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
            ← Volver al Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Información de la Orden</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Número de Orden</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">#{{ $order->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Compra</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completada
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Método de Pago</dt>
                            <dd class="mt-1 text-sm text-gray-900">Tarjeta de Crédito</dd>
                        </div>
                        @if($order->coupon)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cupón Aplicado</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->coupon->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Descuento</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($order->discount_amount, 2) }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Items de la Orden</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($order->orderItems as $item)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $item->ticketType->name }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">{{ $item->ticketType->event->name }}</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->ticketType->event->date)->format('d/m/Y H:i') }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->ticketType->event->location }}</p>
                                        @if($item->ticketType->description)
                                            <p class="text-sm text-gray-600 mt-2">{{ $item->ticketType->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">Cantidad: {{ $item->quantity }}</p>
                                        <p class="text-sm text-gray-500">Precio unitario: ${{ number_format($item->price, 2) }}</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-2">${{ number_format($item->quantity * $item->price, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Resumen de Pago</h3>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Subtotal</dt>
                            <dd class="text-sm text-gray-900">${{ number_format($order->subtotal, 2) }}</dd>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500">Descuento</dt>
                            <dd class="text-sm text-green-600">-${{ number_format($order->discount_amount, 2) }}</dd>
                        </div>
                        @endif
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between">
                                <dt class="text-base font-medium text-gray-900">Total</dt>
                                <dd class="text-base font-bold text-gray-900">${{ number_format($order->total, 2) }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- QR Codes -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Códigos QR</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->tickets as $ticket)
                            <div class="text-center">
                                <div class="bg-gray-100 p-4 rounded-lg mb-2">
                                    <div class="text-xs text-gray-500 mb-1">Ticket #{{ $ticket->id }}</div>
                                    <div class="w-20 h-20 mx-auto bg-white rounded border-2 border-gray-200 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">{{ $ticket->ticketType->name }}</p>
                                <p class="text-xs text-gray-400">{{ $ticket->qr_code }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 space-y-3">
                <button onclick="window.print()" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Imprimir Orden
                </button>
                <a href="{{ route('events.public') }}" 
                   class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-center block">
                    Ver Más Eventos
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
