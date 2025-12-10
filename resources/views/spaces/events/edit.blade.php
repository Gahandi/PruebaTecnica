@extends('layouts.app') {{-- Asumo que usas un layout llamado app.blade.php --}}

@section('title', 'Editar Evento - ' . $event->name)

@section('content')
<link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">

<div class="max-w-6xl mx-auto py-8 sm:px-6 lg:px-8">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-100">
        {{-- Encabezado de la vista --}}
        <div class="bg-gradient-to-r from-pink-500 to-pink-600 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Editar Evento: {{ $event->name }}</h1>
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

        {{-- FORMULARIO DE EDICIÓN --}}
        <form method="POST"
              action="{{ route('spaces.events.update', ['subdomain' => $space->subdomain, 'event' => $event->slug]) }}"
              enctype="multipart/form-data"
              class="p-8">
            @csrf
            @method('PUT') {{-- NECESARIO para que Laravel sepa que es una actualización --}}

            <div class="grid grid-cols-1 gap-12">

                {{-- Columna 1: Info y Ubicación --}}
                <div classs="space-y-8">
                    {{-- Bloque: Información del Evento --}}
                    <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Información del Evento
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-3">Nombre del Evento</label>
                                <input type="text" name="name" id="name"
                                       value="{{ old('name', $event->name) }}" required
                                       class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('name') border-red-500 @enderror"
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
                                          class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('description') border-red-500 @enderror"
                                          placeholder="Describe tu evento...">{{ old('description', $event->description) }}</textarea>
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
                                    {{-- Formatear la fecha para input datetime-local --}}
                                    <input type="datetime-local" name="date" id="date"
                                           value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i')) }}" required
                                           class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('description') border-red-500 @enderror">
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
                                    class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('description') border-red-500 @enderror">
                                        <option value="">Selecciona un tipo</option>
                                        @foreach($typeEvents as $typeEvent)
                                            <option value="{{ $typeEvent->id }}"
                                                {{ old('type_event_id', $event->type_events_id) == $typeEvent->id ? 'selected' : '' }}>
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

                    {{-- Bloque: Ubicación del Evento --}}
                    <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-4 sm:p-6 mt-8"> 
                        <h2 class="text-lg sm:text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Ubicación del Evento
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-3">Dirección</label>
                                <input type="text" name="address" id="address"
                                    value="{{ old('address', $event->address) }}" required
                                    class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('address') border-red-500 @enderror"
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
                                <input type="text" name="coordinates" id="coordinates"
                                    value="{{ old('coordinates', $event->coordinates) }}"
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
                                <label for="tags-input" class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Etiquetas (Tags)
                                </label>
                                
                                <div class="flex flex-col sm:flex-row gap-2 mb-3">
                                    
                                    <input type="text" id="tags-input" 
                                        class="w-full sm:flex-1 border-2 border-pink-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm"
                                        placeholder="Escribe una etiqueta...">
                                    
                                    <button type="button" id="add-tag-btn" onclick="addTagFromInput()"
                                    class="w-full sm:w-auto justify-center bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-6 py-2.5 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center space-x-2 whitespace-nowrap">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <span>Agregar</span>
                                    </button>
                                </div>

                                <div class="mb-4">
                                    <select id="tags-select" 
                                    class="w-full border-2 border-pink-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 text-sm">
                                        <option value="">O selecciona un tag existente</option>
                                        @foreach($tags ?? [] as $tag)
                                            <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="tags-container" class="flex flex-wrap gap-2 min-h-[60px] p-4 border-2 border-pink-200 rounded-xl ">
                                    <span class="text-sm text-gray-400 italic" id="tags-empty-message">No hay etiquetas agregadas aún</span>
                                    </div>

                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Las etiquetas ayudan a que tu evento sea más fácil de encontrar.
                                </p>
                                <div id="tags-hidden-inputs"></div>
                            </div>

                            <div class="rounded-xl overflow-hidden border-2 border-pink-200 shadow-lg">
                                <div id="map" style="height: 400px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Columna 2: Temario, Imágenes y Boletos --}}
                <div class="space-y-8">
                    {{-- Bloque: Temario del Evento --}}
                    <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Temario del Evento
                        </h2>

                        <div>
                            <textarea id="agenda" name="agenda"
                                      class="w-full rounded-xl border-2 border-pink-200 shadow-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                      rows="12"
                                      placeholder="Escribe el temario aquí (usa Markdown)...">{{ old('agenda', $event->agenda) }}</textarea>
                        </div>
                    </div>

                    {{-- Bloque: Imágenes del Evento --}}
                    <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Imágenes del Evento
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="banner" class="block text-sm font-medium text-gray-700 mb-3">Banner del Evento (debe de ser de 1024 * 768)</label>
                                <div class="relative">
                                    <input type="file" name="banner" id="banner" accept="image/*"
                                           class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('banner') border-red-500 @enderror">
                                    {{-- ... icon ... --}}
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
                                    {{-- VISTA PREVIA DEL BANNER ACTUAL --}}
                                    <img id="preview-banner"
                                        src="{{ $event->banner ? Storage::url($event->banner) : '' }}"
                                        class="w-full h-32 object-cover rounded-xl border-2 border-gray-200 shadow-lg {{ $event->banner ? '' : 'hidden' }}"
                                        alt="Vista previa del banner">
                                </div>
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-3">Imagen Principal (debe de ser de 736 * 308 )</label>
                                <div class="relative">
                                    <input type="file" name="image" id="image" accept="image/*"
                                           class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('image') border-red-500 @enderror">
                                    {{-- ... icon ... --}}
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
                                    {{-- VISTA PREVIA DE LA IMAGEN PRINCIPAL ACTUAL --}}
                                    <img id="preview-image"
                                        src="{{ $event->image ? Storage::url($event->image) : '' }}"
                                        class="w-full h-32 object-cover rounded-xl border-2 border-gray-200 shadow-lg {{ $event->image ? '' : 'hidden' }}"
                                        alt="Vista previa de la imagen principal">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="icon" class="block text-sm font-medium text-gray-700 mb-3">Icono del Evento (debe de ser de 800 * 800)</label>
                            <div class="relative">
                                <input type="file" name="icon" id="icon" accept="image/*"
                                       class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 @error('icon') border-red-500 @enderror">
                                {{-- ... icon ... --}}
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
                                {{-- VISTA PREVIA DEL ICONO ACTUAL --}}
                                <img id="preview-icon"
                                    src="{{ $event->icon ? Storage::url($event->icon) : '' }}"
                                    class="w-20 h-20 rounded-xl border-2 border-gray-200 shadow-lg {{ $event->icon ? '' : 'hidden' }}"
                                    alt="Vista previa del icono">
                            </div>
                        </div>
                    </div>

                    {{-- Bloque: Tipos de Boletos --}}
                    <div class="bg-gradient-to-r from-pink-40 to-pink-50 rounded-xl p-6">
                        <h2 class="text-xl font-semibold text-[#e24972] mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            Tipos de Boletos
                        </h2>

                        <div id="ticket-types" class="space-y-4">
                            {{-- INICIO DEL BUCLE PARA PRECARGAR BOLETOS EXISTENTES O OLD INPUT --}}
                            @php $ticketIndex = 0; @endphp

                            {{-- Combinar old input (si hay errores) con datos de la BD --}}
                            @php
                                $currentTickets = old('ticket_types', $event->ticketTypes->map(function($ticket) {
                                    return [
                                        'name' => $ticket->id, // Usamos el ID como valor inicial para el select
                                        'price' => $ticket->pivot->price,
                                        'quantity' => $ticket->pivot->quantity,
                                        'is_db' => true, // Flag para saber si viene de la DB
                                        'name_other' => null, // No hay "other" por defecto de la DB
                                    ];
                                })->toArray());
                            @endphp

                            @foreach($currentTickets as $index => $ticket)
                                @php
                                    // Normalizar la data. Si viene de old, ya es un array. Si es de la DB, lo mapeamos arriba.
                                    $ticketData = (object) (is_array($ticket) ? $ticket : (isset($ticket['is_db']) ? $ticket : $ticket->pivot->toArray()));
                                    $ticketId = $ticketData->name ?? null; // El ID del TicketType
                                    $price = $ticketData->price ?? 0;
                                    $quantity = $ticketData->quantity ?? 0;
                                    $nameOther = $ticketData->name_other ?? null; // Si el usuario escribió un nombre

                                    // Determinar si el campo "other" debe estar visible
                                    $isOtherSelected = false;
                                    if($nameOther) {
                                        $isOtherSelected = true;
                                        $ticketId = 'other';
                                    } elseif (!is_numeric($ticketId) && $ticketId != null) {
                                        // Esto maneja el caso de old('ticket_types[0][name]') == 'other'
                                        $isOtherSelected = true;
                                        $ticketId = 'other';
                                    }
                                @endphp

                                <div class="ticket-type border-2 border-pink-200 rounded-xl p-6 shadow-sm">
                                    @if($ticketIndex > 0)
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="font-semibold text-gray-900 text-lg">Tipo de Boleto {{ $ticketIndex + 1 }}</h3>
                                        <button type="button" onclick="removeTicketType(this)"
                                                class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-all duration-200 absolute top-4 right-4">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Nombre del Boleto</label>
                                            <select name="ticket_types[{{ $ticketIndex }}][name]" data-index="{{ $ticketIndex }}"
                                                    class="ticket-name-select w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 {{ $isOtherSelected ? 'hidden' : '' }}" required>
                                                <option value="">Selecciona un tipo</option>
                                                @foreach($ticketTypes as $tt)
                                                    <option value="{{ $tt->id }}"
                                                        {{ (string)$ticketId === (string)$tt->id ? 'selected' : '' }}>
                                                        {{ $tt->name }}
                                                    </option>
                                                @endforeach
                                                <option value="other" {{ $isOtherSelected ? 'selected' : '' }}>Agregar otro tipo</option>
                                            </select>

                                            <input type="text" name="ticket_types[{{ $ticketIndex }}][name_other]"
                                                   value="{{ $nameOther }}"
                                                   class="ticket-name-input w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 {{ $isOtherSelected ? '' : 'hidden' }}"
                                                   placeholder="Escribe el nombre del boleto">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Precio ($)</label>
                                            <input type="number" name="ticket_types[{{ $ticketIndex }}][price]" step="0.01" min="0" required
                                                   value="{{ $price }}"
                                                   class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                                   placeholder="0.00">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Cantidad</label>
                                            <input type="number" name="ticket_types[{{ $ticketIndex }}][quantity]" min="1" required
                                                   value="{{ $quantity }}"
                                                   class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:focus:border-pink-500 transition-all duration-200"
                                                   placeholder="100">
                                        </div>
                                    </div>
                                </div>
                                @php $ticketIndex++; @endphp
                            @endforeach
                            {{-- FIN DEL BUCLE --}}
                        </div>

                        <button type="button" onclick="addTicketType()"
                                class="mt-6 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl flex items-center space-x-2">
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
                        class="px-8 bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 py-3 text-white rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl font-medium flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Actualizar Evento</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&loading=async&libraries=places&callback=initMap">
    </script>

    <script>
        // Inicializar el contador de boletos con la cantidad de boletos ya cargados
        let ticketTypeCount = {{ count($currentTickets) }};

        // -----------------------------------------------------------
        // Lógica para cambiar entre select/input (Nombre del Boleto)
        // ADAPTACIÓN: Diferenciar el campo enviado (ID vs Nombre nuevo)
        // -----------------------------------------------------------
        function handleTicketTypeChange(selectElement) {
            const ticketTypeContainer = selectElement.closest('.ticket-type');
            const index = selectElement.dataset.index;
            const inputElement = ticketTypeContainer.querySelector('.ticket-name-input');

            if (selectElement.value === 'other') {
                selectElement.classList.add('hidden');
                selectElement.removeAttribute('required');

                // Si es 'other', enviamos el texto escrito en el input como 'name'
                selectElement.name = `ticket_types[${index}][name_id]`; // ID de la DB (se ignorará si no es ID)
                inputElement.name = `ticket_types[${index}][name]`; // El nombre nuevo

                inputElement.classList.remove('hidden');
                inputElement.setAttribute('required', 'required');
                inputElement.focus();
            } else {
                selectElement.classList.remove('hidden');
                selectElement.setAttribute('required', 'required');

                // Si es un ID, enviamos el ID seleccionado en el select como 'name'
                selectElement.name = `ticket_types[${index}][name]`; // El ID del ticketType
                inputElement.name = `ticket_types[${index}][name_other]`; // Nombre del input (se ignora)

                inputElement.classList.add('hidden');
                inputElement.removeAttribute('required');
                inputElement.value = ''; // Limpiar el valor si volvemos al select
            }
        }

        // -----------------------------------------------------------
        // Lógica para agregar nuevos tipos de boletos
        // -----------------------------------------------------------
        function addTicketType() {
            const container = document.getElementById('ticket-types');
            const newIndex = ticketTypeCount;

            // El HTML debe reflejar la adaptación de los campos name para el nuevo índice
            const ticketHtml = `
                <div class="ticket-type border-2 border-gray-200 rounded-xl p-6 shadow-sm relative">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-semibold text-gray-900 text-lg">Tipo de Boleto ${newIndex + 1}</h3>
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
                            <select name="ticket_types[${newIndex}][name]" data-index="${newIndex}"
                                    class="ticket-name-select w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200" required>
                                <option value="">Selecciona un tipo</option>
                                @foreach($ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->id }}">{{ $ticketType->name }}</option>
                                @endforeach
                                <option value="other">Agregar otro tipo</option>
                            </select>
                            <input type="text" name="ticket_types[${newIndex}][name_other]"
                                class="ticket-name-input hidden w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" placeholder="Escribe el nombre del boleto">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Precio ($)</label>
                            <input type="number" name="ticket_types[${newIndex}][price]" step="0.01" min="0" required
                                class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Cantidad</label>
                            <input type="number" name="ticket_types[${newIndex}][quantity]" min="1" required
                                class="w-full border-2 border-pink-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-pink-500 focus:focus:border-pink-500 transition-all duration-200"
                                placeholder="100">
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', ticketHtml);

            // Vuelve a añadir event listeners al nuevo select
            const newSelect = container.querySelector(`.ticket-name-select[data-index="${newIndex}"]`);
            if (newSelect) {
                newSelect.addEventListener('change', function() {
                    handleTicketTypeChange(this);
                });
            }

            ticketTypeCount++;
        }

        // -----------------------------------------------------------
        // Lógica de eliminación y re-indexación (No requiere cambios)
        // -----------------------------------------------------------
        function removeTicketType(button) {
            const ticketDiv = button.closest('.ticket-type');
            if (ticketDiv) {
                ticketDiv.remove();
                reindexTicketTypes();
            }
        }

        function reindexTicketTypes() {
            const ticketContainers = document.querySelectorAll('#ticket-types .ticket-type');
            ticketTypeCount = 0;
            ticketContainers.forEach((container, index) => {
                const title = container.querySelector('h3');
                if(title) {
                    title.textContent = `Tipo de Boleto ${index + 1}`;
                }

                container.querySelectorAll('[name^="ticket_types"]').forEach(input => {
                    const oldName = input.name;
                    const newName = oldName.replace(/ticket_types\[\d+\]/, `ticket_types[${index}]`);
                    input.name = newName;
                    if (input.classList.contains('ticket-name-select')) {
                        input.setAttribute('data-index', index);
                    }
                });
                ticketTypeCount++;
            });
        }


        // -----------------------------------------------------------
        // Lógica principal al cargar el documento (Mapa, EasyMDE, Vistas Previas)
        // -----------------------------------------------------------
        document.addEventListener("DOMContentLoaded", function() {

            // --- 1. Inicializar EasyMDE ---
            new EasyMDE({
                element: document.getElementById("agenda"),
                spellChecker: false,
                placeholder: "Escribe el temario aquí (usa Markdown)...",
                minHeight: "250px",
            });

            const easyMDE_description = new EasyMDE({
                element: document.getElementById("description"),
                spellChecker: false,
                placeholder: "Escribe la descripción aquí...",
                minHeight: "150px",
            });

            // Asegurar que el textarea oculto de descripción se actualice
            easyMDE_description.codemirror.on('change', () => {
                document.getElementById('description').value = easyMDE_description.value();
            });


            // --- 2. Lógica de Tipos de Boletos (Inicialización) ---
            // Inicializar event listeners y el estado 'other' para los boletos existentes
            document.querySelectorAll('.ticket-name-select').forEach((select, index) => {
                // Asegurar que todos tengan data-index (necesario para handleTicketTypeChange)
                select.dataset.index = index;
                select.addEventListener('change', function() {
                    handleTicketTypeChange(this);
                });
                // Ejecutar para configurar el nombre del campo y ocultar/mostrar el input si es 'other' (caso old() o DB)
                handleTicketTypeChange(select);
            });


            // --- 3. Script de Previsualización de Imágenes (No requiere cambios) ---
            const previewImage = (inputId, previewId) => {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                if (!input || !preview) return;

                input.addEventListener("change", (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            preview.src = e.target.result;
                            preview.classList.remove("hidden");
                        };
                        reader.readAsDataURL(file);
                    } else if (!preview.src || preview.src.includes('placeholder')) {
                        // Si el usuario cancela la selección, se queda la imagen de la DB
                        // Si no había imagen de DB, se oculta.
                        if (preview.src === "") {
                            preview.classList.add("hidden");
                        }
                    }
                });
            };
            previewImage("icon", "preview-icon");
            previewImage("banner", "preview-banner");
            previewImage("image", "preview-image");


            // --- 4. Validación de Fecha y Hora (No permite editar a fecha pasada) ---
            const dateInput = document.getElementById('date');
            const form = dateInput.closest('form');

            // Establecer el valor mínimo al momento actual
            function setMinDate() {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                let isoNow = now.toISOString().slice(0, 16);
                dateInput.min = isoNow;
            }

            setMinDate();

            form.addEventListener('submit', function(event) {
                const selectedDate = new Date(dateInput.value);
                const currentDate = new Date();

                const existingError = dateInput.parentNode.querySelector('.date-validation-error');
                if (existingError) {
                    existingError.remove();
                }

                // Si la fecha seleccionada es anterior a la fecha/hora actual, detener el envío.
                if (selectedDate <= currentDate) {
                    event.preventDefault();

                    const errorMessage = document.createElement('p');
                    errorMessage.className = 'mt-2 text-sm text-red-600 flex items-center date-validation-error';
                    errorMessage.innerHTML = `
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        La fecha y hora del evento no pueden ser anteriores o iguales al momento actual.
                    `;

                    dateInput.parentNode.appendChild(errorMessage);
                    dateInput.focus();
                }
            });

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

            // Precargar tags existentes del evento
            @if($event->tags && $event->tags->count() > 0)
                selectedTags = @json($event->tags->pluck('name')->toArray());
            @endif

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
                    tagElement.className = 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-300 shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105';
                    tagElement.innerHTML = `
                        <svg class="w-3 h-3 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        ${tag.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, "&#39;")}
                        <button type="button" onclick="removeTagFromEvent('${tag.replace(/'/g, "\\'").replace(/"/g, '&quot;')}')" 
                                class="ml-2 text-green-700 hover:text-red-600 hover:bg-red-50 rounded-full p-0.5 transition-all duration-200">
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
            window.addTagFromInput = function() {
                const tagValue = tagsInput.value.trim();
                if (tagValue) {
                    addTag(tagValue);
                }
            };

            window.removeTagFromEvent = function(tagName) {
                removeTag(tagName);
            };

            // Agregar tag con Enter
            tagsInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addTagFromInput();
                }
            });

            // Agregar tag desde el select
            tagsSelect.addEventListener('change', function() {
                if (this.value) {
                    addTag(this.value);
                    this.value = '';
                }
            });

            // Renderizar tags iniciales
            renderTags();
            updateHiddenInputs();
        });

        // --- 5. MAPA INTERACTIVO (GOOGLE MAPS) ---
        // Esta función será llamada cuando Google Maps API esté cargado
        window.initMap = function() {
            // Asegurarse de que el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initializeGoogleMap();
                });
            } else {
                initializeGoogleMap();
            }
        };

        function initializeGoogleMap() {
            const coordInput = document.getElementById('coordinates');
            const addressInput = document.getElementById('address');

                // Coordenadas por defecto (Toluca, México)
                const defaultLat = 19.2826;
                const defaultLng = -99.6556;

            let initialLat = defaultLat;
            let initialLng = defaultLng;

                // Revisar si hay coordenadas existentes del evento
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
                            } else {
                                // Si no encuentra la dirección, limpia el input de coordenadas para evitar errores
                                coordInput.value = '';
                            }
                        });
                }
                    }, 500);
            });
        }
    </script>
@endpush
