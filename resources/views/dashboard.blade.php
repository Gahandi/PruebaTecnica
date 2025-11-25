@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto py-4 sm:py-6 px-2 sm:px-4 lg:px-8">
    <!-- Welcome Message -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">¡Bienvenido, {{ auth()->user()->name }}!</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Aquí tienes un resumen de tu sistema de boletos</p>
    </div>
            <!-- Métricas principales -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-3 sm:p-4 lg:p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-md flex items-center justify-center">
                                    <span class="text-white font-bold text-sm sm:text-base">E</span>
                                </div>
                            </div>
                            <div class="ml-2 sm:ml-3 lg:ml-5 w-0 flex-1 min-w-0">
                                <dl>
                                    <dt class="text-xs sm:text-sm font-medium text-gray-500 truncate">Eventos</dt>
                                    <dd class="text-base sm:text-lg lg:text-xl font-medium text-gray-900">{{ $totalEvents }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <span class="text-white font-bold">O</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Órdenes</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalOrders }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                    <span class="text-white font-bold">T</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tickets</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalTickets }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <span class="text-white font-bold">$</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ingresos</dt>
                                    <dd class="text-lg font-medium text-gray-900">${{ number_format($totalRevenue, 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                    <span class="text-white font-bold">C</span>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Check-ins</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalCheckins }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos y tablas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <!-- Ingresos por mes -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 sm:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Ingresos por Mes</h3>
                        <div class="text-xs sm:text-sm text-gray-500">Últimos 6 meses</div>
                    </div>
                    <div class="relative h-48 sm:h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Boletos por tipo -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-3 sm:mb-4 gap-2">
                        <h3 class="text-base sm:text-lg font-medium text-gray-900">Boletos Vendidos por Tipo</h3>
                        <div class="text-xs sm:text-sm text-gray-500">Distribución</div>
                    </div>
                    <div class="relative h-48 sm:h-64">
                        <canvas id="ticketsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Eventos más populares -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Eventos Más Populares</h3>
                    <div class="space-y-3">
                        @foreach($popularEvents as $event)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $event->name }}</span>
                            <span class="text-sm font-medium text-gray-900">{{ $event->orders_count }} órdenes</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Cupones más usados -->
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Cupones Más Usados</h3>
                    <div class="space-y-3">
                        @foreach($couponsUsed as $coupon)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $coupon->code }} ({{ $coupon->discount_percentage }}%)</span>
                            <span class="text-sm font-medium text-gray-900">{{ $coupon->times_used }} usos</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Check-ins recientes -->
            <div class="mt-6 sm:mt-8">
                <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Check-ins Recientes</h3>
                    <div class="overflow-x-auto -mx-4 sm:mx-0">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Ticket ID</th>
                                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentCheckins as $checkin)
                                <tr>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900">
                                                <div class="font-medium">{{ Str::limit($checkin->ticket->order->event->name ?? 'N/A', 30) }}</div>
                                                <div class="text-xs text-gray-500 sm:hidden mt-1">{{ substr($checkin->ticket->id, 0, 8) }}...</div>
                                    </td>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500 hidden sm:table-cell">
                                        {{ substr($checkin->ticket->id, 0, 8) }}...
                                    </td>
                                            <td class="px-3 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                        {{ $checkin->scanned_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de ingresos por mes
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRevenueData['months']) !!},
            datasets: [{
                label: 'Ingresos',
                data: {!! json_encode($monthlyRevenueData['revenues']) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Gráfico de boletos por tipo
    const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
    new Chart(ticketsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ticketsByType->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($ticketsByType->pluck('total_sold')) !!},
                backgroundColor: [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6',
                    '#EC4899',
                    '#06B6D4',
                    '#84CC16'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
</script>
@endsection
