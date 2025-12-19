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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">ID</p>
                <p class="font-semibold">{{ $activityLog->id }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Usuario</p>
                <p class="font-semibold">
                    {{ $activityLog->user?->name ?? 'Sistema' }}
                    <span class="text-xs text-gray-400">
                        ({{ $activityLog->user?->email ?? '—' }})
                    </span>
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Acción</p>
                <span class="inline-block px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                    {{ $activityLog->action }}
                </span>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha</p>
                <p class="font-semibold">
                    {{ $activityLog->created_at->format('d/m/Y H:i:s') }}
                </p>
            </div>

            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Descripción</p>
                <p class="font-semibold">
                    {{ $activityLog->description }}
                </p>
            </div>

        </div>
    </div>

</div>
@endsection
