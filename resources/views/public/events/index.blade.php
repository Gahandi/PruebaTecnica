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
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($events as $event)
                <a href="{{ route('events.show', $event) }}" 
                   class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group block">
                    <!-- Event Image -->
                    <div class="relative aspect-square overflow-hidden">
                        @if($event->icon && $event->icon !== 'test.jpg')
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}"
                                alt="{{ $event->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @elseif($event->banner && $event->banner !== 'test.jpg')
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                                alt="{{ $event->name }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-pink-500 to-purple-500 flex items-center justify-center">
                                <svg class="w-12 h-12 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                        <!-- Price Badge -->
                        @if($event->ticketTypes->count() > 0)
                            <div class="absolute bottom-2 left-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-lg">
                                    ${{ number_format($event->ticketTypes->min('pivot.price'), 0) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Event Content -->
                    <div class="p-3">
                        <h3 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-[#e24972] transition-colors">{{ $event->name }}</h3>
                        <p class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1 text-[#e24972]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                        </p>
                    </div>
                </a>
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
