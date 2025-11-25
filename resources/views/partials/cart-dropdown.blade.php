<!-- Header -->
<div class="px-3 py-2 sm:px-4 sm:py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
    <div class="flex items-center justify-between">
        <div class="min-w-0 flex-1">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">Carrito de Compras</h3>
            <p class="text-xs sm:text-sm text-gray-500">{{ $cartCount }} item(s)</p>
        </div>
        <button onclick="closeCartDropdown()" class="text-gray-400 hover:text-gray-600 ml-2 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Cart Items with Scroll -->
<div class="flex-1 overflow-y-auto max-h-64 sm:max-h-80">
    @if(empty($cart))
        <div class="px-4 py-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                </svg>
            </div>
            <p class="text-gray-500 text-sm">Tu carrito está vacío</p>
        </div>
    @else
        @foreach($cart as $key => $item)
            <div class="px-3 py-2 sm:px-4 sm:py-3 border-b border-gray-100 hover:bg-gray-50">
                <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                    <!-- Icono de boleto -->
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item['ticket_type_name'] ?? 'Boleto' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $item['event_name'] ?? 'Evento' }}</p>
                        @if(isset($item['event_date']))
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item['event_date'])->format('d M Y') }}</p>
                        @endif
                        <!-- Mostrar disponibilidad -->
                        @php
                            $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $item['ticket_type_id'])
                                ->where('event_id', $item['event_id'])
                                ->first();
                            $reservedQuantity = \App\Models\TicketReservation::where('ticket_types_id', $item['ticket_type_id'])
                                ->where('event_id', $item['event_id'])
                                ->where('reserved_until', '>', now())
                                ->where('is_active', true)
                                ->where('session_id', '!=', session()->getId())
                                ->sum('quantity');
                            $available = $ticketEvent ? ($ticketEvent->quantity - $reservedQuantity) : 0;
                        @endphp
                        <p class="text-xs text-green-600 font-medium">
                            Disponibles: {{ $available }} boletos
                        </p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <p class="text-xs sm:text-sm font-medium text-gray-900">{{ $item['quantity'] }}x</p>
                        <p class="text-xs sm:text-sm text-gray-500">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

@if(!empty($cart))
    <!-- Footer with Total and Actions -->
    <div class="px-3 py-2 sm:px-4 sm:py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
        <div class="flex justify-between items-center mb-2 sm:mb-3">
            <span class="text-xs sm:text-sm font-medium text-gray-900">Total (IVA incluido):</span>
            <span class="text-base sm:text-lg font-bold text-gray-900">${{ number_format($cartTotal, 2) }}</span>
        </div>

        <div class="flex flex-col sm:flex-row gap-2">
            <a href="{{ \App\Helpers\CartHelper::getCartViewRoute() }}"
               class="flex-1 bg-gray-600 text-white text-center px-3 py-2 rounded-md text-xs sm:text-sm hover:bg-gray-700 transition-colors">
                Ver Carrito
            </a>
            <a href="{{ \App\Helpers\CartHelper::getCheckoutRoute() }}"
               class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-md text-xs sm:text-sm hover:bg-blue-700 transition-colors">
                Comprar
            </a>
        </div>
    </div>
@endif

