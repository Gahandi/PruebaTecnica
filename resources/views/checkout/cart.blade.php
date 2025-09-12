@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Carrito de Compras</h1>
            <p class="text-gray-600">Revisa tus boletos antes de proceder al pago</p>
        </div>
        <a href="{{ route('events.public') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
            ← Continuar Comprando
        </a>
    </div>

    @if(empty($cart))
        <!-- Empty Cart -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tu carrito está vacío</h3>
            <p class="text-gray-500 mb-4">Agrega algunos boletos para comenzar tu compra.</p>
            <a href="{{ route('events.public') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Ver Eventos
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Boletos en tu carrito</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($cart as $ticketTypeId => $item)
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $item['ticket_type']->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $item['ticket_type']->event->name }}</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($item['ticket_type']->event->date)->format('d/m/Y H:i') }}</p>
                                        <p class="text-sm text-gray-500">{{ $item['ticket_type']->event->location }}</p>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <p class="text-lg font-semibold text-gray-900">${{ number_format($item['price'], 2) }}</p>
                                            <p class="text-sm text-gray-500">por boleto</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <form method="POST" action="{{ route('checkout.update-cart') }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="ticket_type_id" value="{{ $ticketTypeId }}">
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item['quantity'] }}" 
                                                       min="0" 
                                                       max="{{ $item['ticket_type']->quantity }}"
                                                       class="w-16 px-2 py-1 border border-gray-300 rounded text-center"
                                                       onchange="this.form.submit()">
                                            </form>
                                            <form method="POST" action="{{ route('checkout.remove-from-cart', $ticketTypeId) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Resumen de la Orden</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $subtotal = 0;
                            $totalItems = 0;
                            foreach($cart as $item) {
                                $subtotal += $item['price'] * $item['quantity'];
                                $totalItems += $item['quantity'];
                            }
                        @endphp
                        
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Boletos ({{ $totalItems }})</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($subtotal, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Subtotal</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($subtotal, 2) }}</dd>
                            </div>
                            @php
                                $taxes = $subtotal * 0.16; // 16% IVA
                                $total = $subtotal + $taxes;
                            @endphp
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">IVA (16%)</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($taxes, 2) }}</dd>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between">
                                    <dt class="text-base font-medium text-gray-900">Total</dt>
                                    <dd class="text-base font-medium text-gray-900">${{ number_format($total, 2) }}</dd>
                                </div>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <a href="{{ route('checkout.checkout') }}" 
                               class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-center block">
                                Proceder al Pago
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
