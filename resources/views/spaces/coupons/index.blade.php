@extends('layouts.space-dashboard')

@section('title', 'Cupones - ' . $space->name)

@php
    $primaryColor = $space->color_primary ?? '#ec4899';
    $secondaryColor = $space->color_secondary ?? '#8b5cf6';
@endphp

@section('content')
    <div class="p-4 md:p-6">
        <div class="max-w-7xl mx-auto space-y-6">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Cupones de Descuento</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $coupons->count() }} cupones en {{ $space->name }}</p>
                </div>
                <a href="{{ route('spaces.coupons.create', $space->subdomain) }}"
                    class="inline-flex items-center px-4 py-2 text-white rounded-lg hover:opacity-90 transition-all shadow-lg"
                    style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Cupón
                </a>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Coupons Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background: linear-gradient(135deg, {{ $primaryColor }}, {{ $secondaryColor }});">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Código</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Descuento</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden sm:table-cell">
                                    Usos</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">
                                    Reglas</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">
                                    Expira</th>
                                <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Estado</th>
                                <th class="px-4 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($coupons as $coupon)
                                @php
                                    $status = $coupon->status_badge;
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'exhausted' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                    $statusLabels = [
                                        'active' => 'Activo',
                                        'inactive' => 'Inactivo',
                                        'expired' => 'Expirado',
                                        'exhausted' => 'Agotado',
                                    ];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold font-mono"
                                            style="background: {{ $primaryColor }}20; color: {{ $primaryColor }};">
                                            {{ $coupon->code }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-xl font-bold"
                                            style="color: {{ $primaryColor }};">{{ $coupon->discount_percentage }}%</span>
                                    </td>
                                    <td class="px-4 py-4 hidden sm:table-cell">
                                        <div class="text-sm">
                                            <span class="font-semibold text-gray-900">{{ $coupon->uses_count }}</span>
                                            @if($coupon->max_uses)
                                                <span class="text-gray-400">/ {{ $coupon->max_uses }}</span>
                                            @else
                                                <span class="text-gray-400">/ ∞</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <div class="flex flex-wrap gap-1">
                                            @if($coupon->max_uses_per_user)
                                                <span
                                                    class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $coupon->max_uses_per_user }}/usuario</span>
                                            @endif
                                            @if($coupon->min_order_amount)
                                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded">Mín:
                                                    ${{ number_format($coupon->min_order_amount, 0) }}</span>
                                            @endif
                                            @if(!$coupon->max_uses_per_user && !$coupon->min_order_amount)
                                                <span class="text-xs text-gray-400">Sin reglas</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 hidden lg:table-cell text-sm text-gray-600">
                                        @if($coupon->expires_at)
                                            {{ $coupon->expires_at->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">Sin límite</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusLabels[$status] ?? ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('spaces.coupons.edit', [$space->subdomain, $coupon]) }}"
                                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                                title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('spaces.coupons.destroy', [$space->subdomain, $coupon]) }}"
                                                method="POST" onsubmit="return confirm('¿Eliminar este cupón?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                                    title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                </path>
                                            </svg>
                                            <p class="text-gray-500 text-lg font-medium">No hay cupones</p>
                                            <p class="text-gray-400 text-sm mb-4">Crea tu primer cupón de descuento</p>
                                            <a href="{{ route('spaces.coupons.create', $space->subdomain) }}"
                                                class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors"
                                                style="background: {{ $primaryColor }};">
                                                Crear Cupón
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection