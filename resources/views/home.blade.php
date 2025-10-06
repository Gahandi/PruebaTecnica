@extends('layouts.app')

@section('title', 'Boletos - Encuentra los Mejores Eventos')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-purple-900 via-blue-900 to-indigo-900 overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-50"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Encuentra los Mejores
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                    Eventos
                </span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 mb-8 max-w-3xl mx-auto">
                Descubre conciertos, deportes, teatro, comedia y más. Compra boletos de forma segura y fácil.
            </p>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto mb-12">
                <div class="relative">
                    <input type="text"
                           placeholder="¿Qué evento buscas?"
                           class="w-full px-6 py-4 text-lg rounded-full border-0 shadow-lg focus:ring-4 focus:ring-yellow-400 focus:outline-none">
                    <button class="absolute right-2 top-2 bg-yellow-400 hover:bg-yellow-500 text-black px-8 py-2 rounded-full font-semibold transition-colors">
                        Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Events Carousel -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Eventos Destacados</h2>
            <p class="text-lg text-gray-600">Los eventos más populares del momento</p>
        </div>

        <!-- Carousel -->
        <div class="relative">
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" id="carousel">
                    @foreach($featuredEvents as $event)
                    <div class="w-full md:w-1/2 lg:w-1/3 flex-shrink-0 px-4">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="relative">
                                @if($event->banner && $event->banner !== 'test.jpg')
                                    <img src="{{ asset('storage/' . $event->banner) }}"
                                         alt="{{ $event->name }}"
                                         class="w-full h-64 object-cover">
                                @else
                                    <div class="w-full h-64 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="text-lg font-semibold">{{ $event->name }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute top-4 right-4">
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                        Destacado
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $event->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</p>
                                <p class="text-gray-500 mb-4">{{ $event->address }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $event->space->name }}
                                        </span>
                                    </div>
                                    <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($event->space->subdomain) }}/{{ $event->slug }}"
                                       class="bg-yellow-400 hover:bg-yellow-500 text-black px-6 py-2 rounded-full font-semibold transition-colors">
                                        Ver Evento
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Carousel Controls -->
            <button onclick="previousSlide()"
                    class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <button onclick="nextSlide()"
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:shadow-xl transition-shadow">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Categories Section -->
<div class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Explora por Categoría</h2>
            <p class="text-lg text-gray-600">Encuentra eventos que te interesen</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($categories as $category)
            <div class="group cursor-pointer">
                <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl p-8 text-center hover:from-purple-600 hover:to-pink-600 transition-all duration-300 transform hover:scale-105">
                    <div class="text-white">
                        <div class="w-16 h-16 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold">{{ $category['name'] }}</h3>
                        <p class="text-sm opacity-90">{{ $category['count'] }} eventos</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- All Events Section -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Todos los Eventos</h2>
            <p class="text-lg text-gray-600">Descubre todos los eventos disponibles</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($allEvents as $event)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="relative">
                    @if($event->banner && $event->banner !== 'test.jpg')
                        <img src="{{ asset('storage/' . $event->banner) }}"
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
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $event->name }}</h3>
                    <p class="text-gray-600 mb-2">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</p>
                    <p class="text-gray-500 mb-4">{{ $event->address }}</p>

                    <!-- Ticket Availability -->
                    @if($event->ticketTypes->count() > 0)
                        @php
                            $totalTickets = $event->ticketTypes->sum('pivot.quantity');
                            $availableTickets = $event->ticketTypes->sum('pivot.quantity');
                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                <span>Boletos disponibles</span>
                                <span>{{ $availableTickets }} disponibles</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $availableTickets > 0 ? 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $event->space->name }}
                            </span>
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

        @if($allEvents->count() > 6)
        <div class="text-center mt-12">
            <a href="{{ route('events.public') }}"
               class="bg-gray-900 hover:bg-gray-800 text-white px-8 py-3 rounded-full font-semibold transition-colors">
                Ver Todos los Eventos
            </a>
        </div>
        @endif
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-to-r from-yellow-400 to-orange-500 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            ¿Tienes un evento que promocionar?
        </h2>
        <p class="text-xl text-white mb-8">
            Crea tu propio espacio de eventos y vende boletos de forma fácil y segura
        </p>
        <a href="{{ route('user.spaces.create') }}"
           class="bg-white text-gray-900 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition-colors">
            Crear Mi Espacio
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentSlide = 0;
const totalSlides = {{ $featuredEvents->count() }};
const slidesToShow = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;

function nextSlide() {
    currentSlide = (currentSlide + 1) % Math.max(1, totalSlides - slidesToShow + 1);
    updateCarousel();
}

function previousSlide() {
    currentSlide = currentSlide === 0 ? Math.max(0, totalSlides - slidesToShow) : currentSlide - 1;
    updateCarousel();
}

function updateCarousel() {
    const carousel = document.getElementById('carousel');
    const slideWidth = 100 / slidesToShow;
    carousel.style.transform = `translateX(-${currentSlide * slideWidth}%)`;
}

// Auto-advance carousel
setInterval(nextSlide, 5000);

// Update slides on resize
window.addEventListener('resize', () => {
    const newSlidesToShow = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
    if (newSlidesToShow !== slidesToShow) {
        currentSlide = 0;
        updateCarousel();
    }
});
</script>
@endpush
