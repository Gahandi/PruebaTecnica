@extends('layouts.app')

@section('title', 'Editar Perfil - ' . $space->name)

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-100">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Editar Perfil del Cajón</h1>
        
        <form method="POST" action="{{ route('spaces.update', ['subdomain' => $space->subdomain]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Información Básica</h2>
                </div>
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cajón</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $space->name) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="subdomain" class="block text-sm font-medium text-gray-700 mb-2">Subdominio</label>
                    <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain', $space->subdomain) }}" readonly
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-50 text-gray-600">
                    <p class="mt-1 text-sm text-gray-500">El subdominio no se puede cambiar</p>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $space->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="about" class="block text-sm font-medium text-gray-700 mb-2">Acerca de (Descripción larga)</label>
                    <textarea name="about" id="about" rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('about') border-red-500 @enderror">{{ old('about', $space->about) }}</textarea>
                    @error('about')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Imágenes -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Imágenes</h2>
                </div>
                
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo del Cajón</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('logo') border-red-500 @enderror">
                    @if($space->logo)
                        <div class="mt-2">
                            <img src="{{ $space->logo }}" alt="Logo actual" class="w-16 h-16 rounded-full object-cover">
                            <p class="text-sm text-gray-500">Logo actual</p>
                        </div>
                    @endif
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="banner" class="block text-sm font-medium text-gray-700 mb-2">Banner del Cajón</label>
                    <input type="file" name="banner" id="banner" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('banner') border-red-500 @enderror">
                    @if($space->banner)
                        <div class="mt-2">
                            <img src="{{ $space->banner }}" alt="Banner actual" class="w-full h-24 object-cover rounded">
                            <p class="text-sm text-gray-500">Banner actual</p>
                        </div>
                    @endif
                    @error('banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Colores -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Colores del Cajón</h2>
                </div>
                
                <div>
                    <label for="color_primary" class="block text-sm font-medium text-gray-700 mb-2">Color Primario</label>
                    <input type="color" name="color_primary" id="color_primary" value="{{ old('color_primary', $space->color_primary) }}"
                           class="w-full h-12 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('color_primary') border-red-500 @enderror">
                    @error('color_primary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="color_secondary" class="block text-sm font-medium text-gray-700 mb-2">Color Secundario</label>
                    <input type="color" name="color_secondary" id="color_secondary" value="{{ old('color_secondary', $space->color_secondary) }}"
                           class="w-full h-12 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('color_secondary') border-red-500 @enderror">
                    @error('color_secondary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Información de Contacto -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Información de Contacto</h2>
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $space->location) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Sitio Web</label>
                    <input type="url" name="website" id="website" value="{{ old('website', $space->website) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('website') border-red-500 @enderror">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Email de Contacto</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', $space->contact_email) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('contact_email') border-red-500 @enderror">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono de Contacto</label>
                    <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $space->contact_phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('contact_phone') border-red-500 @enderror">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('spaces.profile', $space->subdomain) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
