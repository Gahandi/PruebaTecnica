@extends('layouts.app')

@section('title', 'Crear Evento')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Evento</h1>
                <p class="text-gray-600">Completa la información del nuevo evento</p>
            </div>
            <a href="{{ route('admin.events.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                ← Volver
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <form method="POST" action="{{ route('admin.events.store') }}" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del evento -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Evento *
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                            required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha del evento -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha del Evento *
                        </label>
                        <input type="datetime-local" id="date" name="date" value="{{ old('date') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('date') border-red-500 @enderror"
                            required>
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ubicación -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                            Ubicación *
                        </label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                            required>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-center mb-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Descripción *
                            </label>
                            <span id="description-counter" class="text-sm text-gray-500">
                                <span id="description-count">0</span>/2000 caracteres
                            </span>
                        </div>
                        <textarea id="description" name="description" rows="4" maxlength="2000" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                            oninput="updateCharCount('description', 2000)">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Máximo 2000 caracteres sin incluir formato</p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agenda -->
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-center mb-2">
                            <label for="agenda" class="block text-sm font-medium text-gray-700">
                                Agenda / Temario
                            </label>
                            <span id="agenda-counter" class="text-sm text-gray-500">
                                <span id="agenda-count">0</span>/2000 caracteres
                            </span>
                        </div>
                        <textarea id="agenda" name="agenda" rows="4" maxlength="2000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('agenda') border-red-500 @enderror"
                            oninput="updateCharCount('agenda', 2000)">{{ old('agenda') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Máximo 2000 caracteres sin incluir formato</p>
                        @error('agenda')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.events.index') }}"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Crear Evento
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Función para actualizar el contador de caracteres
            function updateCharCount(fieldId, maxLength) {
                const field = document.getElementById(fieldId);
                const counter = document.getElementById(fieldId + '-count');
                const counterWrapper = document.getElementById(fieldId + '-counter');

                if (!field || !counter) return;

                const currentLength = field.value.length;
                counter.textContent = currentLength;

                // Cambiar color según el porcentaje usado
                if (currentLength > maxLength * 0.9) {
                    counterWrapper.classList.remove('text-gray-500', 'text-yellow-600');
                    counterWrapper.classList.add('text-red-600', 'font-semibold');
                } else if (currentLength > maxLength * 0.75) {
                    counterWrapper.classList.remove('text-gray-500', 'text-red-600');
                    counterWrapper.classList.add('text-yellow-600');
                } else {
                    counterWrapper.classList.remove('text-yellow-600', 'text-red-600', 'font-semibold');
                    counterWrapper.classList.add('text-gray-500');
                }
            }

            // Inicializar contadores al cargar la página
            document.addEventListener('DOMContentLoaded', function () {
                updateCharCount('description', 2000);
                updateCharCount('agenda', 2000);
            });

            // Validación del formulario antes de enviar
            document.querySelector('form').addEventListener('submit', function (e) {
                const description = document.getElementById('description');
                const agenda = document.getElementById('agenda');

                if (description && description.value.length > 2000) {
                    e.preventDefault();
                    alert('La descripción excede el límite de 2000 caracteres. Por favor, redúcela.');
                    description.focus();
                    return false;
                }

                if (agenda && agenda.value.length > 2000) {
                    e.preventDefault();
                    alert('La agenda excede el límite de 2000 caracteres. Por favor, redúcela.');
                    agenda.focus();
                    return false;
                }
            });
        </script>
    @endpush
@endsection