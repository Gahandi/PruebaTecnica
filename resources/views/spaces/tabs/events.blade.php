<!-- Tab: Eventos -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
    <div>
        <h2 class="text-3xl font-bold text-[#e24972]">Eventos de {{ $space->name }}</h2>
        <p class="text-gray-600 mt-2">Descubre los próximos eventos de este espacio</p>
    </div>
    <div class="flex space-x-3">
        @auth
            @if($isAdmin)
                <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                   class="px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105"
                   style="background: linear-gradient(135deg, {{ $space->color_primary }}, {{ $space->color_secondary ?? $space->color_primary }});">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Evento
                </a>
            @endif
        @endauth
    </div>
</div>

<!-- Filtros Simples -->
@if(isset($allTags) && $allTags->count() > 0 || isset($allCategories) && $allCategories->count() > 0)
<div class="mb-8 bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 border border-gray-200">
    <form method="GET" action="{{ route('spaces.profile', $space->subdomain) }}" id="spaceFilterForm" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Filtro por Tags -->
            @if(isset($allTags) && $allTags->count() > 0)
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Filtrar por Etiquetas
                </label>
                <select name="tag" onchange="document.getElementById('spaceFilterForm').submit()" 
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-medium bg-white transition-all">
                    <option value="">Todas las etiquetas</option>
                    @foreach($allTags as $tag)
                        <option value="{{ $tag->id }}" {{ (isset($tagFilter) && $tagFilter == $tag->id) ? 'selected' : '' }}>
                            {{ $tag->name }} ({{ $tag->events_count }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Filtro por Categorías -->
            @if(isset($allCategories) && $allCategories->count() > 0)
            <div class="flex-1">
                <label class="block text-sm font-semibold text-pink-600 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Filtrar por Categoría
                </label>
                <select name="category" onchange="document.getElementById('spaceFilterForm').submit()" 
                        class="w-full px-4 py-2.5 border-2 border-pink-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-sm font-medium bg-white transition-all">
                    <option value="">Todas las categorías</option>
                    @foreach($allCategories as $category)
                        <option value="{{ $category->id }}" {{ (isset($categoryFilter) && $categoryFilter == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->events_count }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Botón Limpiar -->
            @if((isset($tagFilter) && $tagFilter) || (isset($categoryFilter) && $categoryFilter))
            <div class="flex items-end">
                <a href="{{ route('spaces.profile', $space->subdomain) }}" 
                   class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold text-sm transition-all duration-200 flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Limpiar
                </a>
            </div>
            @endif
        </div>
    </form>
</div>
@endif

@php
    $eventsToShow = isset($filteredEvents) && $filteredEvents->count() > 0 ? $filteredEvents : (isset($space->events) ? $space->events->where('active', true)->where('date', '>=', now()) : collect());
@endphp

@if($eventsToShow->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($eventsToShow as $event)
            <div class="bg-white border-2 border-gray-100 rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:border-pink-200 transform hover:-translate-y-1">
                <div class="relative">
                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                        alt="{{ $event->name }}"
                        class="w-full h-48 object-cover">
                    <div class="absolute top-4 right-4">
                        <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-3 py-1 shadow-lg">
                            <span class="text-sm font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($event->date)->format('d M') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3 text-[#e24972]">{{ $event->name }}</h3>
                    @if($event->ticketTypes->count() > 0)
                        <div class="flex items-baseline mb-3 text-sm">
                            <span class="text-gray-600 font-medium mr-2">Entradas Desde:</span>
                            <span class="text-lg font-bold text-green-600">
                                ${{ number_format($event->ticketTypes->min('pivot.price'), 2) }}
                            </span>
                        </div>
                    @endif
                    <div class="flex items-center text-gray-600 mb-3">
                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex items-center text-gray-600 mb-4">
                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm">{{ $event->address }}</span>
                    </div>
                    @if($event->description)
                        <p class="text-gray-700 text-sm mb-6 line-clamp-2 leading-relaxed">{{ $event->description }}</p>
                    @endif

                    <!-- Tags del Evento -->
                    @if($event->tags && $event->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($event->tags as $tag)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if($event->ticketTypes->count() > 0)
                        @php
                            $totalTickets = $event->ticketTypes->sum('pivot.quantity');
                            $soldTickets = \App\Models\Ticket::where('event_id', $event->id)->count();
                            $availableTickets = $totalTickets - $soldTickets;
                            $percentage = $totalTickets > 0 ? ($availableTickets / $totalTickets) * 100 : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                <span>Boletos disponibles</span>
                                <span>{{ $availableTickets }} disponibles de {{ $totalTickets }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800 hover:bg-pink-200">
                            {{ $space->name }}
                        </span>

                        <div class="flex space-x-3">
                            {{-- INICIO: Botón "Editar Evento" (Añadido) --}}
                            @auth
                                @if($isAdmin)
                                    <a href="{{ route('spaces.events.edit', ['subdomain' => $space->subdomain, 'event' => $event->slug]) }}"
                                       class="px-4 py-3 rounded-xl text-gray-700 font-semibold border border-gray-300 hover:bg-gray-100 transition-all duration-200 hover:shadow-md transform hover:scale-105 flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-9l7 7m-7-9v7h7"></path>
                                        </svg>
                                        Editar
                                    </a>
                                @endif
                            @endauth
                            {{-- FIN: Botón "Editar Evento" (Añadido) --}}

                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}"
                               class="px-6 py-3 rounded-xl bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-600 hover:to-pink-500 text-white font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105">
                                Ver Evento
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-4">
            @if((isset($tagFilter) && $tagFilter) || (isset($categoryFilter) && $categoryFilter))
                No se encontraron eventos con estos filtros
            @else
                No hay eventos aún
            @endif
        </h3>
        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
            @if((isset($tagFilter) && $tagFilter) || (isset($categoryFilter) && $categoryFilter))
                Intenta cambiar los filtros o 
                <a href="{{ route('spaces.profile', $space->subdomain) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">ver todos los eventos</a>
            @else
                Este espacio aún no tiene eventos programados.
            @endif
        </p>
        @auth
            @if($isAdmin && !((isset($tagFilter) && $tagFilter) || (isset($categoryFilter) && $categoryFilter)))
                <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                   class="inline-flex items-center px-8 py-4 rounded-xl text-white font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105"
                   style="background: linear-gradient(135deg, {{ $space->color_primary }}, {{ $space->color_secondary ?? $space->color_primary }});">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Evento
                </a>
            @endif
        @endauth
    </div>
@endif
