@extends('layouts.admin')

@section('title', 'Dashboard Profesional')

@section('admin-content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1
                            class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-pink-600 to-purple-600 bg-clip-text text-transparent">
                            ¡Bienvenido, {{ auth()->user()->name }}!
                        </h1>
                        <p class="mt-2 text-gray-600">Aquí está el resumen completo de tu plataforma de eventos</p>
                    </div>
                    <div class="mt-4 md:mt-0 flex space-x-3">
                        <a href="{{ route('dashboard.export.pdf') }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Exportar PDF
                        </a>
                        <!-- <button
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg shadow-lg text-sm font-medium text-white hover:from-pink-600 hover:to-purple-700 transition-all transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                    </path>
                                </svg>
                                Nuevo Evento
                            </button> -->
                    </div>
                </div>
            </div>

            {{-- Main Metrics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
                {{-- Revenue Card --}}
                <div
                    class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border-l-4 border-pink-500 transform transition-all hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Ingresos Totales</p>
                            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 truncate">
                                ${{ number_format($totalRevenue, 2) }}</p>
                            <div class="flex items-center mt-2">
                                @if($revenueGrowth >= 0)
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-green-600">+{{ number_format($revenueGrowth, 1) }}%</span>
                                @else
                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-red-600">{{ number_format($revenueGrowth, 1) }}%</span>
                                @endif
                                <span class="text-xs text-gray-500 ml-1 sm:ml-2 hidden xs:inline">vs mes anterior</span>
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-pink-500 to-pink-600 p-2 sm:p-3 lg:p-4 rounded-lg sm:rounded-xl ml-2 sm:ml-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Orders Card --}}
                <div
                    class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border-l-4 border-blue-500 transform transition-all hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Órdenes Completadas</p>
                            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">
                                {{ number_format($completedOrders) }}</p>
                            <div class="flex items-center mt-2">
                                @if($ordersGrowth >= 0)
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-green-600">+{{ number_format($ordersGrowth, 1) }}%</span>
                                @else
                                    <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <span
                                        class="text-sm font-semibold text-red-600">{{ number_format($ordersGrowth, 1) }}%</span>
                                @endif
                                <span class="text-xs text-gray-500 ml-1 sm:ml-2 hidden xs:inline">vs mes anterior</span>
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 p-2 sm:p-3 lg:p-4 rounded-lg sm:rounded-xl ml-2 sm:ml-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Tickets Card --}}
                <div
                    class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border-l-4 border-purple-500 transform transition-all hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Tickets Vendidos</p>
                            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">
                                {{ number_format($totalTickets) }}</p>
                            <div class="mt-2">
                                <span class="text-sm text-gray-600">Promedio: </span>
                                <span
                                    class="text-sm font-semibold text-purple-600">${{ number_format($averageOrderValue, 2) }}</span>
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 p-2 sm:p-3 lg:p-4 rounded-lg sm:rounded-xl ml-2 sm:ml-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Events Card --}}
                <div
                    class="bg-white rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 border-l-4 border-green-500 transform transition-all hover:shadow-2xl">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-gray-600">Eventos Activos</p>
                            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">
                                {{ $activeEvents }}</p>
                            <div class="mt-2">
                                <span class="text-sm text-gray-600">Total: </span>
                                <span class="text-sm font-semibold text-green-600">{{ $totalEvents }}</span>
                            </div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 p-2 sm:p-3 lg:p-4 rounded-lg sm:rounded-xl ml-2 sm:ml-4 flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Secondary Metrics --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <div
                    class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-orange-100">Tasa de Conversión</p>
                            <p class="text-2xl sm:text-3xl lg:text-4xl font-bold mt-1 sm:mt-2">
                                {{ number_format($conversionRate, 1) }}%</p>
                        </div>
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-orange-200 flex-shrink-0 ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-teal-100">Check-ins Realizados</p>
                            <p class="text-2xl sm:text-3xl lg:text-4xl font-bold mt-1 sm:mt-2">
                                {{ number_format($totalCheckins) }}</p>
                            <p class="text-xs sm:text-sm mt-1">{{ number_format($checkinRate, 1) }}% de tickets</p>
                        </div>
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-teal-200 flex-shrink-0 ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl p-4 sm:p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-medium text-indigo-100">Usuarios Registrados</p>
                            <p class="text-2xl sm:text-3xl lg:text-4xl font-bold mt-1 sm:mt-2">
                                {{ number_format($totalUsers) }}</p>
                        </div>
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-indigo-200 flex-shrink-0 ml-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Admin Quick Actions --}}
            @if(auth()->user()->hasRole('admin'))
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <a href="{{ route('admin.users.index') }}"
                        class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-100">Gestión de Usuarios</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($userStats['total']) }}</p>
                                <p class="text-sm mt-1 text-purple-100">{{ $userStats['new_today'] }} nuevos hoy</p>
                            </div>
                            <svg class="w-12 h-12 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('admin.activity-log.index') }}"
                        class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-100">Activity Log</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($recentActivity->count()) }}</p>
                                <p class="text-sm mt-1 text-blue-100">Actividades recientes</p>
                            </div>
                            <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('admin.checkins.index') }}"
                        class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white hover:shadow-2xl transition-all transform hover:scale-105">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-100">Check-ins</p>
                                <p class="text-3xl font-bold mt-2">{{ number_format($totalCheckins) }}</p>
                                <p class="text-sm mt-1 text-green-100">Ver estadísticas</p>
                            </div>
                            <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </a>
                </div>

                {{-- Alerts Section --}}
                @if(count($alerts) > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Alertas y Notificaciones</h3>
                        <div class="space-y-3">
                            @foreach($alerts as $alert)
                                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 
                                                                    @if($alert['type'] === 'warning') border-yellow-500
                                                                    @elseif($alert['type'] === 'error') border-red-500
                                                                    @else border-blue-500
                                                                    @endif">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">{{ $alert['icon'] }}</span>
                                        <p class="text-sm text-gray-900">{{ $alert['message'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Activity Log Widget --}}
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Actividad Reciente</h3>
                        <a href="{{ route('admin.activity-log.index') }}"
                            class="text-sm text-pink-600 hover:text-pink-700 font-medium">
                            Ver todo →
                        </a>
                    </div>
                    <div class="space-y-4">
                        @foreach($recentActivity->take(5) as $activity)
                            <div class="flex items-start space-x-3 pb-4 border-b border-gray-100 last:border-0">
                                <span class="text-xl flex-shrink-0">{{ $activity->icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        @if($activity->user)
                                            <span class="text-xs text-gray-500">{{ $activity->user->name }}</span>
                                            <span class="text-xs text-gray-400">•</span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Revenue Chart --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Ingresos Mensuales</h3>
                        <span class="text-sm text-gray-500">Últimos 6 meses</span>
                    </div>
                    <div class="h-80">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- Daily Sales Chart --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Ventas Diarias</h3>
                        <span class="text-sm text-gray-500">Últimos 7 días</span>
                    </div>
                    <div class="h-80">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tickets Distribution --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Distribución de Tickets por Tipo</h3>
                    <div class="h-80">
                        <canvas id="ticketsChart"></canvas>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Estadísticas Rápidas</h3>
                    <div class="space-y-4">
                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-pink-50 to-purple-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Valor Promedio</p>
                                <p class="text-2xl font-bold text-gray-900">${{ number_format($averageOrderValue, 2) }}</p>
                            </div>
                            <div class="bg-pink-500 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Tickets por Orden</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    {{ $completedOrders > 0 ? number_format($totalTickets / $completedOrders, 1) : 0 }}
                                </p>
                            </div>
                            <div class="bg-blue-500 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600">Eventos Próximos</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $upcomingEvents->count() }}</p>
                            </div>
                            <div class="bg-green-500 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tables Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Popular Events --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Eventos Más Populares</h3>
                    <div class="space-y-4">
                        @forelse($popularEvents as $index => $event)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ Str::limit($event->name, 30) }}</p>
                                        <p class="text-sm text-gray-500">{{ $event->orders_count }} órdenes</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">${{ number_format($event->revenue, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
                        @endforelse
                    </div>
                </div>

                {{-- Top Buyers --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Top Compradores</h3>
                    <div class="space-y-4">
                        @forelse($topBuyers as $index => $buyer)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ substr($buyer->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $buyer->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $buyer->orders_count }} compras</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">${{ number_format($buyer->total_spent, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No hay datos disponibles</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Recent Orders --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Órdenes Recientes</h3>
                    <div class="space-y-3">
                        @forelse($recentOrders as $order)
                            <div class="flex items-center justify-between p-3 border-l-4 border-pink-500 bg-gray-50 rounded">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $order->user->name ?? 'Usuario' }}</p>
                                    @foreach($order->events as $event)
                                        <p class="text-xs text-gray-500">
                                            {{ $event->name }}
                                        </p>
                                    @endforeach
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-sm">
                                        ${{ number_format($order->payments->sum('total') ?? 0, 2) }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No hay órdenes recientes</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Check-ins --}}
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Check-ins Recientes</h3>
                    <div class="space-y-3">
                        @forelse($recentCheckins as $checkin)
                            <div class="flex items-center justify-between p-3 border-l-4 border-green-500 bg-gray-50 rounded">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">
                                        {{ $checkin->ticket->order->event->name ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-500">{{ substr($checkin->ticket->id, 0, 8) }}...</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">{{ $checkin->scanned_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-8">No hay check-ins recientes</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Upcoming Events --}}
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Próximos Eventos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($upcomingEvents as $event)
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-pink-500 transition-colors">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-bold text-gray-900">{{ Str::limit($event->name, 25) }}</h4>
                                <span
                                    class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">
                                {{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ Str::limit($event->address, 40) }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8 col-span-3">No hay eventos próximos</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyRevenueData['months']) !!},
                datasets: [{
                    label: 'Ingresos',
                    data: {!! json_encode($monthlyRevenueData['revenues']) !!},
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
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
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
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailySalesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailySalesData['days']) !!},
                datasets: [{
                    label: 'Ventas Diarias',
                    data: {!! json_encode($dailySalesData['revenues']) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
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
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Tickets Distribution Chart
        const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ticketsCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($ticketsByType->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($ticketsByType->pluck('total_sold')) !!},
                    backgroundColor: [
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(132, 204, 22, 0.8)'
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
                            font: {
                                size: 12
                            }
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
    </script>
@endsection