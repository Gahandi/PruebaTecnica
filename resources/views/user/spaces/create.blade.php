@extends('layouts.app')

@section('title', 'Crear Espacio')

@section('content')
<div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Mi Cajón</h1>
                <p class="text-gray-600 mt-1">Crea tu cajón de eventos personalizado</p>
            </div>
            <a href="{{ route('user.spaces.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                Volver
            </a>
        </div>
    </div>

    <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-100">
        <form method="POST" action="{{ route('user.spaces.store') }}">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de tu Cajón</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Mi Cajón de Eventos"
                           oninput="updateSubdomain(this.value)">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">Tu URL personalizada</label>
                    <div class="flex">
                        <input type="text"
                               name="subdomain"
                               id="subdomain"
                               value="{{ old('subdomain') }}"
                               readonly
                               class="flex-1 border border-gray-300 rounded-l-lg px-4 py-3 bg-gray-50 text-gray-600 @error('subdomain') border-red-500 @enderror"
                               placeholder="mi-cajon-de-eventos">
                        <span class="inline-flex items-center px-3 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 rounded-r-lg">
                            .{{ config('app.url') }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Se genera automáticamente basado en el nombre de tu cajón.</p>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description"
                              id="description"
                              rows="4"
                              required
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Describe tu espacio de eventos...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('user.spaces.index') }}"
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Crear Mi Cajón
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateSubdomain(name) {
    const subdomain = name
        .toLowerCase()
        .replace(/[^a-z0-9\s]/g, '') // Solo letras, números y espacios
        .replace(/\s+/g, '-') // Reemplazar espacios con guiones
        .replace(/-+/g, '-') // Reemplazar múltiples guiones con uno solo
        .replace(/^-|-$/g, ''); // Quitar guiones al inicio y final

    document.getElementById('subdomain').value = subdomain;
}
</script>
@endsection
