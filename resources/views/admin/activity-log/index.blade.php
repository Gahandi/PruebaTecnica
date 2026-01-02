@extends('layouts.admin')

@section('title', 'Registro de Actividad')

@section('admin-content')
    <div class="p-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Registro de Actividad</h1>
                    <p class="mt-2 text-sm text-gray-600">Historial completo de acciones del sistema</p>
                </div>
                <a href="{{ route('admin.activity-log.export', request()->all()) }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Exportar CSV
                </a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-600">Hoy</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <p class="text-sm font-medium text-gray-600">Esta Semana</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['this_week']) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-pink-500">
                    <p class="text-sm font-medium text-gray-600">Este Mes</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['this_month']) }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Descripción..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                        <select name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Acción</label>
                        <select name="action"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            <option value="">Todas</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ \App\Models\ActivityLog::$actionTranslations[$action] ?? ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
                        <select name="model_type"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                            <option value="">Todos</option>
                            @foreach($modelTypes as $modelType)
                                @php
                                    $shortName = class_basename($modelType);
                                @endphp
                                <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                    {{ \App\Models\ActivityLog::$modelTranslations[$shortName] ?? $shortName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all">
                            Filtrar
                        </button>
                        <a href="{{ route('admin.activity-log.index') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Activity Log Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Acción</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Usuario</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Descripción</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">IP / Ubicación</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-700 uppercase">Fecha</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-700 uppercase">Detalles</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $colorClasses = [
                                                'green' => 'bg-green-100 text-green-800',
                                                'blue' => 'bg-blue-100 text-blue-800',
                                                'red' => 'bg-red-100 text-red-800',
                                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                                'purple' => 'bg-purple-100 text-purple-800',
                                                'indigo' => 'bg-indigo-100 text-indigo-800',
                                                'pink' => 'bg-pink-100 text-pink-800',
                                                'orange' => 'bg-orange-100 text-orange-800',
                                                'teal' => 'bg-teal-100 text-teal-800',
                                                'gray' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $colorClass = $colorClasses[$log->color] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                            {{ $log->action_translated }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($log->user)
                                            <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                                        @else
                                            <span class="text-sm text-gray-500">Sistema</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ Str::limit($log->description, 60) }}</div>
                                        @if($log->model_type)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $log->model_translated }} #{{ $log->model_id }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $log->ip_address ?? '-' }}</div>
                                        @if($log->city || $log->country)
                                            <div class="text-xs text-gray-500">{{ $log->city }}{{ $log->city && $log->country ? ', ' : '' }}{{ $log->country }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $log->created_at->format('d/m/Y H:i') }}
                                        <div class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.activity-log.show', $log) }}"
                                            class="text-pink-600 hover:text-pink-900 font-medium">
                                            Ver más
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="mt-4 text-lg font-medium">No se encontraron registros</p>
                                        <p class="mt-2 text-sm">Intenta ajustar los filtros de búsqueda</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection