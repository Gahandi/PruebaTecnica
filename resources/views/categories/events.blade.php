@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="text-center mb-10">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
            Eventos de "{{ $category->name }}"
        </h2>
        <p class="text-gray-600 text-lg">
            Explora los eventos disponibles en esta categoría
        </p>
    </div>

    @if($events->count() > 0)

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($events as $event)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="relative">
                    @if($event->banner && $event->banner !== 'test.jpg')
                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                            alt="{{ $event->name }}"
                            class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center">
                            <div class="text-center text-white">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="font-semibold">{{ $event->name }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $event->name }}</h3>

                    @if($event->ticketTypes->count() > 0)
                        <div class="flex items-baseline mb-3 text-sm">
                            <span class="text-gray-600 font-medium mr-2">Entradas Desde:</span>
                            <span class="text-lg font-bold text-green-600">
                                ${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}
                            </span>
                        </div>
                    @endif

                    <p class="text-gray-600 mb-2">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</p>
                    <p class="text-gray-500 mb-4">{{ $event->address }}</p>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}"
                            target="_blank"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                {{ $event->space->name }}
                        </a>
                        </div>

                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                        class="bg-yellow-400 hover:bg-yellow-500 text-black px-4 py-2 rounded-full font-semibold transition-colors text-sm">
                            Ver Evento
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
        </div>

    @else
        <div class="text-center py-20">
            <h3 class="text-xl font-semibold text-gray-700">No hay eventos disponibles en esta categoría</h3>
        </div>
    @endif

</div>

@endsection
