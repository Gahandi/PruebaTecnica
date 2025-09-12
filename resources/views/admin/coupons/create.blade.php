@extends('layouts.app')

@section('title', 'Crear Cupón')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Crear Cupón</h1>
            <p class="text-gray-600">Completa la información del nuevo cupón de descuento</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
            ← Volver
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código del cupón -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Código del Cupón *
                    </label>
                    <input type="text" 
                           id="code" 
                           name="code" 
                           value="{{ old('code') }}"
                           placeholder="Ej: DESCUENTO20, VERANO2024"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                           required>
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">El código debe ser único y fácil de recordar</p>
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
                               value="{{ old('discount_percentage') }}"
                               min="1"
                               max="100"
                               placeholder="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('discount_percentage') border-red-500 @enderror"
                               required>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    @error('discount_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Entre 1% y 100%</p>
                </div>

                <!-- Fecha de expiración -->
                <div class="md:col-span-2">
                    <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Expiración
                    </label>
                    <input type="datetime-local" 
                           id="expires_at" 
                           name="expires_at" 
                           value="{{ old('expires_at') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expires_at') border-red-500 @enderror">
                    @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Deja vacío si el cupón no expira</p>
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Describe el cupón y sus condiciones de uso..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.coupons.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Crear Cupón
                </button>
            </div>
        </form>
    </div>
</div>
@endsection