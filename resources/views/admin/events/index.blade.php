@extends('layouts.admin')
@section('title', 'Eventos')

@section('admin-content')
    <div class="container mx-auto px-6 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Lista de Eventos
            </h1>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                Evento
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                Espacio
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                Estadísticas
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Fecha del evento
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Creado
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- Evento --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($event->image)
                                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                                                     alt="{{ $event->name }}"
                                                     class="w-12 h-12 rounded-lg object-cover mr-4 shadow-sm">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center mr-4 shadow-sm">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">
                                                    {{ $event->name }}
                                                </div>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    @if($event->active)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <span class="w-1.5 h-1.5 mr-1 rounded-full bg-green-400"></span>
                                                            Activo
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <span class="w-1.5 h-1.5 mr-1 rounded-full bg-red-400"></span>
                                                            Inactivo
                                                        </span>
                                                    @endif
                                                    @if($event->type_event)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            {{ $event->type_event->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Espacio con más detalles --}}
                                    <td class="px-6 py-4">
                                        @if($event->space)
                                            <div class="flex items-center">
                                                @if($event->space->logo)
                                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->space->logo) }}" 
                                                         alt="{{ $event->space->name }}"
                                                         class="w-10 h-10 rounded-full object-cover mr-3 shadow-sm border border-gray-200">
                                                @else
                                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center mr-3 shadow-sm">
                                                        <span class="text-white font-bold text-sm">{{ substr($event->space->name, 0, 1) }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        {{ $event->space->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 space-y-0.5">
                                                        @if($event->space->subdomain)
                                                            <div class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                                                </svg>
                                                                <a href="http://{{ $event->space->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                                                                   target="_blank" 
                                                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                                                    {{ $event->space->subdomain }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if($event->space->location)
                                                            <div class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                                <span class="truncate max-w-[150px]" title="{{ $event->space->location }}">
                                                                    {{ $event->space->location }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                        @if($event->space->contact_email)
                                                            <div class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <a href="mailto:{{ $event->space->contact_email }}" 
                                                                   class="text-blue-600 hover:text-blue-800 hover:underline truncate max-w-[150px]"
                                                                   title="{{ $event->space->contact_email }}">
                                                                    {{ $event->space->contact_email }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Sin espacio asignado</span>
                                        @endif
                                    </td>

                                    {{-- Estadísticas --}}
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                                </svg>
                                                <span class="text-gray-600">{{ $event->tickets_events_count ?? 0 }} tipos de tickets</span>
                                            </div>
                                            <div class="flex items-center text-sm">
                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                                </svg>
                                                <span class="text-gray-600">{{ $event->orders_count ?? 0 }} órdenes</span>
                                            </div>
                                            @if($event->state)
                                                <div class="flex items-center text-sm">
                                                    <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span class="text-gray-600">{{ $event->state->name }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Fecha evento --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $event->date->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $event->date->format('H:i') }} hrs
                                        </div>
                                        @php
                                            $now = now();
                                            $eventDate = $event->date;
                                        @endphp
                                        @if($eventDate->isPast())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-1">
                                                Finalizado
                                            </span>
                                        @elseif($eventDate->isToday())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                <span class="w-1.5 h-1.5 mr-1 rounded-full bg-yellow-400 animate-pulse"></span>
                                                Hoy
                                            </span>
                                        @elseif($eventDate->diffInDays($now) <= 7)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                                En {{ $eventDate->diffForHumans() }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Creado --}}
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            {{ $event->created_at->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $event->created_at->format('H:i') }}
                                        </div>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end space-x-2">
                                            {{-- Ver evento público --}}
                                            <a href="{{ route('events.show', $event) }}" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                                               title="Ver evento público">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Ver
                                            </a>

                                            {{-- Editar evento (ir al espacio) --}}
                                            @if($event->space && $event->space->subdomain && $event->slug)
                                                <a href="http://{{ $event->space->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}/eventos/{{ $event->slug }}/editar" 
                                                   target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition-colors"
                                                   title="Editar evento">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Editar
                                                </a>
                                            @endif

                                            {{-- Gestionar espacio --}}
                                            @if($event->space && $event->space->subdomain)
                                                <a href="http://{{ $event->space->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors"
                                                   title="Gestionar espacio">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    Gestionar
                                                </a>
                                            @endif

                                            {{-- Eliminar evento --}}
                                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                                                onsubmit="return confirmDelete('{{ $event->name }}')" class="inline-block">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors" 
                                                        title="Eliminar evento">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2 0V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-lg font-medium text-gray-500">No hay eventos registrados</p>
                                            <p class="text-sm text-gray-400 mt-1">Los eventos creados en los espacios aparecerán aquí</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t bg-gray-50">
                    {{ $events->links() }}
                </div>
            </div>
        </div>

        <script>
            function confirmDelete(name) {
                return confirm(
                    `⚠️ ¿Estás seguro de eliminar el evento "${name}"?\n\nEsta acción eliminará el evento del sistema.`
                );
            }
        </script>
@endsection