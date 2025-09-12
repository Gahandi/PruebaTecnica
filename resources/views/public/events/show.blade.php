@extends('layouts.app')

@section('title', $event->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="h-64 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
            <div class="text-center text-white">
                <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold">{{ $event->name }}</h1>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Evento</h2>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Fecha y Hora</p>
                                <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($event->date)->format('l, d F Y \a \l\a\s H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Ubicación</p>
                                <p class="text-sm text-gray-600">{{ $event->location }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Compra de Boletos</h2>
                    <p class="text-sm text-gray-600 mb-4">Selecciona los tipos de boletos que deseas comprar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Purchase Form -->
    @if($event->ticketTypes->count() > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form id="purchaseForm" class="space-y-6">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Selecciona tus boletos</h3>
                
                <div class="space-y-4">
                    @foreach($event->ticketTypes as $ticketType)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $ticketType->name }}</h4>
                                    <p class="text-sm text-gray-600">Disponibles: {{ $ticketType->quantity }}</p>
                                </div>
                                
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-green-600">${{ number_format($ticketType->price, 2) }}</p>
                                        <p class="text-xs text-gray-500">por boleto</p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <button type="button" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors"
                                                onclick="decreaseQuantity({{ $ticketType->id }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        
                                        <input type="number" 
                                               id="quantity_{{ $ticketType->id }}" 
                                               name="tickets[{{ $loop->index }}][quantity]" 
                                               value="0" 
                                               min="0" 
                                               max="{{ $ticketType->quantity }}"
                                               class="w-16 text-center border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                               onchange="updateTotal()">
                                               
                                        <button type="button" 
                                                class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition-colors"
                                                onclick="increaseQuantity({{ $ticketType->id }}, {{ $ticketType->quantity }})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                        
                                        <input type="hidden" name="tickets[{{ $loop->index }}][ticket_type_id]" value="{{ $ticketType->id }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>



                <!-- Order Summary -->
                <div class="border-t pt-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Resumen de la Orden</h4>
                        <div id="order_summary" class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal:</span>
                                <span id="subtotal">$0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Descuento:</span>
                                <span id="discount">$0.00</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>IVA (16%):</span>
                                <span id="taxes">$0.00</span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Total:</span>
                                    <span id="total">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        <!-- Purchase Button -->
        <div class="flex justify-end space-x-4">
            <button type="button" 
                    id="add_to_cart_button"
                    disabled
                    onclick="addToCart()"
                    class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                Agregar al Carrito
            </button>
            <button type="button" 
                    id="view_cart_button"
                    onclick="window.location.href='{{ route('checkout.cart') }}'"
                    class="bg-gray-600 text-white px-8 py-3 rounded-md hover:bg-gray-700 transition-colors">
                Ver Carrito
            </button>
        </div>
            </form>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay boletos disponibles</h3>
            <p class="text-gray-500">Este evento no tiene tipos de boletos configurados.</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
console.log('JavaScript starting...');

let ticketPrices = {
    @foreach($event->ticketTypes as $ticketType)
        {{ $ticketType->id }}: {{ $ticketType->price }},
    @endforeach
};

console.log('Ticket prices:', ticketPrices);

let appliedCoupon = null;

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
        subtotal += quantity{{ $ticketType->id }} * {{ $ticketType->price }};
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

async function applyCoupon() {
    const couponCode = document.getElementById('coupon_code').value;
    const messageDiv = document.getElementById('coupon_message');
    
    if (!couponCode) {
        messageDiv.textContent = 'Por favor ingresa un código de descuento';
        messageDiv.className = 'mt-2 text-sm text-red-600';
        messageDiv.classList.remove('hidden');
        return;
    }
    
    try {
        const response = await fetch('/api/v1/coupons/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ code: couponCode })
        });
        
        const data = await response.json();
        
        if (data.success) {
            appliedCoupon = data.coupon;
            messageDiv.textContent = `Descuento del ${appliedCoupon.discount_percentage}% aplicado`;
            messageDiv.className = 'mt-2 text-sm text-green-600';
            messageDiv.classList.remove('hidden');
            updateTotal();
        } else {
            messageDiv.textContent = data.message || 'Código de descuento inválido';
            messageDiv.className = 'mt-2 text-sm text-red-600';
            messageDiv.classList.remove('hidden');
        }
    } catch (error) {
        messageDiv.textContent = 'Error al validar el cupón';
        messageDiv.className = 'mt-2 text-sm text-red-600';
        messageDiv.classList.remove('hidden');
    }
}

// Add to cart function
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
        alert('Por favor selecciona al menos un boleto.');
        return;
    }
    
    // Disable button to prevent multiple submissions
    const button = document.getElementById('add_to_cart_button');
    button.disabled = true;
    button.textContent = 'Agregando...';
    
    try {
        // Add each ticket type to cart sequentially
        for (const ticket of tickets) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('ticket_type_id', ticket.ticket_type_id);
            formData.append('quantity', ticket.quantity);
            
            const response = await fetch('{{ route("checkout.add-to-cart") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (!response.ok) {
                throw new Error('Error al agregar boletos al carrito');
            }
        }
        
        // Redirect to cart after all tickets are added
        window.location.href = '{{ route("checkout.cart") }}';
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al agregar boletos al carrito. Inténtalo de nuevo.');
    } finally {
        // Re-enable button
        button.disabled = false;
        button.textContent = 'Agregar al Carrito';
    }
}

// Initialize
console.log('JavaScript loaded successfully');
updateTotal();
</script>
@endpush
@endsection
