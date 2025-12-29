@extends('layouts.admin')

@section('title', 'Editar Tipo de Evento')

@section('admin-content')
    <div class="p-6">
        <div class="max-w-3xl mx-auto">

            {{-- Encabezado --}}
            <div class="mb-8">
                <a href="{{ route('admin.type_events.index') }}"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Tipos de Evento
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Editar Tipo de Evento</h1>
                <p class="mt-2 text-sm text-gray-600">Modifica los datos de este tipo de evento</p>
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
                    <h2 class="text-xl font-bold text-white">{{ $typeEvent->name }}</h2>
                </div>

                <form action="{{ route('admin.type_events.update', $typeEvent) }}" method="POST"
                    enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $typeEvent->name) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('description', $typeEvent->description) }}</textarea>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Imagen</label>
                        @if($typeEvent->image)
                            <div class="mb-3 flex items-center space-x-4">
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($typeEvent->image) }}"
                                    class="h-20 w-20 rounded-lg object-cover">
                                <span class="text-sm text-gray-500">Imagen actual</span>
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100">
                        <p class="mt-1 text-xs text-gray-500">Deja vacío para mantener la imagen actual. Formatos: JPG, PNG,
                            GIF. Máximo 2MB.</p>
                    </div>

                    <div class="flex justify-between pt-4 border-t">
                        <form action="{{ route('admin.type_events.destroy', $typeEvent) }}" method="POST"
                            onsubmit="return confirm('¿Estás seguro de eliminar este tipo de evento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                Eliminar
                            </button>
                        </form>

                        <div class="flex space-x-4">
                            <a href="{{ route('admin.type_events.index') }}"
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