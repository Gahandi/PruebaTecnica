@extends('layouts.admin')

@section('title', 'Configuración del Sistema')

@section('admin-content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        
        {{-- Encabezado --}}
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Configuración del Sistema</h1>
                <p class="mt-2 text-sm text-gray-600">Administra la configuración general de la plataforma</p>
            </div>
            <form action="{{ route('admin.settings.initialize') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Restaurar Valores
                </button>
            </form>
        </div>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Mensaje de error --}}
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- Formulario de configuraciones --}}
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            @forelse($settings as $group => $groupSettings)
                <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                        <h2 class="text-xl font-bold text-white capitalize">
                            @switch($group)
                                @case('general') General @break
                                @case('notifications') Notificaciones @break
                                @case('system') Sistema @break
                                @case('fees') Cargos @break
                                @default {{ ucfirst($group) }}
                            @endswitch
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        @foreach($groupSettings as $setting)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    @if($setting->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                
                                <div class="ml-4 w-64">
                                    @if($setting->type === 'boolean')
                                        <select name="settings[{{ $setting->key }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                            <option value="true" {{ $setting->value === 'true' || $setting->value === '1' ? 'selected' : '' }}>Sí</option>
                                            <option value="false" {{ $setting->value === 'false' || $setting->value === '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    @elseif($setting->type === 'integer')
                                        <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                    @endif
                                </div>

                                <div class="ml-3 flex space-x-2">
                                    <a href="{{ route('admin.settings.edit', $setting) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <p class="text-gray-500">No hay configuraciones registradas.</p>
                    <a href="{{ route('admin.settings.create') }}" class="mt-4 inline-block text-pink-600 hover:text-pink-800">Crear primera configuración</a>
                </div>
            @endforelse

            @if($settings->count() > 0)
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Guardar Cambios
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
