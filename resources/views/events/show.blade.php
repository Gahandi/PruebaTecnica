
@extends('layouts.app')

@section('title', $event->name . ' - ' . $space->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 ">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8 ">
        <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
            <div class="text-center text-white">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        @else
            <div class="h-[60vh] min-h-[500px] bg-gradient-to-br from-blue-500 via-purple-600 to-indigo-700 flex items-center justify-center relative">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative z-10 text-center text-white px-8">
                    <div class="backdrop-blur-lg bg-black/20 rounded-2xl p-8 border border-white/30 shadow-2xl">
                        <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-light mb-4 tracking-wide">{{ $event->name }}</h1>
                        <p class="text-lg text-white/90 mb-6">por {{ $space->name }}</p>
                        @if($event->ticketTypes->count() > 0)
                            <div class="inline-block bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-4 py-2">
                                <p class="text-white text-xs font-medium mb-1">Desde</p>
                                <p class="text-white text-lg font-semibold">${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="relative z-10 -mt-32 max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
        
        <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-8 border border-white/30 shadow-xl">
            <h2 class="text-2xl font-semibold text-gray-900 mb-8 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Información del Evento
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Fecha y Hora</h3>
                        <p class="text-gray-700 text-lg">{{ \Carbon\Carbon::parse($event->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($event->date)->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">Ubicación</h3>
                        <p class="text-gray-700 text-lg">{{ $event->address }}</p>
                        @if($event->coordinates)
                            <button onclick="showMap()" class="text-sm text-blue-600 hover:text-blue-800 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver en mapa
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            @if($event->description)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Descripción</h3>
                    <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-gray-900 prose-a:text-blue-600 prose-code:text-purple-600 prose-code:bg-purple-50 prose-code:px-2 prose-code:py-1 prose-code:rounded prose-pre:bg-gray-900 prose-pre:text-gray-100 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 prose-blockquote:text-gray-800">
                        {!! \Illuminate\Support\Str::markdown($event->description) !!}
                    </div>
                </div>
            @endif

            @if($event->agenda && $event->agenda !== 'N/A')
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Temario</h3>
                    <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-gray-200">
                        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-700 prose-strong:text-gray-900 prose-a:text-blue-600 prose-code:text-purple-600 prose-code:bg-purple-50 prose-code:px-2 prose-code:py-1 prose-code:rounded prose-pre:bg-gray-900 prose-pre:text-gray-100 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 prose-blockquote:text-gray-800">
                            {!! \Illuminate\Support\Str::markdown($event->agenda) !!}
                        </div>
                    </div>
                </div>
            @endif

            @if($event->coordinates)
                <div class="bg-gradient-to-r from-green-50 to-emerald-100 rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ubicación del Evento
                    </h2>
                    <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg">
                        <div id="map" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            @endif
        </div>

    <!-- Ticket Purchase Form -->
    @if($event->ticketTypes->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form id="purchaseForm" class="space-y-6">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                    <div class="grid grid-cols-1 gap-6">
                    @foreach($event->ticketTypes as $ticketType)
                            <div class="bg-white/60 backdrop-blur-sm border-2 border-gray-200 rounded-xl p-6 hover:border-indigo-300 transition-all duration-300 hover:shadow-lg hover:scale-105 flex justify-between">
                                <div class="flex flex-col items-center justify-center align-middle">
                                    <div class="flex items-center justify-center align-middle">
                                        <h4 class="text-xl font-bold text-gray-900 text-left align-middle">{{ $ticketType->name }}</h4>
                                    </div>
                                    <div class="items-center justify-center space-x-4 align-middle">
                                    <div class="flex items-center align-middle">
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-green-600">{{ $ticketType->pivot->quantity }} disponibles</span>
                                    </div>
                                    @if($ticketType->pivot->quantity <= 5)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ¡Últimos!
                                        </span>
                                    @elseif($ticketType->pivot->quantity <= 20)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pocos disponibles
                                        </span>
                                    @endif
                                </div>
                                </div>
                                <div class="flex items-center justify-center align-middle items-center justify-between h-full">
                                    <div class="text-center align-middle">
                                        <p class="text-3xl font-bold text-green-600 mb-1">${{ number_format($ticketType->pivot->price, 2) }}</p>
                                        <p class="text-sm text-gray-500">por boleto</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center space-x-4 align-middle">
                                        <button type="button"
                                            class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors"
                                                onclick="decreaseQuantity({{ $ticketType->id }})">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>

                                        <input type="number"
                                               id="quantity_{{ $ticketType->id }}"
                                               name="tickets[{{ $loop->index }}][quantity]"
                                               value="0"
                                               min="0"
                                               max="{{ $ticketType->pivot->quantity }}"
                                           class=" text-center text-xl font-bold border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                               onchange="updateTotal()">

                                        <button type="button"
                                            class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors"
                                                onclick="increaseQuantity({{ $ticketType->id }}, {{ $ticketType->pivot->quantity }})">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                </div>

                                <input type="hidden" name="tickets[{{ $loop->index }}][ticket_type_id]" value="{{ $ticketType->id }}">
                        </div>
                    @endforeach
                </div>

                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200">
                        <h4 class="text-xl font-semibold text-gray-900 mb-6">Resumen de la Orden</h4>
                        <div id="order_summary" class="space-y-4">
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Subtotal:</span>
                                <span id="subtotal" class="font-semibold">$0.00</span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">Descuento:</span>
                                <span id="discount" class="font-semibold text-green-600">$0.00</span>
                            </div>
                            <div class="flex justify-between text-lg">
                                <span class="text-gray-600">IVA (16%):</span>
                                <span id="taxes" class="font-semibold">$0.00</span>
                            </div>
                            <div class="border-t pt-4">
                                <div class="flex justify-between font-bold text-2xl">
                                    <span>Total:</span>
                                    <span id="total" class="text-green-600">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                    <div class="flex flex-col sm:flex-row gap-4">
            <button type="button"
                    id="add_to_cart_button"
                    disabled
                    onclick="addToCart()"
                                class="flex-1 bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-8 py-4 rounded-xl hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed font-semibold text-lg flex items-center justify-center space-x-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span>Agregar al Carrito</span>
            </button>
                        
            <button type="button"
                    id="view_cart_button"
                    onclick="window.location.href='{{ config('app.url') }}/cart'"
                                class="flex-1 bg-gray-600 text-white px-8 py-4 rounded-xl hover:bg-gray-700 transition-all duration-300 font-semibold text-lg flex items-center justify-center space-x-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                            </svg>
                            <span>Ver Carrito</span>
            </button>
        </div>
            </form>
        </div>
    @else
            <div class="bg-white/80 backdrop-blur-lg rounded-2xl p-12 text-center border border-white/30 shadow-xl">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">No hay boletos disponibles</h3>
            <p class="text-gray-500">Este evento no tiene tipos de boletos configurados.</p>
        </div>
    @endif

    <div class="space-y-4 bg-white shadow-lg p-6 rounded-lg p-4">
            <h1 class="text-xl font-semibold text-gray-900 mb-4"> Ubicación del evento </h1>
            <div id="map" style="height: 400px; border-radius: 12px;"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
<script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

@push('scripts')
<script>
console.log('JavaScript starting...');

let ticketPrices = {
    @foreach($event->ticketTypes as $ticketType)
        {{ $ticketType->id }}: {{ $ticketType->pivot->price }}{{ $loop->last ? '' : ',' }}
    @endforeach
};

console.log('Ticket prices:', ticketPrices);

let appliedCoupon = null;
let map = null;

// Funciones para manejo de cantidades
function increaseQuantity(ticketTypeId, maxQuantity) {
    console.log('increaseQuantity called:', ticketTypeId, maxQuantity);
    const input = document.getElementById(`quantity_${ticketTypeId}`);
    if (!input) {
        console.error('Input not found for ticket type:', ticketTypeId);
        return;
    }
    const currentValue = parseInt(input.value);
    if (currentValue < maxQuantity) {
        input.value = currentValue + 1;
        updateTotal();
    }
}

function decreaseQuantity(ticketTypeId) {
    console.log('decreaseQuantity called:', ticketTypeId);
    const input = document.getElementById(`quantity_${ticketTypeId}`);
    if (!input) {
        console.error('Input not found for ticket type:', ticketTypeId);
        return;
    }
    const currentValue = parseInt(input.value);
    if (currentValue > 0) {
        input.value = currentValue - 1;
        updateTotal();
    }
}

function updateTotal() {
    let subtotal = 0;
    let hasTickets = false;

    // Calculate subtotal
    @foreach($event->ticketTypes as $ticketType)
        const quantity{{ $ticketType->id }} = parseInt(document.getElementById('quantity_{{ $ticketType->id }}').value) || 0;
        if (quantity{{ $ticketType->id }} > 0) hasTickets = true;
        subtotal += quantity{{ $ticketType->id }} * {{ $ticketType->pivot->price }};
    @endforeach

    // Apply coupon discount
    let discount = 0;
    if (appliedCoupon) {
        discount = (subtotal * appliedCoupon.discount_percentage) / 100;
    }

    // Calculate taxes (16% IVA)
    const taxableAmount = subtotal - discount;
    const taxes = taxableAmount * 0.16;
    const total = taxableAmount + taxes;

    // Update display
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('discount').textContent = '$' + discount.toFixed(2);
    document.getElementById('taxes').textContent = '$' + taxes.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);

    // Enable/disable add to cart button
    document.getElementById('add_to_cart_button').disabled = !hasTickets;
}

// Función mejorada para agregar al carrito
async function addToCart() {
    const tickets = [];
    @foreach($event->ticketTypes as $ticketType)
        const quantity{{ $ticketType->id }} = parseInt(document.getElementById('quantity_{{ $ticketType->id }}').value) || 0;
        if (quantity{{ $ticketType->id }} > 0) {
            tickets.push({
                ticket_type_id: {{ $ticketType->id }},
                quantity: quantity{{ $ticketType->id }}
            });
        }
    @endforeach

    if (tickets.length === 0) {
        showNotification('Por favor selecciona al menos un boleto.', 'error');
        return;
    }

    // Disable button to prevent multiple submissions
    const button = document.getElementById('add_to_cart_button');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>Agregando...</span>
    `;

    try {
        // Add each ticket type to cart sequentially
        for (const ticket of tickets) {
            const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('ticket_type_id', ticket.ticket_type_id);
    formData.append('quantity', ticket.quantity);

        const response = await fetch('{{ route("cart.add") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

            const responseData = await response.json();
            
        if (!response.ok) {
                console.error('Error response:', response.status, responseData);
                throw new Error(responseData.message || 'Error al agregar al carrito');
            }
        }

        // Mostrar notificación de éxito
        showNotification('¡Boletos agregados al carrito exitosamente!', 'success');

        // Disparar evento de carrito actualizado
        document.dispatchEvent(new CustomEvent('cartUpdated'));
        
        // Actualizar contador del carrito
        updateCartCount();

    } catch (error) {
        console.error('Error:', error);
        showNotification(error.message || 'Error al agregar boletos al carrito. Inténtalo de nuevo.', 'error');
    } finally {
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    const icon = type === 'success' ? 
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
        type === 'error' ?
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
    
    notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Función para actualizar contador del carrito
function updateCartCount() {
    fetch('{{ route("cart.count") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Actualizar el contador visual en el header
        const cartCount = document.querySelector('#cart-dropdown .bg-red-500');
        if (cartCount) {
            cartCount.textContent = data.count;
            if (data.count > 0) {
                cartCount.style.display = 'inline-flex';
                cartCount.classList.add('animate-pulse');
                setTimeout(() => cartCount.classList.remove('animate-pulse'), 1000);
            } else {
                cartCount.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
    });
}

// Función para mostrar el mapa
function showMap() {
    if (map) {
        map.invalidateSize();
        return;
    }
    
    @if($event->coordinates)
        const coordinates = "{{ $event->coordinates }}".split(',').map(Number);
        if (coordinates.length === 2) {
            map = L.map('map').setView(coordinates, 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            L.marker(coordinates).addTo(map)
                .bindPopup('<b>{{ $event->name }}</b><br>{{ $event->address }}')
                .openPopup();
        }
    @endif
}

// --- ¡NUEVO! ---
// Parallax Hero Image
document.addEventListener('scroll', function() {
    const heroImage = document.getElementById('hero-image');
    if (heroImage) {
        const scrollPosition = window.scrollY;
        // Mueve la imagen hacia abajo a una fracción de la velocidad de scroll
        // Esto crea el efecto de que "baja" más lento que la página
        heroImage.style.transform = `translateY(${scrollPosition * 0.4}px)`;
    }
});
// --- FIN NUEVO ---

// Inicializar mapa automáticamente
document.addEventListener('DOMContentLoaded', function() {
    @if($event->coordinates)
        showMap();
    @endif
    
    // Inicializar highlight.js para código en markdown
    hljs.highlightAll();
    
    updateTotal();
});

// Escuchar eventos de carrito actualizado
document.addEventListener('cartUpdated', function() {
    updateCartCount();
});

// Initialize
console.log('JavaScript loaded successfully');
updateTotal();

// SCRIPT DEL MAPA
document.addEventListener('DOMContentLoaded', function() {
    console.log("Inicializando mapa...");
    const mapElement = document.getElementById('map');

    if(!mapElement){
        console.error("El contenedor del mapa (#map) no se encontró");
        return;
    }
   @if($event->coordinates) 
   const [latStr, lngStr] = "{{ $event->coordinates }}".split(',');
   const lat = parseFloat(latStr.trim());
   const lng = parseFloat(lngStr.trim()); 

   if (!isNaN(lat) && !isNaN(lng)) {
        // Crear el mapa centrado en las coordenadas del evento
        const map = L.map('map').setView([lat, lng], 15);

        // Cargar el mapa base
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Agregar el marcador
        L.marker([lat, lng]).addTo(map)
            .bindPopup(`<b>{{ $event->name }}</b><br>{{ $event->address }}`)
            .openPopup();

        map.invalidateSize();

        console.log("Mapa inicializado y tamaño recalculado.");

   } else {
        document.getElementById('map').innerHTML =
            '<p class="text-gray-500 text-center py-8">Coordenadas no válidas para este evento.</p>';
   }
   @else
        document.getElementById('map').innerHTML = 
            '<p class="text-gray-500 text-center py-8">No hay coordenadas disponibles para este evento.</p>';
  @endif          
});

</script>
@endpush
@endsection