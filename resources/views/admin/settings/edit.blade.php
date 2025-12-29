@extends('layouts.admin')

@section('title', 'Editar Configuración')

@section('admin-content')
    <div class="p-6">
        <div class="max-w-3xl mx-auto">

            {{-- Encabezado --}}
            <div class="mb-8">
                <a href="{{ route('admin.settings.index') }}"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Configuración
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Editar Configuración</h1>
                <p class="mt-2 text-sm text-gray-600">Modifica los valores de esta configuración</p>
            </div>

            {{-- Errores de validación --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <ul class="list-disc list-inside text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</h2>
                </div>

                <form action="{{ route('admin.settings.update.single', $setting) }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-2">Clave</label>
                        <input type="text" name="key" id="key" value="{{ $setting->key }}" readonly
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
                        <p class="mt-1 text-xs text-gray-500">La clave no se puede modificar</p>
                    </div>

                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Valor</label>
                        @if($setting->type === 'boolean')
                            <select name="value" id="value"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="true" {{ $setting->value === 'true' || $setting->value === '1' ? 'selected' : '' }}>
                                    Sí</option>
                                <option value="false" {{ $setting->value === 'false' || $setting->value === '0' ? 'selected' : '' }}>No</option>
                            </select>
                        @elseif($setting->type === 'integer')
                            <input type="number" name="value" id="value" value="{{ $setting->value }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        @else
                            <input type="text" name="value" id="value" value="{{ $setting->value }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        @endif
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Dato</label>
                        <select name="type" id="type" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="string" {{ $setting->type == 'string' ? 'selected' : '' }}>Texto</option>
                            <option value="boolean" {{ $setting->type == 'boolean' ? 'selected' : '' }}>Booleano (Sí/No)
                            </option>
                            <option value="integer" {{ $setting->type == 'integer' ? 'selected' : '' }}>Número Entero</option>
                            <option value="json" {{ $setting->type == 'json' ? 'selected' : '' }}>JSON</option>
                        </select>
                    </div>

                    <div>
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                        <select name="group" id="group" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="general" {{ $setting->group == 'general' ? 'selected' : '' }}>General</option>
                            <option value="notifications" {{ $setting->group == 'notifications' ? 'selected' : '' }}>
                                Notificaciones</option>
                            <option value="system" {{ $setting->group == 'system' ? 'selected' : '' }}>Sistema</option>
                            <option value="fees" {{ $setting->group == 'fees' ? 'selected' : '' }}>Cargos</option>
                        </select>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ $setting->description }}</textarea>
                    </div>

                    <div class="flex justify-between pt-4 border-t">
                        <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de eliminar esta configuración?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                Eliminar
                            </button>
                        </form>

                        <div class="flex space-x-4">
                            <a href="{{ route('admin.settings.index') }}"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection