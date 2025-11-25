@extends('layouts.space')

@section('title', 'Cupones - ' . $space->name)

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Cupones de Descuento</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Gestiona los cupones de descuento para {{ $space->name }}</p>
        </div>
        <a href="{{ route('spaces.coupons.create', $space->subdomain) }}" 
           class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Nuevo Cupón
        </a>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if($coupons->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descuento</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usos</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Expira</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-2 sm:mr-4 flex-shrink-0">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $coupon->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $coupon->discount_percentage }}%</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $coupon->payments_count ?? 0 }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                    @if($coupon->expires_at)
                                        <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($coupon->expires_at)->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($coupon->expires_at)->format('H:i') }}</div>
                                    @else
                                        <span class="text-sm text-gray-500">Sin expiración</span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    @php
                                        $isExpired = $coupon->expires_at && \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($coupon->expires_at));
                                    @endphp
                                    @if($isExpired)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Expirado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <a href="{{ route('spaces.coupons.show', [$space->subdomain, $coupon]) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-xs sm:text-sm">
                                            Ver
                                        </a>
                                        <a href="{{ route('spaces.coupons.edit', [$space->subdomain, $coupon]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-xs sm:text-sm">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('spaces.coupons.destroy', [$space->subdomain, $coupon]) }}" 
                                              class="inline" 
                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar este cupón?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs sm:text-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12 px-4">
                <div class="w-16 h-16 sm:w-24 sm:h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay cupones</h3>
                <p class="text-sm sm:text-base text-gray-500 mb-4">Comienza creando tu primer cupón de descuento para este espacio.</p>
                <a href="{{ route('spaces.coupons.create', $space->subdomain) }}" 
                   class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Crear Cupón
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

