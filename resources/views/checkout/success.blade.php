@extends('layouts.app')

@section('title', 'Compra Exitosa')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Success Message -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">¡Compra Exitosa!</h1>
        <p class="text-gray-600">Tu orden ha sido procesada correctamente. Aquí están los detalles:</p>
    </div>

    <!-- Order Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Detalles de la Orden #{{ $order->id }}</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6">
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
                        @if($order->coupon)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cupón Aplicado</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->coupon->code }} ({{ $order->coupon->discount_percentage }}% descuento)</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Pagado</dt>
                            <dd class="mt-1 text-sm text-gray-900 text-lg font-semibold">${{ number_format($order->total, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Tickets -->
            <div class="mt-6 bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Tickets Comprados</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $item->ticketType->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $item->ticketType->event->name }}</p>
                                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->ticketType->event->date)->format('d/m/Y H:i') }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->ticketType->event->location }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-900">{{ $item->quantity }} x ${{ number_format($item->price, 2) }}</p>
                                        <p class="text-lg font-semibold text-gray-900">${{ number_format($item->quantity * $item->price, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Codes -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Códigos QR</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($order->tickets as $ticket)
                            <div class="text-center">
                                <div class="bg-gray-100 p-4 rounded-lg mb-2">
                                    <div class="text-xs text-gray-500 mb-1">Ticket #{{ $ticket->id }}</div>
                                    <div class="w-24 h-24 mx-auto bg-white rounded border-2 border-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500">{{ $ticket->ticketType->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 space-y-3">
                <a href="{{ route('checkout.order', $order) }}" 
                   class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-center block">
                    Ver Detalles Completos
                </a>
                <a href="{{ route('events.public') }}" 
                   class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-center block">
                    Ver Más Eventos
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-center block">
                    Ir al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Important Information -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <svg class="w-5 h-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-800">Información Importante</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Guarda este código QR en tu dispositivo móvil</li>
                        <li>Presenta el código QR en la entrada del evento</li>
                        <li>Los tickets son válidos solo para la fecha y hora especificada</li>
                        <li>No se permiten reembolsos después de la compra</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Invalidar cache del carrito después de una compra exitosa
    if (typeof window.invalidateCartCache === 'function') {
        window.invalidateCartCache();
    }
    // Actualizar contador del carrito
    if (typeof window.updateCartCount === 'function') {
        window.updateCartCount();
    }
</script>
@endpush
@endsection
