@extends('layouts.app')

@section('title', 'Eventos Disponibles')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Eventos Disponibles</h1>
        <p class="text-xl text-gray-600">Descubre los mejores eventos y compra tus boletos</p>
    </div>

    <!-- Events Grid -->
    @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($events as $event)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Event Image Placeholder -->
                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                             alt="{{ $event->name }}"
                             class="w-full h-48 object-cover">

                    <!-- Event Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $event->name }}</h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-sm">{{ $event->location }}</span>
                            </div>
                        </div>

                        <!-- Ticket Types -->
                        @if($event->ticketTypes->count() > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Tipos de Boletos:</h4>
                                <div class="space-y-1">
                                    @foreach($event->ticketTypes->take(3) as $ticketType)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">{{ $ticketType->name }}</span>
                                            <span class="font-medium text-green-600">${{ number_format($ticketType->price, 2) }}</span>
                                        </div>
                                    @endforeach
                                    @if($event->ticketTypes->count() > 3)
                                        <p class="text-xs text-gray-500">+{{ $event->ticketTypes->count() - 3 }} más</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <a href="{{ route('events.show', $event) }}" 
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors duration-200 text-center block">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay eventos disponibles</h3>
            <p class="text-gray-500">Pronto tendremos nuevos eventos para ti.</p>
        </div>
    @endif
</div>
@endsection
