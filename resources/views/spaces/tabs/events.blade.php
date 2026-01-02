<!-- Tab: Eventos - Improved Design -->
@php
    $now = \Carbon\Carbon::now();
    $upcomingEvents = isset($filteredEvents) && $filteredEvents->count() > 0
        ? $filteredEvents->filter(fn($e) => \Carbon\Carbon::parse($e->date) >= $now)
        : (isset($space->events) ? $space->events->where('active', true)->filter(fn($e) => \Carbon\Carbon::parse($e->date) >= $now) : collect());

    $pastEvents = isset($space->events)
        ? $space->events->where('active', true)->filter(fn($e) => \Carbon\Carbon::parse($e->date) < $now)->sortByDesc('date')->take(6)
        : collect();
@endphp

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Eventos</h2>
        <p class="text-gray-500 mt-1">{{ $upcomingEvents->count() }} próximos · {{ $pastEvents->count() }} anteriores
        </p>
    </div>
    @auth
        @if($isAdmin)
            <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                class="inline-flex items-center px-5 py-2.5 rounded-xl text-white font-semibold transition-all duration-200 hover:shadow-lg bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Crear Evento
            </a>
        @endif
    @endauth
</div>

<!-- Filters -->
@if((isset($allTags) && $allTags->count() > 0) || (isset($allCategories) && $allCategories->count() > 0))
    <div class="mb-8 bg-gray-50 rounded-xl p-4 border border-gray-200">
        <form method="GET" action="{{ route('spaces.profile', $space->subdomain) }}" id="spaceFilterForm"
            class="flex flex-wrap gap-3 items-center">
            @if(isset($allTags) && $allTags->count() > 0)
                <select name="tag" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Todas las etiquetas</option>
                    @foreach($allTags as $tag)
                        <option value="{{ $tag->id }}" {{ (isset($tagFilter) && $tagFilter == $tag->id) ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if(isset($allCategories) && $allCategories->count() > 0)
                <select name="category" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    <option value="">Todas las categorías</option>
                    @foreach($allCategories as $category)
                        <option value="{{ $category->id }}" {{ (isset($categoryFilter) && $categoryFilter == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if((isset($tagFilter) && $tagFilter) || (isset($categoryFilter) && $categoryFilter))
                <a href="{{ route('spaces.profile', $space->subdomain) }}"
                    class="px-4 py-2 text-gray-600 hover:text-gray-900 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Limpiar
                </a>
            @endif
        </form>
    </div>
@endif

<!-- Upcoming Events Section -->
@if($upcomingEvents->count() > 0)
    <div class="mb-12">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
            Próximos Eventos
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($upcomingEvents as $event)
                <div
                    class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:border-pink-300">
                    <div class="relative aspect-square overflow-hidden">
                        @if($event->icon && $event->icon !== 'test.jpg')
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}" alt="{{ $event->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @elseif($event->banner && $event->banner !== 'test.jpg')
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" alt="{{ $event->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-pink-500 to-purple-500 flex items-center justify-center">
                                <svg class="w-12 h-12 text-white/70" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-2 left-2 right-2">
                            <div class="flex items-center justify-between">
                                <span class="bg-white/90 backdrop-blur-sm px-2 py-0.5 rounded-full text-xs font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($event->date)->format('d M') }}
                                </span>
                                @if($event->ticketTypes->count() > 0)
                                    <span class="bg-green-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">
                                        ${{ number_format($event->ticketTypes->min('pivot.price'), 0) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-gray-900 text-sm mb-1 line-clamp-2 group-hover:text-pink-600 transition-colors">
                            {{ $event->name }}
                        </h4>

                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}"
                            class="block w-full text-center px-3 py-1.5 bg-pink-600 hover:bg-pink-700 text-white text-xs font-medium rounded-lg transition-colors mt-2">
                            Ver Evento
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@else
    <div class="text-center py-16 mb-12">
        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Sin eventos próximos</h3>
        <p class="text-gray-500 mb-6">Este espacio no tiene eventos programados por ahora</p>
        @auth
            @if($isAdmin)
                <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                    class="inline-flex items-center px-6 py-3 bg-pink-600 hover:bg-pink-700 text-white font-medium rounded-xl transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Evento
                </a>
            @endif
        @endauth
    </div>
@endif

<!-- Past Events Section -->
@if($pastEvents->count() > 0)
    <div class="border-t border-gray-200 pt-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Eventos Anteriores
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pastEvents as $event)
                <div
                    class="flex bg-gray-50 rounded-lg overflow-hidden border border-gray-200 hover:bg-gray-100 transition-colors">
                    <div class="w-24 h-24 flex-shrink-0 overflow-hidden">
                        @if($event->icon && $event->icon !== 'test.jpg')
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->icon) }}" alt="{{ $event->name }}"
                                class="w-full h-full object-cover grayscale opacity-75">
                        @elseif($event->banner && $event->banner !== 'test.jpg')
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" alt="{{ $event->name }}"
                                class="w-full h-full object-cover grayscale opacity-75">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 flex-1 min-w-0 flex flex-col justify-between">
                        <div>
                            <h4 class="font-medium text-gray-700 text-sm truncate" title="{{ $event->name }}">{{ $event->name }}
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</p>
                        </div>

                        <div class="flex items-center justify-between mt-2">
                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Ver
                            </a>

                            @auth
                                @if($isAdmin)
                                    <div class="flex items-center gap-2">
                                        {{-- Duplicar --}}
                                        <form action="{{ route('spaces.events.duplicate', [$space->subdomain, $event->slug]) }}"
                                            method="POST" onsubmit="return confirm('¿Duplicar evento?');">
                                            @csrf
                                            <button type="submit" class="text-purple-500 hover:text-purple-700 transition-colors"
                                                title="Duplicar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <form action="{{ route('spaces.events.destroy', [$space->subdomain, $event->slug]) }}"
                                            method="POST" onsubmit="return confirm('¿Eliminar evento anterior?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 transition-colors"
                                                title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif