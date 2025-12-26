@extends('layouts.app')

@section('title', 'Categorías de Eventos')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">
        <div class="max-w-7xl mx-auto py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl sm:text-4xl font-bold text-[#e24972] mb-4">Categorías de Eventos</h1>
                <p class="text-lg sm:text-xl text-gray-600">Explora eventos por categoría y encuentra lo que te apasiona</p>
            </div>

            <!-- Categories Grid -->
            <div id="categories-container" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                @foreach($categories as $category)
                    @php
                        $catImageUrl = $category->image ? \App\Helpers\ImageHelper::getImageUrl($category->image) : asset('images/categories/Poster7.jpeg');
                    @endphp
                    <a href="{{ route('categories.show', $category->id) }}" class="group category-card">
                        <div
                            class="relative rounded-xl overflow-hidden aspect-square transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                            <!-- Imagen -->
                            <img src="{{ $catImageUrl }}" alt="{{ $category->name }}"
                                class="w-full h-full object-cover transition-all duration-300">

                            <!-- Overlay con nombre de categoría -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent
                                                        flex items-end justify-center p-4">
                                <div class="text-center">
                                    <h3 class="text-white font-bold text-base sm:text-lg lg:text-xl drop-shadow-lg">
                                        {{ $category->name }}
                                    </h3>
                                    <span class="text-white/80 text-sm">
                                        {{ $category->events_count }} {{ $category->events_count == 1 ? 'evento' : 'eventos' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Degradado hover -->
                            <div class="absolute inset-0 opacity-0
                                                        bg-gradient-to-r from-[rgba(255,105,180,0.40)]
                                                        to-[rgba(252,159,205,0.7)]
                                                        group-hover:opacity-100 transition-all duration-300">
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Loading Indicator -->
            <div id="loading-indicator" class="hidden text-center py-8">
                <div class="inline-flex items-center space-x-3">
                    <svg class="animate-spin h-8 w-8 text-[#e24972]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-gray-600 font-medium">Cargando más categorías...</span>
                </div>
            </div>

            <!-- End Message -->
            <div id="end-message" class="hidden text-center py-8">
                <p class="text-gray-500 font-medium">¡Has visto todas las categorías!</p>
            </div>

            <!-- Empty State (solo si no hay categorías iniciales) -->
            @if($categories->count() == 0)
                <div id="empty-state" class="text-center py-16 bg-white rounded-2xl shadow-lg">
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No hay categorías disponibles</h3>
                    <p class="text-lg text-gray-600 mb-6">Pronto tendremos categorías de eventos para ti.</p>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center bg-gradient-to-r from-pink-500 to-pink-400 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 hover:from-pink-600 hover:to-pink-500">
                        Volver al Inicio
                    </a>
                </div>
            @endif

            <!-- Back to Home -->
            <div class="text-center mt-12">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center text-gray-600 hover:text-[#e24972] font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>

    <!-- Infinite Scroll Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentPage = {{ $categories->currentPage() }};
            let lastPage = {{ $categories->lastPage() }};
            let isLoading = false;

            const container = document.getElementById('categories-container');
            const loadingIndicator = document.getElementById('loading-indicator');
            const endMessage = document.getElementById('end-message');

            // Función para crear una tarjeta de categoría
            function createCategoryCard(category) {
                const imageUrl = category.image
                    ? `{{ config('app.s3_base_url') }}/${category.image}`
                    : '{{ asset('images/categories/Poster7.jpeg') }}';

                const eventsText = category.events_count === 1 ? 'evento' : 'eventos';

                return `
                        <a href="/categories/${category.id}" class="group category-card">
                            <div class="relative rounded-xl overflow-hidden aspect-square transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                                <img src="${imageUrl}" alt="${category.name}" 
                                    class="w-full h-full object-cover transition-all duration-300"
                                    onerror="this.src='{{ asset('images/categories/Poster7.jpeg') }}'">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end justify-center p-4">
                                    <div class="text-center">
                                        <h3 class="text-white font-bold text-base sm:text-lg lg:text-xl drop-shadow-lg">
                                            ${category.name}
                                        </h3>
                                        <span class="text-white/80 text-sm">
                                            ${category.events_count} ${eventsText}
                                        </span>
                                    </div>
                                </div>
                                <div class="absolute inset-0 opacity-0 bg-gradient-to-r from-[rgba(255,105,180,0.40)] to-[rgba(252,159,205,0.7)] group-hover:opacity-100 transition-all duration-300"></div>
                            </div>
                        </a>
                    `;
            }

            // Función para cargar más categorías
            async function loadMoreCategories() {
                if (isLoading || currentPage >= lastPage) return;

                isLoading = true;
                loadingIndicator.classList.remove('hidden');

                try {
                    const response = await fetch(`/api/v1/categories?page=${currentPage + 1}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Error al cargar categorías');

                    const data = await response.json();

                    // Agregar nuevas categorías al contenedor
                    data.data.forEach(category => {
                        container.insertAdjacentHTML('beforeend', createCategoryCard(category));
                    });

                    currentPage = data.current_page;
                    lastPage = data.last_page;

                    // Mostrar mensaje de fin si no hay más páginas
                    if (!data.has_more) {
                        endMessage.classList.remove('hidden');
                    }

                } catch (error) {
                    console.error('Error cargando categorías:', error);
                } finally {
                    isLoading = false;
                    loadingIndicator.classList.add('hidden');
                }
            }

            // Detectar scroll al final de la página
            function handleScroll() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const scrollHeight = document.documentElement.scrollHeight;
                const clientHeight = document.documentElement.clientHeight;

                // Cargar más cuando esté a 200px del final
                if (scrollTop + clientHeight >= scrollHeight - 200) {
                    loadMoreCategories();
                }
            }

            // Agregar listener de scroll
            window.addEventListener('scroll', handleScroll);

            // Mostrar mensaje de fin si ya no hay más páginas desde el inicio
            if (currentPage >= lastPage && {{ $categories->count() }} > 0) {
                endMessage.classList.remove('hidden');
            }
        });
    </script>
@endsection