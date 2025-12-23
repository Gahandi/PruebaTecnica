@extends('layouts.app')

@section('title', 'Crear Espacio')

@section('content')
    <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#e24972]">Crear Mi Cajón</h1>
                    <p class="text-gray-600 mt-1">Crea tu cajón de eventos personalizado</p>
                </div>
                <a href="{{ route('user.spaces.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Volver
                </a>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-100">
            {{-- Mostrar errores generales --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium">Por favor corrige los siguientes errores:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('user.spaces.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">

                    {{-- NAME --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de tu Cajón <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-pink-500 focus:border-pink-500 @error('name') border-red-500 bg-red-50 @enderror"
                            placeholder="Mi Cajón de Eventos" oninput="updateSubdomain(this.value)">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- SUBDOMAIN --}}
                    <div>
                        <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">
                            Tu URL personalizada <span class="text-red-500">*</span>
                        </label>

                        {{-- Hidden input que enviará el valor --}}
                        <input type="hidden" name="subdomain" id="subdomain-input" value="{{ old('subdomain') }}">

                        {{-- Vista previa del subdomain --}}
                        <div
                            class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 @error('subdomain') border-red-500 bg-red-50 @enderror">
                            <span class="text-pink-600 font-medium"
                                id="subdomain-display">{{ old('subdomain') ?: 'tu-cajon' }}</span>
                            <span
                                class="text-gray-500">.{{ str_replace(['http://', 'https://'], '', config('app.url')) }}</span>
                        </div>

                        <p class="mt-1 text-sm text-gray-500">Se genera automáticamente basado en el nombre.</p>

                        @error('subdomain')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- DESCRIPTION --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-pink-500 focus:border-pink-500 @error('description') border-red-500 bg-red-50 @enderror"
                            placeholder="Describe tu espacio de eventos...">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    {{-- LOGO --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo del espacio <span class="text-red-500">*</span>
                        </label>

                        <input type="file" name="logo" accept="image/*" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-pink-500 focus:border-pink-500 @error('logo') border-red-500 bg-red-50 @enderror">

                        @error('logo')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="text-xs text-gray-500 mt-1">Formato recomendado: PNG o JPG (máx. 2MB)</p>
                    </div>

                    {{-- BANNER --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Banner del espacio <span class="text-red-500">*</span>
                        </label>

                        <input type="file" name="banner" accept="image/*" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-pink-500 focus:border-pink-500 @error('banner') border-red-500 bg-red-50 @enderror">

                        @error('banner')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="text-xs text-gray-500 mt-1">Formato recomendado: Horizontal 1200x400px (máx. 4MB)</p>
                    </div>

                </div>

                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('user.spaces.index') }}"
                        class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancelar
                    </a>

                    <button type="submit"
                        class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-6 py-3 rounded-lg transition-colors">
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
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Remover acentos
                .replace(/[^a-z0-9\s-]/g, '') // Solo letras, números, espacios y guiones
                .replace(/\s+/g, '-') // Reemplazar espacios con guiones
                .replace(/-+/g, '-') // Reemplazar múltiples guiones con uno solo
                .replace(/^-|-$/g, ''); // Quitar guiones al inicio y final

            // Actualizar el input hidden (el que se envía al servidor)
            document.getElementById('subdomain-input').value = subdomain;

            // Actualizar el texto de vista previa
            document.getElementById('subdomain-display').textContent = subdomain || 'tu-cajon';
        }
    </script>
@endsection