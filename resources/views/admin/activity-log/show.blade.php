@extends('layouts.admin')

@section('title', 'Detalle del Log de Actividad')

@section('admin-content')
    <div class="container mx-auto px-6 py-6">

        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">
                    Detalle del Registro de Actividad
                </h2>

                <a href="{{ route('admin.activity-log.index') }}"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-semibold">
                    ← Volver
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div>
                    <p class="text-sm text-gray-500">ID</p>
                    <p class="font-semibold">{{ $activityLog->id }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Usuario</p>
                    <p class="font-semibold">
                        {{ $activityLog->user?->name ?? 'Sistema' }}
                        @if($activityLog->user)
                            <span class="text-xs text-gray-400">
                                ({{ $activityLog->user->email }})
                            </span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Acción</p>
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
                        $colorClass = $colorClasses[$activityLog->color] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-block px-3 py-1 text-sm rounded-full {{ $colorClass }}">
                        {{ $activityLog->action_translated }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Fecha</p>
                    <p class="font-semibold">
                        {{ $activityLog->created_at->format('d/m/Y H:i:s') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $activityLog->created_at->diffForHumans() }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Modelo</p>
                    <p class="font-semibold">
                        @if($activityLog->model_type)
                            {{ $activityLog->model_translated }} #{{ $activityLog->model_id }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Severidad</p>
                    @php
                        $severityColors = [
                            'info' => 'bg-blue-100 text-blue-800',
                            'warning' => 'bg-yellow-100 text-yellow-800',
                            'error' => 'bg-red-100 text-red-800',
                            'critical' => 'bg-red-200 text-red-900',
                        ];
                        $severityLabels = [
                            'info' => 'Información',
                            'warning' => 'Advertencia',
                            'error' => 'Error',
                            'critical' => 'Crítico',
                        ];
                    @endphp
                    <span
                        class="inline-block px-3 py-1 text-sm rounded-full {{ $severityColors[$activityLog->severity] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $severityLabels[$activityLog->severity] ?? ucfirst($activityLog->severity) }}
                    </span>
                </div>

                <div class="md:col-span-2 lg:col-span-3">
                    <p class="text-sm text-gray-500">Descripción</p>
                    <p class="font-semibold text-gray-900">
                        {{ $activityLog->description }}
                    </p>
                </div>

            </div>

            {{-- Información de conexión --}}
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Información de Conexión</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Dirección IP</p>
                        <p class="font-semibold font-mono">{{ $activityLog->ip_address ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">País</p>
                        <p class="font-semibold">{{ $activityLog->country ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ciudad</p>
                        <p class="font-semibold">{{ $activityLog->city ?? '—' }}</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <p class="text-sm text-gray-500">User Agent</p>
                        <p class="font-semibold text-sm text-gray-700 break-all">{{ $activityLog->user_agent ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Cambios realizados --}}
            @if($activityLog->formatted_changes)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Cambios Realizados</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Campo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Anterior
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor Nuevo</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activityLog->formatted_changes as $change)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $change['field'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-600">
                                            <span class="bg-red-50 px-2 py-1 rounded">{{ $change['old'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-green-600">
                                            <span class="bg-green-50 px-2 py-1 rounded">{{ $change['new'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Propiedades adicionales --}}
            @if($activityLog->properties && count($activityLog->properties) > 0)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Propiedades Adicionales</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre
                            class="text-sm text-gray-800 overflow-x-auto">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection