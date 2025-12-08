@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<div class="max-w-7xl mx-auto py-4 sm:py-6 px-2 sm:px-4 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-3 sm:gap-0">
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-[#e24972]">Carrito de Compras</h1>
            <p class="text-sm sm:text-base text-gray-800 mt-1">Revisa tus boletos antes de proceder al pago</p>
        </div>
        <a href="{{ route('events.public') }}"
           class="w-full sm:w-auto bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-center text-sm sm:text-base whitespace-nowrap">
            ← Continuar Comprando
        </a>
    </div>

    @if(empty($cart))
        <!-- Empty Cart -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-14 h-14 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tu carrito está vacío</h3>
            <p class="text-gray-500 mb-4">Agrega algunos boletos para comenzar tu compra.</p>
            <a href="{{ route('events.public') }}"
               class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-400 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Ver Eventos
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 order-2 lg:order-1">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-3 py-3 sm:px-6 sm:py-4 border-b border-gray-200">
                        <h2 class="text-lg sm:text-xl font-semibold text-[#e24972]">Boletos en tu carrito</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($cart as $key => $item)
                            <div class="p-3 sm:p-4 lg:p-6">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                                    <!-- Event Image -->
                                    <div class="flex items-start gap-3 sm:gap-4 w-full sm:w-auto">
                                    @if(isset($item['event_image']) && $item['event_image'])
                                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($item['event_image']) }}" alt="{{ $item['event_name'] ?? 'Evento' }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-lg object-cover flex-shrink-0">
                                    @else
                                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <!-- Event Info -->
                                        <div class="mb-2">
                                                <h3 class="text-base sm:text-lg font-medium text-gray-900">{{ $item['event_name'] ?? 'Evento' }}</h3>
                                            @if(isset($item['event_date']))
                                                    <p class="text-xs sm:text-sm text-gray-500">{{ \Carbon\Carbon::parse($item['event_date'])->format('d M Y, H:i') }}</p>
                                            @endif
                                        </div>

                                        <!-- Ticket Type Info -->
                                            <div class="mb-2 sm:mb-3">
                                                <h4 class="text-sm sm:text-base font-semibold text-gray-800">{{ $item['ticket_type_name'] ?? 'Tipo de Boleto' }}</h4>
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                                        </svg>
                                                        {{ $item['ticket_type_name'] ?? 'Boleto' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price and Controls -->
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full sm:w-auto gap-3 sm:gap-4">
                                        <div class="text-left sm:text-right">
                                            @if(isset($item['price']) && isset($item['quantity']))
                                                <p class="text-base sm:text-lg font-semibold text-gray-900">${{ number_format($item['price'], 2) }}</p>
                                                <p class="text-xs sm:text-sm text-gray-500">por boleto</p>
                                            <p class="text-sm font-medium text-gray-700">Total: ${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                            @else
                                                <p class="text-xs sm:text-sm text-red-500">Error: Datos incompletos</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <form method="POST" action="{{ route('checkout.update-cart') }}" class="inline" id="update-form-{{ $key }}">
                                                @csrf
                                                <input type="hidden" name="cart_key" value="{{ $key }}">
                                                <input type="number"
                                                       name="quantity"
                                                       value="{{ $item['quantity'] ?? 0 }}"
                                                       min="0"
                                                       max="10"
                                                       class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm sm:text-base"
                                                       onchange="updateCartItem('{{ $key }}', this.value)">
                                            </form>
                                            <button type="button" onclick="removeCartItem('{{ $key }}')" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1 order-1 lg:order-2">
                <div class="bg-white shadow-lg rounded-lg overflow-hidden sticky top-20">
                    <div class="px-3 py-3 sm:px-6 sm:py-4 border-b border-gray-200">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Resumen de la Orden</h2>
                    </div>
                    <div class="p-4 sm:p-6">
                        @php
                            $subtotal = 0;
                            $totalItems = 0;
                            foreach($cart as $item) {
                                if (isset($item['price']) && isset($item['quantity'])) {
                                $subtotal += $item['price'] * $item['quantity'];
                                $totalItems += $item['quantity'];
                            }
                            }
                                $taxes = $subtotal * 0.16; // 16% IVA
                                $total = $subtotal + $taxes;
                            @endphp

                        <div class="text-center">
                            <div class="mb-4">
                                <p class="text-xs sm:text-sm text-gray-500 mb-2">Total a pagar</p>
                                <p class="text-3xl sm:text-4xl font-bold text-gray-900">${{ number_format($total, 2) }}</p>
                                <p class="text-xs text-gray-400 mt-1">IVA incluido</p>
                                </div>
                            </div>

                        <div class="mt-4 sm:mt-6">
                            <a href="{{ route('checkout.checkout') }}"
                               class="w-full bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink:500 text-white px-4 py-3 rounded-md hover:bg-blue-700 transition-colors text-center block text-sm sm:text-base font-medium">
                                Proceder al Pago
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    async function updateCartItem(cartKey, quantity) {
        const form = document.getElementById('update-form-' + cartKey);
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route("checkout.update-cart") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                // Invalidar cache
                if (typeof window.invalidateCartCache === 'function') {
                    window.invalidateCartCache();
                }
                // Actualizar contador del carrito
                if (typeof window.updateCartCount === 'function') {
                    await window.updateCartCount();
                }
                // Recargar la página para mostrar los cambios
                window.location.reload();
            } else {
                alert(data.message || 'Error al actualizar el carrito');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar el carrito');
        }
    }

    async function removeCartItem(cartKey) {
        if (!confirm('¿Estás seguro de que deseas eliminar este item del carrito?')) {
            return;
        }

        try {
            // Eliminar del servidor (usar encodeURIComponent para la clave)
            const response = await fetch('{{ route("checkout.remove-from-cart", ":key") }}'.replace(':key', encodeURIComponent(cartKey)), {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                // Invalidar cache
                if (typeof window.invalidateCartCache === 'function') {
                    window.invalidateCartCache();
                }
                // Actualizar contador del carrito
                if (typeof window.updateCartCount === 'function') {
                    await window.updateCartCount();
                }
                // Recargar la página para mostrar los cambios
                window.location.reload();
            } else {
                console.error('Error del servidor:', data);
                console.error('Clave buscada:', cartKey);
                if (data.cart_keys) {
                    console.error('Claves disponibles:', data.cart_keys);
                }
                alert(data.message || 'Error al eliminar el item');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar el item: ' + error.message);
        }
    }
</script>
@endpush
@endsection

@push('scripts')
<script>
    // Actualizar contador del carrito al cargar la página
    document.addEventListener('DOMContentLoaded', async function() {
        if (typeof window.updateCartCount === 'function') {
            await window.updateCartCount();
        }
    });
</script>
@endpush
