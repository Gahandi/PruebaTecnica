@extends('layouts.app')

@section('title', 'Orden #' . substr($order->id, 0, 8))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.orders.index') }}" 
                   class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Orden #{{ substr($order->id, 0, 8) }}</h1>
                    <p class="text-gray-600">Detalles de la orden</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('orders.show', $order) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Ver Público
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="lg:col-span-2">
            <!-- Order Details -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información de la Orden</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">ID de Orden</label>
                        <p class="text-lg text-gray-900 font-mono">{{ $order->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha</label>
                        <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Total</label>
                        <p class="text-lg font-semibold text-green-600">${{ number_format($order->total, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Impuestos</label>
                        <p class="text-lg text-gray-900">${{ number_format($order->taxes, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Cliente</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                        <p class="text-lg text-gray-900">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                        <p class="text-lg text-gray-900">{{ $order->user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Rol</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                            {{ $order->user->role }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Event Information -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Evento</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre del Evento</label>
                        <p class="text-lg text-gray-900">{{ $order->event->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha</label>
                        <p class="text-lg text-gray-900">{{ \Carbon\Carbon::parse($order->event->date)->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Ubicación</label>
                        <p class="text-lg text-gray-900">{{ $order->event->location }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Items de la Orden</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $item->ticketType->name }}</h3>
                                    <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Cantidad:</span>
                                            <span class="font-medium">{{ $item->quantity }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Precio unitario:</span>
                                            <span class="font-medium">${{ number_format($item->price, 2) }}</span>
                                        </div>
                                        <div>
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
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de la Orden</h3>
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

            <!-- Tickets -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tickets Generados</h3>
                <div class="space-y-3">
                    @foreach($order->tickets as $ticket)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 font-mono">#{{ substr($ticket->id, 0, 8) }}</div>
                                    <div class="text-xs text-gray-500">
                                        @if($ticket->used)
                                            <span class="text-red-600">Usado</span>
                                        @else
                                            <span class="text-green-600">Disponible</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <a href="{{ $ticket->qr_url }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-900 text-xs">
                                        Ver QR
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('orders.show', $order) }}" 
                       class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                        Ver Orden Pública
                    </a>
                    <a href="{{ route('admin.events.show', $order->event) }}" 
                       class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Ver Evento
                    </a>
                    <button onclick="printOrder()" 
                            class="block w-full bg-gray-600 text-white text-center py-2 px-4 rounded-md hover:bg-gray-700 transition-colors">
                        Imprimir Orden
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printOrder() {
    window.print();
}
</script>
@endpush
@endsection
