@extends('layouts.space')

@section('title', 'Editar Cupón - ' . $space->name)

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Editar Cupón</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Modifica la información del cupón</p>
        </div>
        <a href="{{ route('spaces.coupons.index', $space->subdomain) }}" 
           class="w-full sm:w-auto bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('spaces.coupons.update', [$space->subdomain, $coupon]) }}" class="p-4 sm:p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código del cupón -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Código del Cupón *
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code', $coupon->code) }}"
                           placeholder="Ej: DESCUENTO20, VERANO2024"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">El código debe ser único para este espacio</p>
                </div>

                <!-- Porcentaje de descuento -->
                <div>
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                        Porcentaje de Descuento *
                    </label>
                    <div class="relative">
                        <input type="number" 
                               id="discount_percentage" 
                               name="discount_percentage" 
                               value="{{ old('discount_percentage', $coupon->discount_percentage) }}"
                               min="1"
                               max="100"
                               placeholder="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('discount_percentage') border-red-500 @enderror"
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">%</span>
                        </div>
                    </div>
                    @error('discount_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Entre 1% y 100%</p>
                </div>

                <!-- Fecha de expiración -->
                <div class="md:col-span-2">
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Expiración
                    </label>
                    <input type="datetime-local" 
                           id="expires_at" 
                           name="expires_at" 
                           value="{{ old('expires_at', $coupon->expires_at ? \Carbon\Carbon::parse($coupon->expires_at)->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expires_at') border-red-500 @enderror">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs sm:text-sm text-gray-500">Deja vacío si el cupón no expira</p>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('spaces.coupons.index', $space->subdomain) }}" 
                   class="w-full sm:w-auto px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Actualizar Cupón
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

