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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($upcomingEvents as $event)
                <div
                    class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-xl transition-all duration-300 hover:border-pink-300">
                    <div class="relative overflow-hidden">
                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" alt="{{ $event->name }}"
                            class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 right-3">
                            <div class="flex items-center justify-between">
                                <span
                                    class="bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($event->date)->format('d M') }}
                                </span>
                                @if($event->ticketTypes->count() > 0)
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                        ${{ number_format($event->ticketTypes->min('pivot.price'), 0) }}+
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2 line-clamp-1 group-hover:text-pink-600 transition-colors">
                            {{ $event->name }}</h4>
                        <div class="flex items-center text-gray-500 text-sm mb-3">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                            </svg>
                            <span class="truncate">{{ $event->address }}</span>
                        </div>

                        @if($event->ticketTypes->count() > 0)
                            @php
                                $totalTickets = $event->ticketTypes->sum('pivot.quantity');
                                $soldTickets = \App\Models\Ticket::where('event_id', $event->id)->count();
                                $availableTickets = $totalTickets - $soldTickets;
                                $percentage = $totalTickets > 0 ? ($availableTickets / $totalTickets) * 100 : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>{{ $availableTickets }} disponibles</span>
                                    <span>{{ round(100 - $percentage) }}% vendido</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-pink-500 h-1.5 rounded-full transition-all"
                                        style="width: {{ 100 - $percentage }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-between">
                            @auth
                                @if($isAdmin)
                                    <a href="{{ route('spaces.events.edit', ['subdomain' => $space->subdomain, 'event' => $event->slug]) }}"
                                        class="text-gray-500 hover:text-gray-700 text-sm">
                                        Editar
                                    </a>
                                @else
                                    <span></span>
                                @endif
                            @else
                                <span></span>
                            @endauth
                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}"
                                class="inline-flex items-center px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Ver Evento
                            </a>
                        </div>
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
                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}" alt="{{ $event->name }}"
                        class="w-24 h-24 object-cover flex-shrink-0 grayscale opacity-75">
                    <div class="p-3 flex-1 min-w-0">
                        <h4 class="font-medium text-gray-700 text-sm truncate">{{ $event->name }}</h4>
                        <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</p>
                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}"
                            class="text-xs text-pink-600 hover:text-pink-700 mt-2 inline-block">
                            Ver detalles →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif