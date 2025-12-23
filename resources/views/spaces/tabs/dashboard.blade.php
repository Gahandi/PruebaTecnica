<!-- Tab: Dashboard - Enhanced Version -->
<div>
    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 sm:mb-8">Dashboard de {{ $space->name }}</h2>

    <!-- Métricas Principales -->
    <div class="grid grid-cols-2 lg:grid-cols-2 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <!-- Total Eventos -->
        <div
            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-blue-100 text-xs sm:text-sm font-medium mb-1">Total Eventos</p>
                    <p class="text-2xl sm:text-4xl font-bold truncate">{{ $totalEvents }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2 sm:p-4 ml-2 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Miembros -->
        <div
            class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-green-100 text-xs sm:text-sm font-medium mb-1">Total Miembros</p>
                    <p class="text-2xl sm:text-4xl font-bold truncate">{{ $totalMembers }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2 sm:p-4 ml-2 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Boletos Vendidos -->
        <div
            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-purple-100 text-xs sm:text-sm font-medium mb-1">Boletos Vendidos</p>
                    <p class="text-2xl sm:text-4xl font-bold truncate">{{ $totalTicketsSold }}</p>
                    <p class="text-purple-100 text-xs mt-1 hidden sm:block">de {{ $totalTicketsAvailable }} disponibles
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2 sm:p-4 ml-2 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ingresos Totales -->
        <div
            class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl sm:rounded-2xl p-4 sm:p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-orange-100 text-xs sm:text-sm font-medium mb-1">Ingresos Totales</p>
                    <p class="text-xl sm:text-4xl font-bold truncate">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2 sm:p-4 ml-2 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div
            class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-teal-100">Check-ins Realizados</p>
                    <p class="text-2xl sm:text-4xl font-bold mt-1 sm:mt-2">{{ number_format($totalCheckins ?? 0) }}</p>
                    <p class="text-xs sm:text-sm mt-1">{{ number_format($checkinRate ?? 0, 1) }}% de tickets</p>
                </div>
                <svg class="w-8 h-8 sm:w-12 sm:h-12 text-teal-200 flex-shrink-0 ml-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-indigo-100">Total Órdenes</p>
                    <p class="text-2xl sm:text-4xl font-bold mt-1 sm:mt-2">{{ number_format($totalOrders ?? 0) }}</p>
                </div>
                <svg class="w-8 h-8 sm:w-12 sm:h-12 text-indigo-200 flex-shrink-0 ml-2" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl sm:rounded-2xl shadow-xl p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-medium text-pink-100">Precio Promedio</p>
                    <p class="text-2xl sm:text-4xl font-bold mt-1 sm:mt-2">
                        ${{ number_format($averageTicketPrice ?? 0, 2) }}</p>
                </div>
                <svg class="w-8 h-8 sm:w-12 sm:h-12 text-pink-200 flex-shrink-0 ml-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Ingresos Mensuales</h3>
                <span class="text-xs sm:text-sm text-gray-500">Últimos 6 meses</span>
            </div>
            <div class="h-64 sm:h-80">
                <canvas id="spaceRevenueChart"></canvas>
            </div>
        </div>

        <!-- Daily Sales Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Ventas Diarias</h3>
                <span class="text-xs sm:text-sm text-gray-500">Últimos 7 días</span>
            </div>
            <div class="h-64 sm:h-80">
                <canvas id="spaceDailySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tickets Distribution & Sales Progress -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Tickets Distribution -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6">Distribución de Tickets</h3>
            <div class="h-64 sm:h-80">
                <canvas id="spaceTicketsChart"></canvas>
            </div>
        </div>

        <!-- Sales Progress -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6">Progreso de Ventas</h3>
            @php
                $percentageSold = $totalTicketsAvailable > 0 ? ($totalTicketsSold / $totalTicketsAvailable) * 100 : 0;
            @endphp
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Boletos Vendidos</span>
                        <span class="font-semibold">{{ number_format($percentageSold, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-4 rounded-full transition-all duration-500"
                            style="width: {{ min($percentageSold, 100) }}%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                        <p class="text-2xl sm:text-3xl font-bold text-green-600">{{ $totalTicketsSold }}</p>
                        <p class="text-xs sm:text-sm text-green-800">Vendidos</p>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                        <p class="text-2xl sm:text-3xl font-bold text-blue-600">
                            {{ $totalTicketsAvailable - $totalTicketsSold }}</p>
                        <p class="text-xs sm:text-sm text-blue-800">Disponibles</p>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="pt-4 border-t border-gray-200">
                    <div
                        class="flex justify-between items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                        <div>
                            <p class="text-sm text-gray-600">Ingresos Totales</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">
                                ${{ number_format($totalRevenue, 2) }}</p>
                        </div>
                        <div class="bg-purple-500 rounded-full p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6">Órdenes Recientes</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentOrders ?? [] as $order)
                    <div
                        class="flex items-center justify-between p-3 border-l-4 border-pink-500 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">{{ $order->user->name ?? 'Usuario' }}
                            </p>
                            @foreach($order->events ?? [] as $event)
                                <p class="text-xs text-gray-500 truncate">{{ $event->name }}</p>
                            @endforeach
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <p class="font-bold text-green-600 text-sm">
                                ${{ number_format($order->payments->sum('total') ?? 0, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <p class="text-sm">No hay órdenes recientes</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Check-ins -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6">Check-ins Recientes</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentCheckins ?? [] as $checkin)
                    <div
                        class="flex items-center justify-between p-3 border-l-4 border-green-500 bg-gray-50 rounded hover:bg-gray-100 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm truncate">
                                {{ $checkin->ticket->order->user->name ?? 'N/A' }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">
                                Ticket: {{ substr($checkin->ticket->id, 0, 8) }}...
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0 ml-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ Check-in
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $checkin->scanned_at->format('d/m H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">No hay check-ins recientes</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4 sm:mb-6">Próximos Eventos</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($upcomingEvents ?? [] as $event)
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-pink-500 transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-bold text-gray-900 truncate flex-1">{{ Str::limit($event->name, 25) }}</h4>
                        <span
                            class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full ml-2 flex-shrink-0">Activo</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                    </p>
                    @if($event->address)
                        <p class="text-xs text-gray-500 truncate">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                            </svg>
                            {{ Str::limit($event->address, 35) }}
                        </p>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <p class="text-sm">No hay eventos próximos</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Chart.js for Space Dashboard -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Revenue Chart
        const revenueCtx = document.getElementById('spaceRevenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyRevenueData['months'] ?? []) !!},
                    datasets: [{
                        label: 'Ingresos',
                        data: {!! json_encode($monthlyRevenueData['revenues'] ?? []) !!},
                        borderColor: 'rgb(236, 72, 153)',
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(236, 72, 153)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function (context) {
                                    return 'Ingresos: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: function (value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('spaceDailySalesChart');
        if (dailySalesCtx) {
            new Chart(dailySalesCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailySalesData['days'] ?? []) !!},
                    datasets: [{
                        label: 'Ventas Diarias',
                        data: {!! json_encode($dailySalesData['revenues'] ?? []) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function (context) {
                                    return 'Ventas: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: function (value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Tickets Distribution Chart
        const ticketsCtx = document.getElementById('spaceTicketsChart');
        if (ticketsCtx) {
            const ticketData = {!! json_encode($ticketsByType ?? collect()) !!};
            new Chart(ticketsCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ticketData.map(t => t.name || 'Sin nombre'),
                    datasets: [{
                        data: ticketData.map(t => t.total_sold || 0),
                        backgroundColor: [
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + context.parsed.toLocaleString() + ' tickets';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>