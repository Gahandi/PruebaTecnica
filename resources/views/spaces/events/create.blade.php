@extends('layouts.space-dashboard')

@section('title', 'Crear Evento - ' . $space->name)

@section('content')

    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">


    <div class="max-w-6xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-pink-500 to-pink-600 px-8 py-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Crear Nuevo Evento</h1>
                        <p class="text-blue-100 mt-2 text-lg">En {{ $space->name }}</p>
                    </div>
                    <a href="{{ route('spaces.profile', $space->subdomain) }}"
                        class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/30">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span>Volver al Cajón</span>
                        </div>
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('spaces.events.store', $space->subdomain) }}" enctype="multipart/form-data"
                class="p-8">
                @csrf

                <div class="grid grid-cols-1 gap-12">

                    <div classs="space-y-8">
                        <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                            <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Información del Evento
                            </h2>

                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-3">Nombre del
                                        Evento</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('name') border-red-500 @enderror"
                                        placeholder="Ej: Conferencia de Tecnología 2024">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700 mb-3">Descripción</label>
                                    <textarea name="description" id="description" rows="4" required
                                        class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('description') border-red-500 @enderror"
                                        placeholder="Describe tu evento...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700 mb-3">Fecha y
                                            Hora</label>
                                        <input type="datetime-local" name="date" id="date" value="{{ old('date') }}"
                                            required
                                            class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('date') border-red-500 @enderror">
                                        @error('date')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="type_event_id"
                                            class="block text-sm font-medium text-gray-700 mb-2 break-words">
                                            Tipo de Evento
                                        </label>

                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <select name="type_event_id" id="type_event_id" required
                                                class="w-full sm:flex-1 border-2 border-pink-200 rounded-xl px-4 py-3
                                                    focus:ring-2 focus:ring-pink-500 focus:border-pink-500
                                                    transition-all duration-200
                                                    @error('type_event_id') border-red-500 @enderror">
                                                <option value="">Selecciona un tipo</option>
                                                @foreach($typeEvents as $typeEvent)
                                                    <option value="{{ $typeEvent->id }}"
                                                        {{ old('type_event_id') == $typeEvent->id ? 'selected' : '' }}>
                                                        {{ $typeEvent->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="button" onclick="openCategoryModal()"
                                                class="w-full sm:w-auto
                                                    bg-gradient-to-r from-pink-500 to-pink-400
                                                    hover:from-pink-600 hover:to-pink-500
                                                    text-white px-4 py-3 rounded-xl
                                                    transition-all duration-300 shadow-md hover:shadow-lg
                                                    flex items-center justify-center space-x-2"
                                                title="Crear nueva categoría">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>

                                                <span class="sm:hidden">Nueva categoría</span>
                                                <span class="hidden sm:inline">Nueva</span>
                                            </button>
                                        </div>

                                        @error('type_event_id')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-4 sm:p-6 mt-8">
                            <h2 class="text-lg sm:text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Ubicación del Evento
                            </h2>

                            <div class="space-y-6">
                                <div>
                                    <label for="address"
                                        class="block text-sm font-medium text-gray-700 mb-3">Dirección</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}" required
                                        class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('address') border-red-500 @enderror"
                                        placeholder="Ej: Av. Reforma 123, Ciudad de México">
                                    <p class="mt-2 text-sm text-gray-500 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Escribe una dirección o haz clic en el mapa para colocar un pin.
                                    </p>
                                    @error('address')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="hidden">
                                    <label for="coordinates"
                                        class="block text-sm font-medium text-gray-700 mb-3">Coordenadas GPS</label>
                                    <input type="text" name="coordinates" id="coordinates" value="{{ old('coordinates') }}"
                                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('coordinates') border-red-500 @enderror"
                                        placeholder="Ej: 19.4326, -99.1332">
                                    @error('coordinates')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tags-input"
                                        class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        Etiquetas (Tags)
                                    </label>

                                    <!-- Input de texto con botón de agregar -->
                                    <div class="flex flex-col sm:flex-row gap-2 mb-3">
                                        <input type="text" id="tags-input"
                                            class="w-full sm:flex-1 border-2 border-pink-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm"
                                            placeholder="Escribe una etiqueta...">
                                        <button type="button" id="add-tag-btn" onclick="addTagFromInput()"
                                            class="w-full sm:w-auto justify-center bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-6 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center space-x-2 whitespace-nowrap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <span>Agregar</span>
                                        </button>
                                    </div>

                                    <!-- Select de tags existentes -->
                                    <div class="mb-4">
                                        <select id="tags-select"
                                            class="w-full border-2 border-pink-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm">
                                            <option value="">O selecciona un tag existente</option>
                                            @foreach($tags ?? [] as $tag)
                                                <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Tags agregadas -->
                                    <div id="tags-container"
                                        class="flex flex-wrap gap-2 min-h-[60px] p-4 border-2 border-pink-200 rounded-xl ">
                                        <span class="text-sm text-gray-400 italic" id="tags-empty-message">No hay etiquetas
                                            agregadas aún</span>
                                        <!-- Los tags se agregarán aquí dinámicamente -->
                                    </div>

                                    <p class="mt-2 text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Las etiquetas ayudan a que tu evento sea más fácil de encontrar.
                                    </p>
                                    <!-- Inputs ocultos para enviar los tags -->
                                    <div id="tags-hidden-inputs"></div>
                                </div>

                                <div class="rounded-xl overflow-hidden border-2 border-pink-200 shadow-lg">
                                    <div id="map" style="height: 400px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                            <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                Temario del Evento
                            </h2>

                            <div>
                                <textarea id="agenda" name="agenda"
                                    class="w-full rounded-xl border-2 border-pink-200 shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                    rows="12"
                                    placeholder="Escribe el temario aquí (usa Markdown)...">{{ old('agenda') }}</textarea>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                            <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Imágenes del Evento
                            </h2>

                            <!-- Información de proporciones -->
                            <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                <p class="text-sm text-blue-700 flex items-start">
                                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Las imágenes deben tener las proporciones indicadas. Se validará automáticamente al cargar cada imagen.</span>
                                </p>
                            </div>

                            <!-- Grid de 3 columnas para las imágenes -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                
                                <!-- ICONO (1:1) - Para el Home -->
                                <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-gray-100">
                                    <label for="icon" class="block text-sm font-semibold text-gray-800 mb-2">
                                        🏠 Icono del Evento
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">Proporción 1:1 (800 × 800 px) — Se mostrará en el home</p>
                                    
                                    <div class="relative">
                                        <input type="file" name="icon" id="icon" accept="image/*"
                                            class="w-full border-2 border-pink-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm @error('icon') border-red-500 @enderror">
                                    </div>
                                    
                                    <!-- Mensaje de validación -->
                                    <div id="icon-validation" class="mt-2 hidden">
                                        <p class="text-sm flex items-center"></p>
                                    </div>
                                    
                                    @error('icon')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    
                                    <!-- Vista Previa Realista - Simula tarjeta del Home -->
                                    <div class="mt-4">
                                        <p class="text-xs text-gray-500 mb-2 font-medium">📍 Así se verá en el home:</p>
                                        <div class="bg-gray-50 rounded-xl p-3">
                                            <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-[200px] mx-auto border border-gray-100 hover:shadow-xl transition-shadow">
                                                <!-- Imagen cuadrada 1:1 -->
                                                <div class="aspect-square bg-gray-200 relative overflow-hidden">
                                                    <img id="preview-icon" 
                                                        class="hidden w-full h-full object-cover"
                                                        alt="Vista previa icono">
                                                    <div id="icon-placeholder" class="w-full h-full flex items-center justify-center text-gray-400 bg-gradient-to-br from-purple-400 via-pink-400 to-indigo-400">
                                                        <div class="text-center text-white">
                                                            <svg class="w-10 h-10 mx-auto mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <p class="text-xs opacity-75">1:1</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Info del evento -->
                                                <div class="p-3">
                                                    <h4 id="icon-preview-title" class="text-sm font-bold text-pink-600 truncate mb-1">Nombre del Evento</h4>
                                                    <p class="text-xs text-gray-500 mb-2">📅 Fecha del evento</p>
                                                    <div class="flex items-center justify-between">
                                                        <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full">{{ $space->name }}</span>
                                                        <span class="text-xs font-bold text-green-600">$XX.XX</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BANNER (16:9) - Para detalles del evento -->
                                <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-gray-100">
                                    <label for="banner" class="block text-sm font-semibold text-gray-800 mb-2">
                                        📺 Banner del Evento
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">Proporción 16:9 (1920 × 1080 px) — Página de detalles</p>
                                    
                                    <div class="relative">
                                        <input type="file" name="banner" id="banner" accept="image/*"
                                            class="w-full border-2 border-pink-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm @error('banner') border-red-500 @enderror">
                                    </div>
                                    
                                    <!-- Mensaje de validación -->
                                    <div id="banner-validation" class="mt-2 hidden">
                                        <p class="text-sm flex items-center"></p>
                                    </div>
                                    
                                    @error('banner')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    
                                    <!-- Vista Previa Realista - Simula página de detalles estilo FB Cover -->
                                    <div class="mt-4">
                                        <p class="text-xs text-gray-500 mb-2 font-medium">📍 Así se verá en la página de detalles:</p>
                                        <div class="rounded-xl overflow-hidden shadow-lg border border-gray-200">
                                            <!-- Banner Container 16:9 -->
                                            <div class="relative bg-gray-900" style="aspect-ratio: 16/9;">
                                                <img id="preview-banner" 
                                                    class="hidden absolute inset-0 w-full h-full object-cover object-center"
                                                    alt="Vista previa banner">
                                                <div id="banner-placeholder" class="absolute inset-0 flex items-center justify-center text-gray-500 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600">
                                                    <div class="text-center text-white">
                                                        <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <p class="text-sm opacity-75">Sube una imagen 16:9</p>
                                                    </div>
                                                </div>
                                                <!-- Gradient overlay (siempre visible) -->
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent pointer-events-none"></div>
                                                <!-- Info overlay en la parte inferior -->
                                                <div class="absolute bottom-0 left-0 right-0 p-3">
                                                    <div class="backdrop-blur-sm bg-black/30 rounded-lg p-2 border border-white/20">
                                                        <div id="banner-preview-title" class="text-white text-sm font-medium truncate">Nombre del Evento</div>
                                                        <div class="flex items-center gap-2 text-white/70 text-xs mt-1">
                                                            <span>📅 Fecha</span>
                                                            <span>•</span>
                                                            <span>📍 Ubicación</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- IMAGEN PRINCIPAL (16:10) -->
                                <div class="bg-white rounded-xl p-4 shadow-sm border-2 border-gray-100">
                                    <label for="image" class="block text-sm font-semibold text-gray-800 mb-2">
                                        🖼️ Imagen Principal
                                    </label>
                                    <p class="text-xs text-gray-500 mb-3">Proporción 16:10 (1920 × 1200 px) — Banners secundarios</p>
                                    
                                    <div class="relative">
                                        <input type="file" name="image" id="image" accept="image/*"
                                            class="w-full border-2 border-pink-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm @error('image') border-red-500 @enderror">
                                    </div>
                                    
                                    <!-- Mensaje de validación -->
                                    <div id="image-validation" class="mt-2 hidden">
                                        <p class="text-sm flex items-center"></p>
                                    </div>
                                    
                                    @error('image')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    
                                    <!-- Vista Previa Contextual - 16:10 -->
                                    <div class="mt-4">
                                        <p class="text-xs text-gray-500 mb-2 font-medium">Vista previa (proporción 16:10):</p>
                                        <div class="bg-gray-100 rounded-xl p-3">
                                            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                                <div class="bg-gray-200 relative overflow-hidden" style="aspect-ratio: 16/10;">
                                                    <img id="preview-image" 
                                                        class="hidden w-full h-full object-cover"
                                                        alt="Vista previa imagen">
                                                    <div id="image-placeholder" class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="p-2">
                                                    <div class="h-2 bg-gray-200 rounded w-full mb-1"></div>
                                                    <div class="h-2 bg-gray-100 rounded w-1/2"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                            <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                    </path>
                                </svg>
                                Tipos de Boletos
                            </h2>

                            <div id="ticket-types" class="space-y-4">
                                <div class="ticket-type border-2 border-pink-200 rounded-xl p-6 shadow-sm">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Nombre del
                                                Boleto</label>
                                            <select name="ticket_types[0][name]"
                                                class="ticket-name-select w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                                required>
                                                <option value="">Selecciona un tipo</option>
                                                @foreach($ticketTypes as $ticketType)
                                                    <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                                                @endforeach
                                                <option value="other">Agregar otro tipo</option>
                                            </select>
                                            <input type="text" name="ticket_types[0][name_other]"
                                                class="ticket-name-input hidden w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                                placeholder="Escribe el nombre del boleto">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Precio ($)</label>
                                            <input type="number" name="ticket_types[0][price]" step="0.01" min="0" required
                                                class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                                placeholder="0.00">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Cantidad</label>
                                            <input type="number" name="ticket_types[0][quantity]" min="1" required
                                                class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                                placeholder="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addTicketType()"
                                class="mt-6 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Agregar Tipo de Boleto</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-12 pt-6 border-t border-gray-200">
                    <a href="{{ route('spaces.profile', $space->subdomain) }}"
                        class="px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-medium">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-8 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 py-3 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl font-medium flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Crear Evento</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&loading=async&libraries=places&callback=initMap">
        </script>

    <script>
        let ticketTypeCount = 1;

        // Función para manejar el cambio entre select e input
        function handleTicketTypeChange(selectElement) {
            const ticketTypeContainer = selectElement.closest('.ticket-type');
            const select = ticketTypeContainer.querySelector('.ticket-name-select');
            const input = ticketTypeContainer.querySelector('.ticket-name-input');

            if (select.value === 'other') {
                select.classList.add('hidden');
                // CAMBIO: Asegurarse de que el input tenga el nombre correcto
                select.name = `ticket_types[${select.dataset.index}][name_id]`;
                input.name = `ticket_types[${select.dataset.index}][name]`;
                input.classList.remove('hidden');
                input.required = true;
                input.focus();
            } else {
                select.classList.remove('hidden');
                select.name = `ticket_types[${select.dataset.index}][name]`;
                input.classList.add('hidden');
                input.required = false;
                input.value = '';
                input.name = `ticket_types[${select.dataset.index}][name_other]`;
            }
        }

        // Agregar event listeners a los selects existentes
        document.addEventListener('DOMContentLoaded', function () {
            const existingSelects = document.querySelectorAll('.ticket-name-select');
            existingSelects.forEach((select, index) => {
                // Añadir data-index para manejo de nombres
                select.dataset.index = index;
                select.addEventListener('change', function () {
                    handleTicketTypeChange(this);
                });
                // Sincronizar el estado inicial (por si Laravel old() repuebla 'other')
                handleTicketTypeChange(select);
            });
        });

        function addTicketType() {
            const container = document.getElementById('ticket-types');
            const newTicketType = document.createElement('div');
            newTicketType.className = 'ticket-type border-2 border-pink-200 rounded-xl p-6 shadow-sm relative';

            const currentIndex = ticketTypeCount; // Capturar el índice actual

            newTicketType.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <h3 class="font-semibold text-gray-900 text-lg">Tipo de Boleto ${currentIndex + 1}</h3>
                    <button type="button" onclick="removeTicketType(this)"
                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-all duration-200 absolute top-4 right-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Nombre del Boleto</label>
                        <select name="ticket_types[${currentIndex}][name]" data-index="${currentIndex}" class="ticket-name-select w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" required>
                            <option value="">Selecciona un tipo</option>
                            @foreach($ticketTypes as $ticketType)
                                <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                            @endforeach
                            <option value="other">Agregar otro tipo</option>
                        </select>
                        <input type="text" name="ticket_types[${currentIndex}][name_other]" class="ticket-name-input hidden w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" placeholder="Escribe el nombre del boleto">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Precio ($)</label>
                        <input type="number" name="ticket_types[${currentIndex}][price]" step="0.01" min="0" required
                               class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                               placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Cantidad</label>
                        <input type="number" name="ticket_types[${currentIndex}][quantity]" min="1" required
                               class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                               placeholder="50">
                    </div>
                </div>
            `;
            container.appendChild(newTicketType);

            // Agregar event listener al nuevo select
            const newSelect = newTicketType.querySelector('.ticket-name-select');
            newSelect.addEventListener('change', function () {
                handleTicketTypeChange(this);
            });

            ticketTypeCount++;
        }

        function removeTicketType(button) {
            button.closest('.ticket-type').remove();
            // Nota: Esto puede dejar huecos en los índices (ej: 0, 1, 3).
            // Laravel PHP manejará esto bien, pero si JS dependiera de índices consecutivos, se necesitaría re-indexar.
        }
        // --- Validación de Fecha y Hora ---

        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('date');
            const form = dateInput.closest('form');

            // 1. Establecer el valor mínimo para evitar fechas pasadas en navegadores modernos (aunque no bloquea el envío)
            // El formato debe ser 'YYYY-MM-DDTHH:MM' (ISO 8601 local)
            function setMinDate() {
                // Obtener la fecha y hora actual, en formato ISO, y recortar los segundos y milisegundos
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Convertir a UTC para luego usar la zona horaria local
                let isoNow = now.toISOString().slice(0, 16);

                // Establecer el atributo 'min' en el input datetime-local
                dateInput.min = isoNow;
            }

            setMinDate();

            // 2. Agregar un listener al formulario para prevenir el envío si la fecha no es válida
            form.addEventListener('submit', function (event) {
                // Crear objetos Date para la validación
                const selectedDate = new Date(dateInput.value);
                const currentDate = new Date();

                // Limpiar el mensaje de error anterior si existe
                const existingError = dateInput.parentNode.querySelector('.date-validation-error');
                if (existingError) {
                    existingError.remove();
                }

                // Si la fecha seleccionada es anterior o igual a la fecha/hora actual
                // Añadimos un pequeño margen (ej. 1 minuto) para la validación del lado del cliente
                // O más sencillo, comparamos el valor del input con el 'min' establecido.
                // Pero para ser explícitos y seguir tu regla:

                // Compara si la fecha seleccionada es menor o igual al momento actual
                // Restamos 1 minuto (60000 ms) para asegurar que el presente inmediato también falle si es necesario.
                if (selectedDate <= currentDate) {
                    event.preventDefault(); // Detener el envío del formulario

                    // Mostrar el mensaje de error personalizado
                    const errorMessage = document.createElement('p');
                    errorMessage.className = 'mt-2 text-sm text-red-600 flex items-center date-validation-error';
                    errorMessage.innerHTML = `
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        No se puede crear un evento con fecha anterior o igual a la de hoy.
                    `;

                    // Insertar el mensaje justo después del input de fecha
                    dateInput.parentNode.appendChild(errorMessage);

                    // Opcional: enfocar el input para que el usuario sepa dónde está el problema
                    dateInput.focus();
                }
            });

            // 3. (Opcional) Limpiar el error cuando el usuario cambie la fecha
            dateInput.addEventListener('change', function () {
                const existingError = dateInput.parentNode.querySelector('.date-validation-error');
                if (existingError) {
                    existingError.remove();
                }
            });

            // --- Manejo de Tags ---
            const tagsContainer = document.getElementById('tags-container');
            const tagsInput = document.getElementById('tags-input');
            const tagsSelect = document.getElementById('tags-select');
            const tagsHiddenInputs = document.getElementById('tags-hidden-inputs');
            const tagsEmptyMessage = document.getElementById('tags-empty-message');
            let selectedTags = [];

            function addTag(tagName) {
                tagName = tagName.trim();
                if (!tagName || selectedTags.includes(tagName)) {
                    // Mostrar feedback visual si el tag ya existe
                    if (selectedTags.includes(tagName)) {
                        tagsInput.classList.add('border-red-500');
                        setTimeout(() => {
                            tagsInput.classList.remove('border-red-500');
                        }, 1000);
                    }
                    return;
                }

                selectedTags.push(tagName);
                renderTags();
                updateHiddenInputs();
                tagsInput.value = '';
                tagsInput.focus();
            }

            function removeTag(tagName) {
                selectedTags = selectedTags.filter(t => t !== tagName);
                renderTags();
                updateHiddenInputs();
            }

            function renderTags() {
                tagsContainer.innerHTML = '';

                if (selectedTags.length === 0) {
                    tagsContainer.innerHTML = '<span class="text-sm text-gray-400 italic" id="tags-empty-message">No hay etiquetas agregadas aún</span>';
                    return;
                }

                selectedTags.forEach(tag => {
                    const tagElement = document.createElement('span');
                    tagElement.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gradient-to-r from-pink-100 to-emerald-100 text-pink-800 border border-pink-300 shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105';
                    tagElement.innerHTML = `
                        <svg class="w-3 h-3 mr-1.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        ${tag}
                        <button type="button" onclick="removeTagFromEvent('${tag.replace(/'/g, "\\'")}')" 
                                class="ml-2 text-pink-700 hover:text-red-600 hover:bg-red-50 rounded-full p-0.5 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    tagsContainer.appendChild(tagElement);
                });
            }

            function updateHiddenInputs() {
                tagsHiddenInputs.innerHTML = '';
                selectedTags.forEach((tag, index) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `tags[${index}]`;
                    input.value = tag;
                    tagsHiddenInputs.appendChild(input);
                });
            }

            // Función para agregar tag desde el botón
            window.addTagFromInput = function () {
                const tagValue = tagsInput.value.trim();
                if (tagValue) {
                    addTag(tagValue);
                }
            };

            window.removeTagFromEvent = function (tagName) {
                removeTag(tagName);
            };

            // Agregar tag con Enter
            tagsInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTagFromInput();
                }
            });

            // Agregar tag desde el select
            tagsSelect.addEventListener('change', function () {
                if (this.value) {
                    addTag(this.value);
                    this.value = '';
                }
            });
        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // --- INICIALIZACIÓN DE EASYMDE (Editor Markdown) ---
            
            // Configuración común para los editores
            const editorToolbar = [
                "bold", "italic", "strikethrough", "|",
                "heading-1", "heading-2", "heading-3", "|",
                "unordered-list", "ordered-list", "checklist", "|",
                "quote", "code", "horizontal-rule", "|",
                "link", "image", "table", "|",
                "preview", "side-by-side", "fullscreen", "|",
                "guide"
            ];
            
            // CSS personalizado para que la previsualización coincida con la vista final
            const previewStyles = `
                .editor-preview, .EasyMDEContainer .editor-preview-side {
                    font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
                    padding: 1.5rem;
                    background: linear-gradient(to bottom right, rgba(255,255,255,0.7), rgba(255,255,255,0.6), rgba(251,231,239,0.4));
                    border-radius: 0.75rem;
                    border: 1px solid rgba(236,72,153,0.2);
                }
                .editor-preview h1, .EasyMDEContainer .editor-preview-side h1 { 
                    font-size: 2rem; font-weight: 700; color: #111827; margin-bottom: 1rem; 
                    border-bottom: 2px solid #ec4899; padding-bottom: 0.5rem;
                }
                .editor-preview h2, .EasyMDEContainer .editor-preview-side h2 { 
                    font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 0.75rem; 
                }
                .editor-preview h3, .EasyMDEContainer .editor-preview-side h3 { 
                    font-size: 1.25rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; 
                }
                .editor-preview p, .EasyMDEContainer .editor-preview-side p { 
                    color: #374151; line-height: 1.75; margin-bottom: 1rem; 
                }
                .editor-preview strong, .EasyMDEContainer .editor-preview-side strong { 
                    font-weight: 600; color: #111827; 
                }
                .editor-preview a, .EasyMDEContainer .editor-preview-side a { 
                    color: #ec4899; text-decoration: underline; 
                }
                .editor-preview a:hover, .EasyMDEContainer .editor-preview-side a:hover { 
                    color: #be185d; 
                }
                .editor-preview code, .EasyMDEContainer .editor-preview-side code {
                    background: rgba(139,92,246,0.1); color: #7c3aed; 
                    padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.875rem;
                }
                .editor-preview pre, .EasyMDEContainer .editor-preview-side pre {
                    background: #1f2937; color: #f3f4f6; padding: 1rem; 
                    border-radius: 0.5rem; overflow-x: auto; margin: 1rem 0;
                }
                .editor-preview pre code, .EasyMDEContainer .editor-preview-side pre code {
                    background: transparent; color: inherit; padding: 0;
                }
                .editor-preview blockquote, .EasyMDEContainer .editor-preview-side blockquote {
                    border-left: 4px solid #ec4899; background: rgba(251,231,239,0.5);
                    padding: 0.75rem 1rem; margin: 1rem 0; color: #1f2937; font-style: italic;
                }
                .editor-preview ul, .EasyMDEContainer .editor-preview-side ul { 
                    list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; color: #374151;
                }
                .editor-preview ol, .EasyMDEContainer .editor-preview-side ol { 
                    list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; color: #374151;
                }
                .editor-preview li, .EasyMDEContainer .editor-preview-side li { 
                    margin-bottom: 0.5rem; line-height: 1.75;
                }
                .editor-preview li::marker, .EasyMDEContainer .editor-preview-side li::marker {
                    color: #ec4899;
                }
                .editor-preview table, .EasyMDEContainer .editor-preview-side table {
                    width: 100%; border-collapse: collapse; margin: 1rem 0;
                }
                .editor-preview th, .EasyMDEContainer .editor-preview-side th {
                    background: #fce7f3; color: #831843; padding: 0.75rem; 
                    border: 1px solid #f9a8d4; text-align: left; font-weight: 600;
                }
                .editor-preview td, .EasyMDEContainer .editor-preview-side td {
                    padding: 0.75rem; border: 1px solid #e5e7eb;
                }
                .editor-preview hr, .EasyMDEContainer .editor-preview-side hr {
                    border: none; border-top: 2px solid #f9a8d4; margin: 1.5rem 0;
                }
                .editor-preview img, .EasyMDEContainer .editor-preview-side img {
                    max-width: 100%; border-radius: 0.5rem; margin: 1rem 0;
                }
                /* Estilo para el checklist */
                .editor-preview input[type="checkbox"], .EasyMDEContainer .editor-preview-side input[type="checkbox"] {
                    accent-color: #ec4899; margin-right: 0.5rem;
                }
            `;
            
            // Inyectar estilos personalizados
            const styleSheet = document.createElement("style");
            styleSheet.textContent = previewStyles;
            document.head.appendChild(styleSheet);
            
            // Instancia para Agenda (Temario)
            const easyMDE_agenda = new EasyMDE({
                element: document.getElementById("agenda"),
                spellChecker: false,
                placeholder: "# Temario del Evento\n\n## Módulo 1: Introducción\n- Punto 1\n- Punto 2\n\n## Módulo 2: Desarrollo\n1. Primer tema\n2. Segundo tema\n\n> Tip: Usa Markdown para dar formato",
                minHeight: "300px",
                maxHeight: "500px",
                toolbar: editorToolbar,
                status: ["autosave", "lines", "words", "cursor"],
                autosave: {
                    enabled: true,
                    uniqueId: "agenda_{{ $space->id ?? 'new' }}",
                    delay: 5000,
                    text: "Guardado automático: "
                },
                previewClass: ["editor-preview", "prose", "prose-pink"],
                sideBySideFullscreen: false,
                shortcuts: {
                    "toggleBold": "Cmd-B",
                    "toggleItalic": "Cmd-I",
                    "toggleHeadingSmaller": "Cmd-H",
                    "toggleHeadingBigger": "Shift-Cmd-H",
                    "togglePreview": "Cmd-P",
                    "toggleSideBySide": "F9",
                    "toggleFullScreen": "F11"
                }
            });

            // Instancia para Descripción
            const easyMDE_description = new EasyMDE({
                element: document.getElementById("description"),
                spellChecker: false,
                placeholder: "Escribe una descripción atractiva de tu evento...\n\n**Destaca** los puntos más importantes.\n\n- Qué aprenderán\n- Quién debería asistir\n- Qué incluye",
                minHeight: "250px",
                maxHeight: "400px",
                toolbar: editorToolbar,
                status: ["autosave", "lines", "words", "cursor"],
                autosave: {
                    enabled: true,
                    uniqueId: "description_{{ $space->id ?? 'new' }}",
                    delay: 5000,
                    text: "Guardado automático: "
                },
                previewClass: ["editor-preview", "prose", "prose-pink"],
                sideBySideFullscreen: false,
                shortcuts: {
                    "toggleBold": "Cmd-B",
                    "toggleItalic": "Cmd-I",
                    "toggleHeadingSmaller": "Cmd-H",
                    "toggleHeadingBigger": "Shift-Cmd-H",
                    "togglePreview": "Cmd-P",
                    "toggleSideBySide": "F9",
                    "toggleFullScreen": "F11"
                }
            });

            // Sincronizar con el textarea oculto para validación de Laravel
            easyMDE_agenda.codemirror.on('change', () => {
                document.getElementById('agenda').value = easyMDE_agenda.value();
            });
            
            easyMDE_description.codemirror.on('change', () => {
                document.getElementById('description').value = easyMDE_description.value();
            });

            // --- SCRIPT DE PREVISUALIZACIÓN DE IMÁGENES CON VALIDACIÓN DE ASPECT RATIO ---
            
            // Configuración de proporciones esperadas para cada imagen
            const aspectRatioConfig = {
                'icon': { ratio: 1, name: '1:1', tolerance: 0.05 },
                'banner': { ratio: 16/9, name: '16:9', tolerance: 0.08 },
                'image': { ratio: 16/10, name: '16:10', tolerance: 0.08 }
            };
            
            // Función para validar la proporción de una imagen
            function validateAspectRatio(width, height, expectedRatio, tolerance) {
                const actualRatio = width / height;
                const difference = Math.abs(actualRatio - expectedRatio) / expectedRatio;
                return difference <= tolerance;
            }
            
            // Función mejorada de previsualización con validación
            const previewImageWithValidation = (inputId, previewId, placeholderId, validationId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                const validationDiv = document.getElementById(validationId);
                
                if (!input || !preview) return;
                
                const config = aspectRatioConfig[inputId];
                
                input.addEventListener("change", (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            // Crear imagen temporal para obtener dimensiones
                            const img = new Image();
                            img.onload = function() {
                                const width = this.width;
                                const height = this.height;
                                const actualRatio = (width / height).toFixed(2);
                                
                                // Validar proporción
                                const isValid = validateAspectRatio(width, height, config.ratio, config.tolerance);
                                
                                // Actualizar vista previa
                                preview.src = e.target.result;
                                preview.classList.remove("hidden");
                                if (placeholder) placeholder.classList.add("hidden");
                                
                                // Mostrar mensaje de validación
                                if (validationDiv) {
                                    validationDiv.classList.remove("hidden");
                                    const p = validationDiv.querySelector('p');
                                    
                                    if (isValid) {
                                        p.className = 'text-sm flex items-center text-green-600';
                                        p.innerHTML = `
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            ✓ Proporción correcta (${config.name}) — ${width}×${height}px
                                        `;
                                        input.classList.remove('border-red-500');
                                        input.classList.add('border-green-500');
                                    } else {
                                        p.className = 'text-sm flex items-center text-amber-600';
                                        p.innerHTML = `
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            ⚠ Proporción diferente: detectada ${actualRatio}:1 (esperada ${config.name}) — ${width}×${height}px
                                        `;
                                        input.classList.remove('border-green-500');
                                        input.classList.add('border-amber-500');
                                    }
                                }
                            };
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.src = "";
                        preview.classList.add("hidden");
                        if (placeholder) placeholder.classList.remove("hidden");
                        if (validationDiv) validationDiv.classList.add("hidden");
                        input.classList.remove('border-green-500', 'border-red-500', 'border-amber-500');
                    }
                });
            };
            
            // Inicializar previsualización con validación para cada input
            previewImageWithValidation("icon", "preview-icon", "icon-placeholder", "icon-validation");
            previewImageWithValidation("banner", "preview-banner", "banner-placeholder", "banner-validation");
            previewImageWithValidation("image", "preview-image", "image-placeholder", "image-validation");
            
            // --- SINCRONIZACIÓN DEL NOMBRE DEL EVENTO EN LAS PREVISUALIZACIONES ---
            const eventNameInput = document.getElementById('name');
            const iconPreviewTitle = document.getElementById('icon-preview-title');
            const bannerPreviewTitle = document.getElementById('banner-preview-title');
            
            if (eventNameInput) {
                eventNameInput.addEventListener('input', function() {
                    const eventName = this.value.trim() || 'Nombre del Evento';
                    if (iconPreviewTitle) iconPreviewTitle.textContent = eventName;
                    if (bannerPreviewTitle) bannerPreviewTitle.textContent = eventName;
                });
            }

            // --- MAPA INTERACTIVO (GOOGLE MAPS) ---
            // Esta función será llamada cuando Google Maps API esté cargado
            window.initMap = function () {
                const coordInput = document.getElementById('coordinates');
                const addressInput = document.getElementById('address');

                // Coordenadas por defecto (Toluca, México)
                const defaultLat = 19.2826;
                const defaultLng = -99.6556;

                let initialLat = defaultLat;
                let initialLng = defaultLng;

                // Revisar si hay coordenadas viejas (por validación de Laravel)
                if (coordInput.value) {
                    const parts = coordInput.value.split(',').map(s => parseFloat(s.trim()));
                    if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                        initialLat = parts[0];
                        initialLng = parts[1];
                    }
                }

                // Inicializar el mapa de Google Maps
                const map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: initialLat, lng: initialLng },
                    zoom: 15,
                    mapTypeControl: true,
                    streetViewControl: true,
                    fullscreenControl: true
                });

                let marker = null;
                let geocoder = new google.maps.Geocoder();

                // Función helper para crear o actualizar marcador y agregar listener de dragend
                function createOrUpdateMarker(lat, lng) {
                    if (marker) {
                        marker.setPosition({ lat: lat, lng: lng });
                    } else {
                        marker = new google.maps.Marker({
                            position: { lat: lat, lng: lng },
                            map: map,
                            draggable: true
                        });

                        // Agregar listener de dragend cuando se crea el marcador
                        marker.addListener('dragend', function (e) {
                            const dragLat = e.latLng.lat();
                            const dragLng = e.latLng.lng();
                            coordInput.value = `${dragLat.toFixed(6)}, ${dragLng.toFixed(6)}`;

                            // Geocodificación inversa
                            geocoder.geocode({ location: { lat: dragLat, lng: dragLng } }, function (results, status) {
                                if (status === 'OK' && results[0]) {
                                    addressInput.value = results[0].formatted_address;
                                }
                            });
                        });
                    }
                }

                // Colocar marcador inicial si había coordenadas
                if (coordInput.value) {
                    createOrUpdateMarker(initialLat, initialLng);
                }

                // Configurar autocompletado de direcciones de Google Places
                const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                    componentRestrictions: { country: ['mx'] }, // Restringir a México
                    fields: ['formatted_address', 'geometry', 'name']
                });

                // Cuando se selecciona una dirección del autocompletado
                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();

                    if (!place.geometry) {
                        console.log('No se encontró información de ubicación para la dirección seleccionada.');
                        return;
                    }

                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();

                    // Actualizar coordenadas
                    coordInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                    // Actualizar marcador
                    createOrUpdateMarker(lat, lng);

                    // Centrar el mapa en la ubicación seleccionada
                    map.setCenter({ lat: lat, lng: lng });
                    map.setZoom(15);

                    // Actualizar el input de dirección con la dirección formateada
                    addressInput.value = place.formatted_address;
                });

                // Evento al hacer clic en el mapa
                map.addListener('click', function (e) {
                    const lat = e.latLng.lat();
                    const lng = e.latLng.lng();

                    // Actualizar marcador
                    createOrUpdateMarker(lat, lng);

                    // Actualizar coordenadas
                    coordInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                    // Geocodificación inversa (coordenadas -> dirección) con Google Geocoding
                    geocoder.geocode({ location: { lat: lat, lng: lng } }, function (results, status) {
                        if (status === 'OK' && results[0]) {
                            addressInput.value = results[0].formatted_address;
                        } else {
                            addressInput.value = 'Dirección no encontrada';
                        }
                    });
                });

                // Evento al escribir manualmente en el input de dirección (geocodificación)
                let geocodeTimeout;
                addressInput.addEventListener('input', function () {
                    clearTimeout(geocodeTimeout);
                    const address = this.value;

                    // Esperar 500ms después de que el usuario deje de escribir
                    geocodeTimeout = setTimeout(function () {
                        if (address && address.length > 5 &&
                            address !== 'Dirección no encontrada' &&
                            address !== 'Error al obtener dirección') {

                            geocoder.geocode({ address: address }, function (results, status) {
                                if (status === 'OK' && results[0]) {
                                    const lat = results[0].geometry.location.lat();
                                    const lng = results[0].geometry.location.lng();

                                    coordInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                                    // Actualizar marcador usando la función helper
                                    createOrUpdateMarker(lat, lng);

                                    map.setCenter({ lat: lat, lng: lng });
                                    map.setZoom(15);
                                }
                            });
                        }
                    }, 500);
                });
            };
        });
    </script>

    <!-- Category Creation Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-pink-500 to-pink-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        Nueva Categoría
                    </h3>
                    <button type="button" onclick="closeCategoryModal()"
                        class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="categoryForm" class="p-6 space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="category_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Categoría
                        *</label>
                    <input type="text" id="category_name" name="name" required
                        class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                        placeholder="Ej: Conciertos, Teatro, Conferencias...">
                    <p id="category_name_error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>

                <!-- Category Image -->
                <div>
                    <label for="category_image" class="block text-sm font-medium text-gray-700 mb-2">Imagen
                        (Opcional)</label>
                    <div class="relative">
                        <input type="file" id="category_image" name="image" accept="image/*"
                            class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                            onchange="previewCategoryImage(this)">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Formatos: JPG, PNG, GIF, WebP. Máximo 2MB.</p>
                    <!-- Image Preview -->
                    <div id="category_image_preview_container" class="mt-3 hidden">
                        <img id="category_image_preview"
                            class="w-full h-32 object-cover rounded-xl border-2 border-pink-200 shadow-md"
                            alt="Vista previa">
                    </div>
                </div>

                <!-- Error Message -->
                <div id="category_form_error"
                    class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm"></div>

                <!-- Modal Footer -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeCategoryModal()"
                        class="px-5 py-2.5 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-200 font-medium">
                        Cancelar
                    </button>
                    <button type="submit" id="saveCategoryBtn"
                        class="px-5 py-2.5 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white rounded-xl transition-all duration-300 shadow-md hover:shadow-lg font-medium flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Crear Categoría</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Category Modal Functions
        function openCategoryModal() {
            const modal = document.getElementById('categoryModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('category_name').focus();
        }

        function closeCategoryModal() {
            const modal = document.getElementById('categoryModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            // Reset form
            document.getElementById('categoryForm').reset();
            document.getElementById('category_image_preview_container').classList.add('hidden');
            document.getElementById('category_name_error').classList.add('hidden');
            document.getElementById('category_form_error').classList.add('hidden');
        }

        function previewCategoryImage(input) {
            const preview = document.getElementById('category_image_preview');
            const container = document.getElementById('category_image_preview_container');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                container.classList.add('hidden');
            }
        }

        // Handle category form submission
        document.getElementById('categoryForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('saveCategoryBtn');
            const errorDiv = document.getElementById('category_form_error');
            const nameError = document.getElementById('category_name_error');

            // Reset errors
            errorDiv.classList.add('hidden');
            nameError.classList.add('hidden');

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Creando...</span>
        `;

            try {
                const formData = new FormData(this);

                const response = await fetch('/categories', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Add new category to select dropdown
                    const select = document.getElementById('type_event_id');
                    const option = document.createElement('option');
                    option.value = data.category.id;
                    option.textContent = data.category.name;
                    option.selected = true;
                    select.appendChild(option);

                    // Close modal
                    closeCategoryModal();

                    // Show success message (optional toast)
                    alert('¡Categoría "' + data.category.name + '" creada exitosamente!');
                } else {
                    // Show errors
                    if (data.errors && data.errors.name) {
                        nameError.textContent = data.errors.name[0];
                        nameError.classList.remove('hidden');
                    } else {
                        errorDiv.textContent = data.message || 'Error al crear la categoría';
                        errorDiv.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.textContent = 'Error de conexión. Inténtalo de nuevo.';
                errorDiv.classList.remove('hidden');
            } finally {
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Crear Categoría</span>
            `;
            }
        });

        // Close modal on backdrop click
        document.getElementById('categoryModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('categoryModal');
                if (!modal.classList.contains('hidden')) {
                    closeCategoryModal();
                }
            }
        });
    </script>
@endsection