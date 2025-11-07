@extends('layouts.app')

@section('title', 'Crear Evento - ' . $space->name)

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Evento</h1>
                <p class="text-gray-600 mt-1">En {{ $space->name }}</p>
            </div>
            <a href="{{ route('spaces.profile', $space->subdomain) }}" 
               class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                Volver al Cajón
            </a>
        </div>
        
        <form method="POST" action="{{ route('spaces.events.store', $space->subdomain) }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Información del Evento</h2>
                </div>
                
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Evento</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="description" id="description" rows="4" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora</label>
                    <input type="datetime-local" name="date" id="date" value="{{ old('date') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('date') border-red-500 @enderror">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="type_event_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Evento</label>
                    <select name="type_event_id" id="type_event_id" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('type_event_id') border-red-500 @enderror">
                        <option value="">Selecciona un tipo</option>
                        @foreach($typeEvents as $typeEvent)
                            <option value="{{ $typeEvent->id }}" {{ old('type_event_id') == $typeEvent->id ? 'selected' : '' }}>
                                {{ $typeEvent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_event_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="coordinates" class="block text-sm font-medium text-gray-700 mb-2">Coordenadas</label>
                    <input type="text" name="coordinates" id="coordinates" value="{{ old('coordinates') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('coordinates') border-red-500 @enderror"
                           placeholder="Ej: 19.4326, -99.1332">
                    <p class="mt-1 text-sm text-gray-500">Coordenadas GPS (latitud, longitud)</p>
                    @error('coordinates')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Imágenes -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Imágenes</h2>
                </div>
                
                <div>
                    <label for="banner" class="block text-sm font-medium text-gray-700 mb-2">Banner del Evento</label>
                    <input type="file" name="banner" id="banner" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('banner') border-red-500 @enderror">
                    @error('banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Imagen del Evento</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('image') border-red-500 @enderror">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Tipos de Boletos -->
                <div class="md:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Tipos de Boletos</h2>
                    <div id="ticket-types">
                        <div class="ticket-type border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Boleto</label>
                                    <input type="text" name="ticket_types[0][name]" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Ej: General">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio ($)</label>
                                    <input type="number" name="ticket_types[0][price]" step="0.01" min="0" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                                    <input type="number" name="ticket_types[0][quantity]" min="1" required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="100">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addTicketType()" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        + Agregar Tipo de Boleto
                    </button>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('spaces.profile', $space->subdomain) }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Crear Evento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let ticketTypeCount = 1;

function addTicketType() {
    const container = document.getElementById('ticket-types');
    const newTicketType = document.createElement('div');
    newTicketType.className = 'ticket-type border border-gray-200 rounded-lg p-4 mb-4';
    newTicketType.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-medium text-gray-900">Tipo de Boleto ${ticketTypeCount + 1}</h3>
            <button type="button" onclick="removeTicketType(this)" 
                    class="text-red-600 hover:text-red-800 text-sm">
                Eliminar
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Boleto</label>
                <input type="text" name="ticket_types[${ticketTypeCount}][name]" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Ej: VIP">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Precio ($)</label>
                <input type="number" name="ticket_types[${ticketTypeCount}][price]" step="0.01" min="0" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0.00">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                <input type="number" name="ticket_types[${ticketTypeCount}][quantity]" min="1" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="50">
            </div>
        </div>
    `;
    container.appendChild(newTicketType);
    ticketTypeCount++;
}

function removeTicketType(button) {
    button.closest('.ticket-type').remove();
}
</script>
@endsection
