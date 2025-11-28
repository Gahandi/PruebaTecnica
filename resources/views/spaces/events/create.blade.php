@extends('layouts.space')

@section('title', 'Crear Evento - ' . $space->name)

@section('content')

<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">


<div class="max-w-6xl mx-auto py-8 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Crear Nuevo Evento</h1>
                    <p class="text-blue-100 mt-2 text-lg">En {{ $space->name }}</p>
                </div>
                <a href="{{ route('spaces.profile', $space->subdomain) }}"
                   class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/30">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Volver al Cajón</span>
                    </div>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('spaces.events.store', $space->subdomain) }}" enctype="multipart/form-data" class="p-8">
            @csrf

            <div class="grid grid-cols-1 gap-12">

                <div classs="space-y-8">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Información del Evento
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-3">Nombre del Evento</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror"
                                       placeholder="Ej: Conferencia de Tecnología 2024">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-3">Descripción</label>
                                <textarea name="description" id="description" rows="4" required
                                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror"
                                          placeholder="Describe tu evento...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 mb-3">Fecha y Hora</label>
                                    <input type="datetime-local" name="date" id="date" value="{{ old('date') }}" required
                                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('date') border-red-500 @enderror">
                                    @error('date')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="type_event_id" class="block text-sm font-medium text-gray-700 mb-3">Tipo de Evento</label>
                                    <select name="type_event_id" id="type_event_id" required
                                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('type_event_id') border-red-500 @enderror">
                                        <option value="">Selecciona un tipo</option>
                                        @foreach($typeEvents as $typeEvent)
                                            <option value="{{ $typeEvent->id }}" {{ old('type_event_id') == $typeEvent->id ? 'selected' : '' }}>
                                                {{ $typeEvent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_event_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-green-50 to-emerald-100 rounded-xl p-6 mt-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ubicación del Evento
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-3">Dirección</label>
                                <input type="text" name="address" id="address" value="{{ old('address') }}" required
                                       class="w-full border-2 bg-white border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('address') border-red-500 @enderror"
                                       placeholder="Ej: Av. Reforma 123, Ciudad de México">
                                <p class="mt-2 text-sm text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Escribe una dirección o haz clic en el mapa para colocar un pin.
                                </p>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="hidden">
                                <label for="coordinates" class="block text-sm font-medium text-gray-700 mb-3">Coordenadas GPS</label>
                                <input type="text" name="coordinates" id="coordinates" value="{{ old('coordinates') }}"
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 @error('coordinates') border-red-500 @enderror"
                                       placeholder="Ej: 19.4326, -99.1332">
                                @error('coordinates')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="tags-input" class="block text-sm font-medium text-gray-700 mb-3">Etiquetas (Tags)</label>
                                <div id="tags-container" class="flex flex-wrap gap-2 mb-2 min-h-[50px] p-3 border-2 border-gray-200 rounded-xl bg-white">
                                    <!-- Los tags se agregarán aquí dinámicamente -->
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" id="tags-input" 
                                           class="flex-1 border-2 bg-white border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                                           placeholder="Escribe una etiqueta y presiona Enter">
                                    <select id="tags-select" class="border-2 bg-white border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                                        <option value="">Seleccionar tag existente</option>
                                        @foreach($tags ?? [] as $tag)
                                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Agrega etiquetas para categorizar tu evento. Presiona Enter para agregar.
                                </p>
                                <!-- Inputs ocultos para enviar los tags -->
                                <div id="tags-hidden-inputs"></div>
                            </div>

                            <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg">
                                <div id="map" style="height: 400px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-100 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Temario del Evento
                        </h2>

                        <div>
                            <textarea id="agenda" name="agenda" class="w-full rounded-xl border-2 border-gray-200 shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200" rows="12" placeholder="Escribe el temario aquí (usa Markdown)...">{{ old('agenda') }}</textarea>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-orange-50 to-yellow-100 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Imágenes del Evento
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="banner" class="block text-sm font-medium text-gray-700 mb-3">Banner del Evento (debe de ser de 1024 * 768)</label>
                                <div class="relative">
                                    <input type="file" name="banner" id="banner" accept="image/*"
                                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('banner') border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('banner')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-4">
                                    <img id="preview-banner" class="hidden w-full h-32 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Vista previa banner">
                                </div>
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-3">Imagen Principal (debe de ser de 736 * 308 )</label>
                                <div class="relative">
                                    <input type="file" name="image" id="image" accept="image/*"
                                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('image') border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-4">
                                    <img id="preview-image" class="hidden w-full h-32 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Vista previa imagen">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-3">Icono del Evento (debe de ser de 800 * 800)</label>
                            <div class="relative">
                                <input type="file" name="icon" id="icon" accept="image/*"
                                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 @error('icon') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('icon')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <div class="mt-4">
                                <img id="preview-icon" class="hidden w-24 h-24 object-cover rounded-xl border-2 border-gray-200 shadow-lg" alt="Vista previa icono">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-indigo-50 to-blue-100 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            Tipos de Boletos
                        </h2>

                        <div id="ticket-types" class="space-y-4">
                            <div class="ticket-type bg-white border-2 border-gray-200 rounded-xl p-6 shadow-sm">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Nombre del Boleto</label>
                                        <select name="ticket_types[0][name]" class="ticket-name-select w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" required>
                                            <option value="">Selecciona un tipo</option>
                                            @foreach($ticketTypes as $ticketType)
                                                <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                                            @endforeach
                                            <option value="other">Agregar otro tipo</option>
                                        </select>
                                        <input type="text" name="ticket_types[0][name_other]" class="ticket-name-input hidden w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" placeholder="Escribe el nombre del boleto">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Precio ($)</label>
                                        <input type="number" name="ticket_types[0][price]" step="0.01" min="0" required
                                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                               placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Cantidad</label>
                                        <input type="number" name="ticket_types[0][quantity]" min="1" required
                                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                               placeholder="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addTicketType()"
                                class="mt-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl font-medium flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
document.addEventListener('DOMContentLoaded', function() {
    const existingSelects = document.querySelectorAll('.ticket-name-select');
    existingSelects.forEach((select, index) => {
        // Añadir data-index para manejo de nombres
        select.dataset.index = index;
        select.addEventListener('change', function() {
            handleTicketTypeChange(this);
        });
        // Sincronizar el estado inicial (por si Laravel old() repuebla 'other')
        handleTicketTypeChange(select);
    });
});

function addTicketType() {
    const container = document.getElementById('ticket-types');
    const newTicketType = document.createElement('div');
    newTicketType.className = 'ticket-type bg-white border-2 border-gray-200 rounded-xl p-6 shadow-sm relative';

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
                <select name="ticket_types[${currentIndex}][name]" data-index="${currentIndex}" class="ticket-name-select w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" required>
                    <option value="">Selecciona un tipo</option>
                    @foreach($ticketTypes as $ticketType)
                        <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                    @endforeach
                    <option value="other">Agregar otro tipo</option>
                </select>
                <input type="text" name="ticket_types[${currentIndex}][name_other]" class="ticket-name-input hidden w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" placeholder="Escribe el nombre del boleto">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Precio ($)</label>
                <input type="number" name="ticket_types[${currentIndex}][price]" step="0.01" min="0" required
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                       placeholder="0.00">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Cantidad</label>
                <input type="number" name="ticket_types[${currentIndex}][quantity]" min="1" required
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                       placeholder="50">
            </div>
        </div>
    `;
    container.appendChild(newTicketType);

    // Agregar event listener al nuevo select
    const newSelect = newTicketType.querySelector('.ticket-name-select');
    newSelect.addEventListener('change', function() {
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

document.addEventListener('DOMContentLoaded', function() {
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
    form.addEventListener('submit', function(event) {
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
    dateInput.addEventListener('change', function() {
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
    let selectedTags = [];

    function addTag(tagName) {
        tagName = tagName.trim();
        if (!tagName || selectedTags.includes(tagName)) return;

        selectedTags.push(tagName);
        renderTags();
        updateHiddenInputs();
    }

    function removeTag(tagName) {
        selectedTags = selectedTags.filter(t => t !== tagName);
        renderTags();
        updateHiddenInputs();
    }

    function renderTags() {
        tagsContainer.innerHTML = '';
        selectedTags.forEach(tag => {
            const tagElement = document.createElement('span');
            tagElement.className = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200';
            tagElement.innerHTML = `
                ${tag}
                <button type="button" onclick="removeTagFromEvent('${tag}')" class="ml-2 text-green-600 hover:text-green-800">
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

    window.removeTagFromEvent = function(tagName) {
        removeTag(tagName);
    };

    tagsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (this.value.trim()) {
                addTag(this.value);
                this.value = '';
            }
        }
    });

    tagsSelect.addEventListener('change', function() {
        if (this.value) {
            addTag(this.value);
            this.value = '';
        }
    });
});

</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- INICIALIZACIÓN DE EASYMDE (Editor Markdown) ---
        // Instancia para Agenda
        new EasyMDE({
            element: document.getElementById("agenda"),
            spellChecker: false,
            placeholder: "Escribe el temario aquí (usa Markdown)...",
            minHeight: "250px",
        });

        // Instancia para Descripción
        const easyMDE_description = new EasyMDE({
            element: document.getElementById("description"),
            spellChecker: false,
            placeholder: "Escribe la descripción aquí...",
            minHeight: "150px",
        });

        // Esto asegura que la validación 'required' de Laravel reciba el texto.
        easyMDE_description.codemirror.on('change', () => {
            document.getElementById('description').value = easyMDE_description.value();
        });

        // --- SCRIPT DE PREVISUALIZACIÓN DE IMÁGENES ---
        const previewImage = (inputId, previewId) => {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);

            if (!input || !preview) return; // Salir si los elementos no existen

            input.addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.classList.remove("hidden");
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = "";
                    preview.classList.add("hidden");
                }
            });
        };
        previewImage("icon", "preview-icon");
        previewImage("banner", "preview-banner");
        previewImage("image", "preview-image");

        // --- MAPA INTERACTIVO (GOOGLE MAPS) ---
        // Esta función será llamada cuando Google Maps API esté cargado
        window.initMap = function() {
            const coordInput = document.getElementById('coordinates');
            const addressInput = document.getElementById('address');

            // Coordenadas por defecto (Toluca, México)
            const defaultLat = 19.2826;
            const defaultLng = -99.6556;

            let initialLat = defaultLat;
            let initialLng = defaultLng;

            // Revisar si hay coordenadas viejas (por validación de Laravel)
            if(coordInput.value) {
                const parts = coordInput.value.split(',').map(s => parseFloat(s.trim()));
                if(parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
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
                    marker.addListener('dragend', function(e) {
                        const dragLat = e.latLng.lat();
                        const dragLng = e.latLng.lng();
                        coordInput.value = `${dragLat.toFixed(6)}, ${dragLng.toFixed(6)}`;

                        // Geocodificación inversa
                        geocoder.geocode({ location: { lat: dragLat, lng: dragLng } }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                addressInput.value = results[0].formatted_address;
                            }
                        });
                    });
                }
            }

            // Colocar marcador inicial si había coordenadas
            if(coordInput.value) {
                createOrUpdateMarker(initialLat, initialLng);
            }

            // Configurar autocompletado de direcciones de Google Places
            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                componentRestrictions: { country: ['mx'] }, // Restringir a México
                fields: ['formatted_address', 'geometry', 'name']
            });

            // Cuando se selecciona una dirección del autocompletado
            autocomplete.addListener('place_changed', function() {
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
            map.addListener('click', function(e) {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();

                // Actualizar marcador
                createOrUpdateMarker(lat, lng);

                // Actualizar coordenadas
                coordInput.value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;

                // Geocodificación inversa (coordenadas -> dirección) con Google Geocoding
                geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        addressInput.value = results[0].formatted_address;
                    } else {
                        addressInput.value = 'Dirección no encontrada';
                    }
                });
            });

            // Evento al escribir manualmente en el input de dirección (geocodificación)
            let geocodeTimeout;
            addressInput.addEventListener('input', function() {
                clearTimeout(geocodeTimeout);
                const address = this.value;

                // Esperar 500ms después de que el usuario deje de escribir
                geocodeTimeout = setTimeout(function() {
                    if (address && address.length > 5 && 
                        address !== 'Dirección no encontrada' && 
                        address !== 'Error al obtener dirección') {
                        
                        geocoder.geocode({ address: address }, function(results, status) {
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
@endsection
