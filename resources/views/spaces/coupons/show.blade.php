@extends('layouts.space')

@section('title', 'Detalle del Cupón - ' . $space->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $coupon->code }}</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Detalles del cupón de descuento</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="{{ route('spaces.coupons.edit', [$space->subdomain, $coupon]) }}" 
               class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-center">
                Editar
            </a>
            <a href="{{ route('spaces.coupons.index', $space->subdomain) }}" 
               class="w-full sm:w-auto bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-center flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Coupon Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Información del Cupón</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <dl class="grid grid-cols-1 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Código</dt>
                            <dd class="mt-1 text-base sm:text-lg text-gray-900 font-mono">{{ $coupon->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Descuento</dt>
                            <dd class="mt-1 text-xl sm:text-2xl font-bold text-green-600">{{ $coupon->discount_percentage }}%</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de Expiración</dt>
                            <dd class="mt-1 text-sm sm:text-base text-gray-900">
                                @if($coupon->expires_at)
                                    {{ \Carbon\Carbon::parse($coupon->expires_at)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-500">Sin expiración</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Creado</dt>
                            <dd class="mt-1 text-sm sm:text-base text-gray-900">
                                {{ $coupon->created_at->format('d/m/Y H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="space-y-6">
            <!-- Estado del Cupón -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Estado</h3>
                </div>
                <div class="p-4 sm:p-6">
                    @php
                        $isExpired = $coupon->expires_at && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($coupon->expires_at));
                    @endphp
                    @if($isExpired)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Expirado
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Activo
                        </span>
                    @endif
                </div>
            </div>

            <!-- Estadísticas de Uso -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Estadísticas de Uso</h3>
                </div>
                <div class="p-4 sm:p-6">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-xs sm:text-sm font-medium text-gray-500">Total de Usos</dt>
                            <dd class="text-xs sm:text-sm text-gray-900 font-semibold">{{ $coupon->payments->count() }}</dd>
                        </div>
                        @if($coupon->payments->count() > 0)
                        <div class="flex justify-between">
                            <dt class="text-xs sm:text-sm font-medium text-gray-500">Descuento Total</dt>
                            <dd class="text-xs sm:text-sm text-gray-900 font-semibold">
                                ${{ number_format($coupon->payments->sum('discount_amount'), 2) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-xs sm:text-sm font-medium text-gray-500">Ahorro Promedio</dt>
                            <dd class="text-xs sm:text-sm text-gray-900 font-semibold">
                                ${{ number_format($coupon->payments->avg('discount_amount'), 2) }}
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagos que usaron este cupón -->
    @if($coupon->payments->count() > 0)
    <div class="mt-6">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Pagos que usaron este cupón ({{ $coupon->payments->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Orden</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Cliente</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($coupon->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900">
                                #{{ substr($payment->order_id, 0, 8) }}...
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 hidden sm:table-cell">
                                {{ $payment->order->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                ${{ number_format($payment->total, 2) }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900">
                                <span class="text-green-600 font-medium">${{ number_format($payment->discount_amount, 2) }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 hidden sm:table-cell">
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

