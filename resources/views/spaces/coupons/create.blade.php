@extends('layouts.space-dashboard')

@section('title', 'Crear Cupón - ' . $space->name)

@php
    $primaryColor = $space->color_primary ?? '#ec4899';
    $secondaryColor = $space->color_secondary ?? '#8b5cf6';
@endphp

@section('content')
    <div class="p-4 md:p-6">
        <div class="max-w-3xl mx-auto">
            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('spaces.coupons.index', $space->subdomain) }}"
                    class="text-gray-600 hover:text-gray-900 text-sm flex items-center mb-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a cupones
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Crear Cupón</h1>
                <p class="mt-1 text-sm text-gray-600">Crea un nuevo código de descuento para {{ $space->name }}</p>
            </div>

            {{-- Formulario --}}
            <form action="{{ route('spaces.coupons.store', $space->subdomain) }}" method="POST"
                class="bg-white rounded-xl shadow-lg p-6 space-y-6">
                @csrf

                {{-- Código y Descuento --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código del Cupón *</label>
                        <input type="text" name="code" id="code" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent uppercase font-mono"
                            style="--tw-ring-color: {{ $primaryColor }};" placeholder="DESCUENTO20"
                            value="{{ old('code') }}">
                        @error('code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-1">Descuento (%)
                            *</label>
                        <input type="number" name="discount_percentage" id="discount_percentage" required min="1" max="100"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            placeholder="20" value="{{ old('discount_percentage') }}">
                        @error('discount_percentage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Reglas de Negocio</h3>
                </div>

                {{-- Fecha expiración y límites --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                            Expiración</label>
                        <input type="datetime-local" name="expires_at" id="expires_at"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            value="{{ old('expires_at') }}">
                        @error('expires_at')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-1">Límite de Usos
                            Total</label>
                        <input type="number" name="max_uses" id="max_uses" min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            placeholder="100" value="{{ old('max_uses') }}">
                        <p class="text-xs text-gray-500 mt-1">Dejar vacío = ilimitado</p>
                    </div>
                    <div>
                        <label for="max_uses_per_user" class="block text-sm font-medium text-gray-700 mb-1">Límite por
                            Usuario</label>
                        <input type="number" name="max_uses_per_user" id="max_uses_per_user" min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                            placeholder="1" value="{{ old('max_uses_per_user') }}">
                        <p class="text-xs text-gray-500 mt-1">Dejar vacío = ilimitado</p>
                    </div>
                </div>

                {{-- Monto mínimo y estado --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_order_amount" class="block text-sm font-medium text-gray-700 mb-1">Monto Mínimo de
                            Compra</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="min_order_amount" id="min_order_amount" min="0" step="0.01"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent"
                                placeholder="0.00" value="{{ old('min_order_amount') }}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Dejar vacío o 0 = sin mínimo</p>
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"
                                style="--peer-checked-bg: {{ $primaryColor }};"
                                :class="peer-checked:bg-[var(--peer-checked-bg)]"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700">Cupón Activo</span>
                        </label>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('spaces.coupons.index', $space->subdomain) }}"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 text-white rounded-lg hover:opacity-90 transition-all shadow-lg"
                        style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                        Crear Cupón
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        input:focus {
            --tw-ring-color:
                {{ $primaryColor }}
            ;
            border-color:
                {{ $primaryColor }}
            ;
        }

        .peer:checked+div {
            background-color:
                {{ $primaryColor }}
            ;
        }
    </style>
@endsection