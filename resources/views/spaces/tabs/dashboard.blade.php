<!-- Tab: Dashboard -->
<div>
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Dashboard de {{ $space->name }}</h2>
    
    <!-- Métricas Principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Eventos -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Eventos</p>
                    <p class="text-4xl font-bold">{{ $totalEvents }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Miembros -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Total Miembros</p>
                    <p class="text-4xl font-bold">{{ $totalMembers }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Boletos Vendidos -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Boletos Vendidos</p>
                    <p class="text-4xl font-bold">{{ $totalTicketsSold }}</p>
                    <p class="text-purple-100 text-xs mt-1">de {{ $totalTicketsAvailable }} disponibles</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ingresos Totales -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-xl transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Ingresos Totales</p>
                    <p class="text-4xl font-bold">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Detalladas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Progreso de Ventas -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Progreso de Ventas</h3>
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
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-4 rounded-full transition-all duration-500" style="width: {{ $percentageSold }}%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $totalTicketsSold }}</p>
                        <p class="text-sm text-gray-600">Vendidos</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ $totalTicketsAvailable - $totalTicketsSold }}</p>
                        <p class="text-sm text-gray-600">Disponibles</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Resumen Financiero</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Ingresos Totales</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="bg-blue-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Boletos Vendidos</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalTicketsSold }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Precio Promedio</p>
                        <p class="text-xl font-bold text-gray-900">${{ $totalTicketsSold > 0 ? number_format($totalRevenue / $totalTicketsSold, 2) : '0.00' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

