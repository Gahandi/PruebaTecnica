<!-- Tab: Órdenes -->
<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Órdenes del Espacio</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $spaceOrders->total() }} órdenes encontradas</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
        <form method="GET" action="" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="tab" value="orders">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       name="orders_search" 
                       value="{{ request('orders_search', '') }}"
                       placeholder="Buscar por ID de orden, nombre o email del cliente..."
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm">
            </div>
            <button type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 transition-all font-medium text-sm">
                Buscar
            </button>
            @if(request('orders_search'))
                <a href="?tab=orders" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-medium text-sm text-center">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-pink-500 to-pink-600">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">ID Orden</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Cliente</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Evento</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Tickets</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Total</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden sm:table-cell">Estado</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Fecha</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($spaceOrders ?? [] as $order)
                        @php
                            $orderTotal = $order->payments->sum('total') ?? 0;
                            $orderEvents = $order->tickets->pluck('event')->unique('id')->filter();
                            $ticketCount = $order->tickets->count();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="toggleOrderDetails('{{ $order->id }}')">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-mono font-semibold text-gray-900">{{ Str::limit($order->id, 8) }}...</span>
                                    <button type="button" onclick="event.stopPropagation(); copyToClipboard('{{ $order->id }}')" 
                                            class="ml-2 text-gray-400 hover:text-pink-500" title="Copiar ID">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($order->user && $order->user->image)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ \App\Helpers\ImageHelper::getImageUrl($order->user->image) }}" alt="{{ $order->user->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                {{ $order->user ? strtoupper(substr($order->user->name, 0, 1)) : '?' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'Sin nombre' }} {{ $order->user->last_name ?? '' }}</p>
                                        <p class="text-xs text-gray-500 hidden sm:block">{{ $order->user->email ?? 'Sin email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                <div class="space-y-1">
                                    @foreach($orderEvents->take(2) as $event)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ Str::limit($event->name ?? 'Evento', 20) }}
                                        </span>
                                    @endforeach
                                    @if($orderEvents->count() > 2)
                                        <span class="text-xs text-gray-500">+{{ $orderEvents->count() - 2 }} más</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                    {{ $ticketCount }} ticket{{ $ticketCount != 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-lg font-bold text-green-600">${{ number_format($orderTotal, 2) }}</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                                @php
                                    $status = $order->status ?? 'pending';
                                    $statusColors = [
                                        'completed' => 'bg-green-100 text-green-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'refunded' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusLabels = [
                                        'completed' => 'Completada',
                                        'pending' => 'Pendiente',
                                        'cancelled' => 'Cancelada',
                                        'refunded' => 'Reembolsada'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 hidden lg:table-cell">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <button type="button" class="text-pink-600 hover:text-pink-800 transition-colors">
                                    <svg class="w-5 h-5 transform transition-transform" id="chevron-{{ $order->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <!-- Expanded Details Row -->
                        <tr id="details-{{ $order->id }}" class="hidden bg-gray-50">
                            <td colspan="8" class="px-4 py-4">
                                <div class="bg-white rounded-xl p-4 border border-gray-200">
                                    <!-- Mobile info -->
                                    <div class="sm:hidden mb-4 pb-4 border-b border-gray-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs text-gray-500">Estado:</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusLabels[$status] ?? ucfirst($status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs text-gray-500">Fecha:</span>
                                            <span class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-500">Email:</span>
                                            <span class="text-sm text-gray-900">{{ $order->user->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    
                                    <h4 class="text-sm font-bold text-gray-700 mb-3">Tickets de esta Orden</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                        @foreach($order->tickets as $ticket)
                                            @php
                                                $hasCheckin = $ticket->checkin && $ticket->checkin->count() > 0;
                                            @endphp
                                            <div class="border rounded-lg p-3 {{ $hasCheckin ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-mono text-gray-500">{{ Str::limit($ticket->id, 8) }}...</span>
                                                    @if($hasCheckin)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            ✓ Check-in
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Pendiente
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $ticket->ticketType->name ?? 'Sin tipo' }}</p>
                                                <p class="text-xs text-gray-600">Evento: {{ $ticket->event->name ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Propietario: {{ $order->user->name ?? 'N/A' }} {{ $order->user->last_name ?? '' }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    @if(request('orders_search'))
                                        <p class="text-gray-500 text-lg font-medium">No se encontraron órdenes</p>
                                        <p class="text-gray-400 text-sm">Intenta con otro término de búsqueda</p>
                                        <a href="?tab=orders" class="mt-4 px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                                            Ver todas las órdenes
                                        </a>
                                    @else
                                        <p class="text-gray-500 text-lg font-medium">No hay órdenes aún</p>
                                        <p class="text-gray-400 text-sm">Las órdenes de tus eventos aparecerán aquí</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($spaceOrders) && $spaceOrders->hasPages())
            <div class="px-4 py-4 bg-gray-50 border-t border-gray-200">
                {{ $spaceOrders->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleOrderDetails(orderId) {
    const detailsRow = document.getElementById('details-' + orderId);
    const chevron = document.getElementById('chevron-' + orderId);
    
    if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
        chevron.classList.add('rotate-180');
    } else {
        detailsRow.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        toast.textContent = 'ID copiado al portapapeles';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>
