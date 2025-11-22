@extends('layouts.space')

@section('title', $space->name)

@php
    use App\Models\RoleSpacePermission;
    $canSeeScanner = RoleSpacePermission::hasPermission($space->id, 'create checkins');
@endphp

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">


     <!-- Contenido Principal -->
     <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
         <!-- Foto de la Organización - Estilo Facebook -->
         <div class="mb-8">
             <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                 
                 <div class="relative facebook-style-photo" >
                     <div class="absolute top-8 right-8 z-30">
                @auth
                @if(auth()->user()->isAdminOfSpace($space->id))
                    <!-- Botón de Edición -->
                        <button 
                            type="button"
                            onclick="toggleEditMode()"
                            class="bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-6 py-3 shadow-xl flex items-center space-x-3 hover:bg-opacity-100 transition-all duration-300 hover:scale-105 edit-organization-button">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8 0a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-800">Editar</span>
                            <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                        </button>
                    @endif
            @else
                <!-- Badge normal para usuarios no autenticados -->
                <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-4 py-2 shadow-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8 0a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-800">Espacio</span>
                    </div>
                </div>
                @endauth
            </div>
                     @if($space->banner)
                     <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->banner) }}" alt="{{ $space->name }}" class="w-full h-96 object-cover">
                     @else
                         <div class="w-full h-96 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                             <div class="text-center text-gray-500">
                                 <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                 </svg>
                                 <p class="text-xl font-medium">Sin foto</p>
                                 <p class="text-sm">Agrega una foto para tu cajón</p>
                             </div>
                         </div>
                     @endif

                     <!-- Overlay con información -->
                     <div class="photo-overlay">
                         <div class="text-white">
                             <h4 class="text-2xl font-bold mb-2">{{ $space->name }}</h4>
                             <p class="text-gray-200">{{ $space->description }}</p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Columna Izquierda - Información de la Organización -->
            <div class="lg:col-span-1 space-y-6 sticky top-6">
                <!-- Información de la Organización -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="flex item-center justify-center pt-10">
                        <div class="z-20 w-25 h-25 md:w-42 md:h-42 rounded-full border-4 border-white shadow-2xl overflow-hidden bg-white">
                            @if($space->logo)
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" alt="{{ $space->name }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-3xl font-bold text-white" style="background: linear-gradient(135deg, {{ $space->color_primary }}, {{ $space->color_secondary ?? $space->color_primary }});">
                                    {{ substr($space->name, 0, 2) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-6 mt-3">
                        <h2 class="text-2xl text-center w-full font-bold text-gray-900"><span id="space-name-display">{{ $space->name }}</span></h2>
                    </div>


                </div>

                <!-- Estadísticas de la Organización -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Estadísticas</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center p-4 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100">
                            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $space->events->count() }}</div>
                            <div class="text-sm font-medium text-blue-800">Eventos</div>
                        </div>
                        <div class="text-center p-4 sm:px-0 rounded-xl bg-gradient-to-br from-green-50 to-green-100">
                            <div class="text-3xl font-bold text-green-600 mb-2">{{ $space->users->count() }}</div>
                            <div class="text-sm font-medium text-green-800">Miembros</div>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociales -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Redes Sociales</h3>

                    <!-- Facebook -->
                    <div class="flex items-center text-gray-600 group mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span id="facebook-display" class="font-medium text-blue-600">{{ $space->social_facebook ?: 'No especificado' }}</span>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="flex items-center text-gray-600 group mb-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-100 to-pink-100 flex items-center justify-center mr-4 group-hover:from-purple-200 group-hover:to-pink-200 transition-colors">
                            <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.004 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244c-.875.807-2.026 1.297-3.323 1.297zm7.718-1.297c-.875.807-2.026 1.297-3.323 1.297s-2.448-.49-3.323-1.297c-.928-.875-1.418-2.026-1.418-3.323s.49-2.448 1.418-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span id="instagram-display" class="font-medium text-pink-600">{{ $space->social_instagram ?: 'No especificado' }}</span>
                        </div>
                    </div>

                    <!-- Twitter -->
                    <div class="flex items-center text-gray-600 group">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <span id="twitter-display" class="font-medium text-blue-400">{{ $space->social_twitter ?: 'No especificado' }}</span>
                        </div>
                    </div>
                </div>
                 
                @if($canSeeScanner)
                    <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                        <a href="{{ route('scanner.index', ['subdomain' => $space->subdomain]) }}"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-xl transition duration-200"
                        >
                            Ir al Scanner
                        </a>
                    </div>
                @endif

            </div>

            <!-- Columna Derecha - Eventos -->
            <div class="lg:col-span-3 ">
                <div class="bg-white  rounded-2xl shadow-xl p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Eventos de {{ $space->name }}</h2>
                            <p class="text-gray-600 mt-2">Descubre los próximos eventos de este espacio</p>
                        </div>
                        <div class="flex space-x-3">
                            @auth
                                @if(auth()->user()->isAdminOfSpace($space->id))
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

                    @if($space->events->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($space->events as $event)
                                <div class="bg-white border-2 border-gray-100 rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:border-gray-200 transform hover:-translate-y-1">
                                    <div class="relative">

                                           
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($event->banner) }}"
                                            alt="{{ $event->name }}"
                                            class="w-full h-48 object-cover">


                                        <!-- Badge de Fecha -->
                                        <div class="absolute top-4 right-4">
                                            <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-3 py-1 shadow-lg">
                                                <span class="text-sm font-semibold text-gray-800">
                                                    {{ \Carbon\Carbon::parse($event->date)->format('d M') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $event->name }}</h3>
                                        <div class="flex items-center text-gray-600 mb-3">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="font-medium">{{ \Carbon\Carbon::parse($event->date)->format('d M Y, H:i') }}</span>
                                        </div>

                                        <div class="flex items-center text-gray-600 mb-4">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="text-sm">{{ $event->address }}</span>
                                                </div>

                                        @if($event->description)
                                            <p class="text-gray-700 text-sm mb-6 line-clamp-2 leading-relaxed">{{ $event->description }}</p>
                                        @endif
                    <!-- Ticket Availability -->
                    @if($event->ticketTypes->count() > 0)
                        @php
                            $totalTickets = $event->ticketTypes->sum('pivot.quantity');
                            $availableTickets = $event->ticketTypes->sum('pivot.quantity');

                            $ticketCount = \App\Models\Ticket::where('event_id', $event->id)->get();
                            $vendidos = 0;
                            \Log::info('=== SIMPLE TEST ===');
                            \Log::info('Datos de prueba: '.json_encode($ticketCount));
                            if ($ticketCount->count() > 0) {
                                foreach ($ticketCount as $item => $value) {
                                    $vendidos++;
                                    \Log::info('Datos de prueba contador: '.$vendidos);
                                }
                            }
                            $disponibles = $availableTickets - $vendidos;
                            $porcentaje = ($disponibles * 100) / $availableTickets;

                        @endphp
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                <span>Boletos disponibles</span>
                                <span>{{ $disponibles }} disponibles de {{ $availableTickets }} </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $availableTickets > 0 ? $porcentaje : 0 }}%"></div>
                            </div>
                        </div>
                    @endif
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $space->name }}
                                                </span>
                                            </div>
                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}/{{ $event->slug }}"
                                               class="px-6 py-3 rounded-xl text-white font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105"
                                               style="background: linear-gradient(135deg, {{ $space->color_primary }}, {{ $space->color_secondary ?? $space->color_primary }});">
                                                Ver Evento
                                            </a>
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
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">No hay eventos aún</h3>
                            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">Este espacio aún no tiene eventos programados. ¡Mantente atento a las próximas novedades!</p>
                            @auth
                                @if(auth()->user()->isAdminOfSpace($space->id))
                                    <div class="mt-8">
                                        <a href="{{ route('spaces.events.create', $space->subdomain) }}"
                                           class="inline-flex items-center px-8 py-4 rounded-xl text-white font-semibold transition-all duration-200 hover:shadow-lg transform hover:scale-105"
                                           style="background: linear-gradient(135deg, {{ $space->color_primary }}, {{ $space->color_secondary ?? $space->color_primary }});">
                                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Crear Primer Evento
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let isEditMode = false;
let originalValues = {};

// Funciones para edición rápida de colores
function updateQuickColors() {
    const primaryColor = document.getElementById('quick-primary-color').value;
    const secondaryColor = document.getElementById('quick-secondary-color').value;

    // Actualizar el gradiente del hero en tiempo real
    const heroSection = document.querySelector('.w-full.h-80.md\\:h-96.bg-gradient-to-r');
    if (heroSection) {
        heroSection.style.transition = 'background 0.3s ease';
        heroSection.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }

    // Actualizar logo background
    const logoDiv = document.querySelector('.w-28.h-28.md\\:w-36.md\\:h-36.rounded-full.border-4.border-white.shadow-2xl.overflow-hidden.bg-white div');
    if (logoDiv) {
        logoDiv.style.transition = 'background 0.3s ease';
        logoDiv.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }
}

function showColorControls() {
    // Crear controles de color dinámicamente
    const colorControls = document.createElement('div');
    colorControls.id = 'color-controls';
    colorControls.className = 'fixed top-20 right-8 bg-white bg-opacity-95 backdrop-blur-sm rounded-2xl px-6 py-4 shadow-xl z-50';
    colorControls.innerHTML = `
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <input type="color"
                       id="edit-primary-color"
                       value="{{ $space->color_primary ?? '#3b82f6' }}"
                       class="w-10 h-10 rounded-lg border-2 border-gray-300 shadow-sm cursor-pointer transition-all duration-300 hover:scale-110"
                       onchange="updateEditColors()">
                <span class="text-sm font-medium text-gray-600">Primario</span>
            </div>

            <div class="flex items-center space-x-2">
                <input type="color"
                       id="edit-secondary-color"
                       value="{{ $space->color_secondary ?? '#8b5cf6' }}"
                       class="w-10 h-10 rounded-lg border-2 border-gray-300 shadow-sm cursor-pointer transition-all duration-300 hover:scale-110"
                       onchange="updateEditColors()">
                <span class="text-sm font-medium text-gray-600">Secundario</span>
            </div>

            <div class="text-xs text-blue-500 font-medium">
                👁️ Vista previa en tiempo real
            </div>
        </div>
    `;

    document.body.appendChild(colorControls);
}

function updateEditColors() {
    const primaryColor = document.getElementById('edit-primary-color').value;
    const secondaryColor = document.getElementById('edit-secondary-color').value;

    // Actualizar el gradiente del hero en tiempo real
    const heroSection = document.querySelector('.w-full.h-80.md\\:h-96.bg-gradient-to-r');
    if (heroSection) {
        heroSection.style.transition = 'background 0.3s ease';
        heroSection.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }

    // Actualizar logo background
    const logoDiv = document.querySelector('.w-28.h-28.md\\:w-36.md\\:h-36.rounded-full.border-4.border-white.shadow-2xl.overflow-hidden.bg-white div');
    if (logoDiv) {
        logoDiv.style.transition = 'background 0.3s ease';
        logoDiv.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }
}

function editPhoto() {
    // Función placeholder para editar foto
    showSuccessMessage('Función de editar foto próximamente');
}

function toggleEditMode() {
    if (isEditMode) {
        exitEditMode();
    } else {
        window.location.href = "{{ route('spaces.edit', $space->subdomain) }}";
    }
}

function enterEditMode() {
    isEditMode = true;

    // Guardar valores originales
    originalValues = {
        name: document.getElementById('space-name-display').textContent,
        about: document.getElementById('about-display').textContent,
        location: document.getElementById('location-display').textContent,
        website: document.getElementById('website-display').textContent,
        email: document.getElementById('email-display').textContent,
        phone: document.getElementById('phone-display').textContent,
        facebook: document.getElementById('facebook-display')?.textContent || '',
        instagram: document.getElementById('instagram-display')?.textContent || '',
        twitter: document.getElementById('twitter-display')?.textContent || '',
        primaryColor: '{{ $space->color_primary ?? "#3b82f6" }}',
        secondaryColor: '{{ $space->color_secondary ?? "#8b5cf6" }}'
    };

    // Mostrar controles de color
    showColorControls();

    // Convertir a inputs con animación
    animateToInputs();

    // Cambiar el botón a modo guardar/cancelar
    const editButton = document.querySelector('button[onclick="toggleEditMode()"]');
    editButton.innerHTML = `
        <div class="flex items-center space-x-3">
            <button onclick="saveChanges()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Guardar</span>
            </button>
            <button onclick="cancelEdit()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span>Cancelar</span>
            </button>
        </div>
    `;
}

function animateToInputs() {
    // Animar nombre
    const nameElement = document.getElementById('space-name-display');
    const nameInput = document.createElement('input');
    nameInput.type = 'text';
    nameInput.value = originalValues.name;
    nameInput.className = 'bg-transparent border-b-2 border-blue-500 outline-none font-bold text-2xl text-gray-900 transition-all duration-300';
    nameInput.id = 'name-input';

    nameElement.style.opacity = '0';
    nameElement.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        nameElement.parentNode.replaceChild(nameInput, nameElement);
        nameInput.style.opacity = '1';
        nameInput.style.transform = 'translateY(0)';
        nameInput.focus();
    }, 150);

    // Animar descripción
    const aboutElement = document.getElementById('about-display');
    const aboutTextarea = document.createElement('textarea');
    aboutTextarea.value = originalValues.about === 'No hay descripción disponible' ? '' : originalValues.about;
    aboutTextarea.className = 'w-full bg-transparent border-b-2 border-blue-500 outline-none text-gray-700 leading-relaxed resize-none transition-all duration-300';
    aboutTextarea.rows = 3;
    aboutTextarea.id = 'about-input';
    aboutTextarea.placeholder = 'Describe tu organización...';

    aboutElement.style.opacity = '0';
    aboutElement.style.transform = 'translateY(-10px)';

    setTimeout(() => {
        aboutElement.parentNode.replaceChild(aboutTextarea, aboutElement);
        aboutTextarea.style.opacity = '1';
        aboutTextarea.style.transform = 'translateY(0)';
    }, 200);

    // Animar campos de contacto
    animateContactFields();

    // Animar redes sociales
    animateSocialFields();

    // Animar colores
    animateColorFields();
}

function animateContactFields() {
    const fields = [
        { id: 'location-display', inputId: 'location-input', placeholder: 'Ubicación', valueKey: 'location' },
        { id: 'website-display', inputId: 'website-input', placeholder: 'Sitio web', valueKey: 'website' },
        { id: 'email-display', inputId: 'email-input', placeholder: 'Email de contacto', valueKey: 'email' },
        { id: 'phone-display', inputId: 'phone-input', placeholder: 'Teléfono', valueKey: 'phone' }
    ];

    fields.forEach((field, index) => {
        setTimeout(() => {
            const element = document.getElementById(field.id);
            if (element) {
                const input = document.createElement('input');
                input.type = 'text';
                input.value = originalValues[field.valueKey] || '';
                input.className = 'w-full bg-transparent border-b-2 border-blue-500 outline-none font-medium transition-all duration-300';
                input.id = field.inputId;
                input.placeholder = field.placeholder;

                element.style.opacity = '0';
                element.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    element.parentNode.replaceChild(input, element);
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, 50);
            }
        }, 300 + (index * 100));
    });
}

function animateSocialFields() {
    const socialFields = [
        { id: 'facebook-display', inputId: 'facebook-input', placeholder: 'Facebook URL', valueKey: 'facebook' },
        { id: 'instagram-display', inputId: 'instagram-input', placeholder: 'Instagram URL', valueKey: 'instagram' },
        { id: 'twitter-display', inputId: 'twitter-input', placeholder: 'Twitter URL', valueKey: 'twitter' }
    ];

    socialFields.forEach((field, index) => {
        setTimeout(() => {
            const element = document.getElementById(field.id);
            if (element) {
                const input = document.createElement('input');
                input.type = 'url';
                input.value = originalValues[field.valueKey] || '';
                input.className = 'w-full bg-transparent border-b-2 border-blue-500 outline-none font-medium transition-all duration-300';
                input.id = field.inputId;
                input.placeholder = field.placeholder;

                element.style.opacity = '0';
                element.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    element.parentNode.replaceChild(input, element);
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, 50);
            }
        }, 700 + (index * 100));
    });
}

function animateColorFields() {
    const colorFields = [
        { id: 'primary-color-display', inputId: 'primary-color-input', valueKey: 'primaryColor' },
        { id: 'secondary-color-display', inputId: 'secondary-color-input', valueKey: 'secondaryColor' }
    ];

    colorFields.forEach((field, index) => {
        setTimeout(() => {
            const element = document.getElementById(field.id);
            if (element) {
                const container = element.parentNode;
                const input = document.createElement('input');
                input.type = 'color';
                input.value = originalValues[field.valueKey] || '#3b82f6';
                input.className = 'w-12 h-12 rounded-xl border-2 border-gray-300 shadow-sm transition-all duration-300';
                input.id = field.inputId;

                // Crear un contenedor para el input
                const inputContainer = document.createElement('div');
                inputContainer.className = 'flex items-center space-x-3';
                inputContainer.appendChild(input);

                const span = document.createElement('span');
                span.className = 'text-gray-600 font-mono text-sm';
                span.textContent = input.value;
                inputContainer.appendChild(span);

                // Agregar indicador de preview en tiempo real
                const previewIndicator = document.createElement('div');
                previewIndicator.className = 'text-xs text-blue-500 font-medium';
                previewIndicator.textContent = '👁️ Vista previa en tiempo real';
                inputContainer.appendChild(previewIndicator);

                // Actualizar el span cuando cambie el color
                input.addEventListener('input', function() {
                    span.textContent = this.value;
                    // Actualizar el background del hero en tiempo real
                    updateHeroBackgroundRealtime();

                    // Agregar indicador visual de que se está actualizando
                    const heroSection = document.querySelector('.relative.bg-gradient-to-r');
                    if (heroSection) {
                        heroSection.style.boxShadow = '0 0 20px rgba(59, 130, 246, 0.3)';
                        setTimeout(() => {
                            heroSection.style.boxShadow = '';
                        }, 300);
                    }
                });

                element.style.opacity = '0';
                element.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    element.parentNode.replaceChild(inputContainer, element);
                    inputContainer.style.opacity = '1';
                    inputContainer.style.transform = 'translateY(0)';
                }, 50);
            }
        }, 1000 + (index * 100));
    });
}

function updateHeroBackground() {
    const primaryColor = document.getElementById('primary-color-input')?.value || '#3b82f6';
    const secondaryColor = document.getElementById('secondary-color-input')?.value || '#8b5cf6';

    // Actualizar hero section background
    const heroSection = document.querySelector('.relative.bg-gradient-to-r');
    if (heroSection) {
        heroSection.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }

    // Actualizar logo background
    const logoDiv = document.querySelector('.w-28.h-28.md\\:w-36.md\\:h-36.rounded-full.border-4.border-white.shadow-2xl.overflow-hidden.bg-white div');
    if (logoDiv) {
        logoDiv.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }
}

function updateHeroBackgroundRealtime() {
    const primaryColor = document.getElementById('primary-color-input')?.value || originalValues.primaryColor || '#3b82f6';
    const secondaryColor = document.getElementById('secondary-color-input')?.value || originalValues.secondaryColor || '#8b5cf6';

    // Actualizar hero section background con transición suave
    const heroSection = document.querySelector('.relative.bg-gradient-to-r');
    if (heroSection) {
        heroSection.style.transition = 'background 0.3s ease';
        heroSection.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }

    // Actualizar logo background con transición suave
    const logoDiv = document.querySelector('.w-28.h-28.md\\:w-36.md\\:h-36.rounded-full.border-4.border-white.shadow-2xl.overflow-hidden.bg-white div');
    if (logoDiv) {
        logoDiv.style.transition = 'background 0.3s ease';
        logoDiv.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }

    // Actualizar preview de gradiente en la sección de colores
    const gradientPreview = document.querySelector('.h-16.rounded-lg.shadow-sm[style*="linear-gradient"]');
    if (gradientPreview) {
        gradientPreview.style.transition = 'background 0.3s ease';
        gradientPreview.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }

    // Actualizar previews individuales de colores
    const primaryPreview = document.querySelector('.h-16.rounded-lg.shadow-sm[style*="background-color"]');
    if (primaryPreview) {
        primaryPreview.style.transition = 'background-color 0.3s ease';
        primaryPreview.style.backgroundColor = primaryColor;
    }

    // Buscar y actualizar el preview del color secundario
    const colorPreviews = document.querySelectorAll('.h-16.rounded-lg.shadow-sm');
    colorPreviews.forEach(preview => {
        if (preview.style.backgroundColor && preview.style.backgroundColor !== primaryColor) {
            preview.style.transition = 'background-color 0.3s ease';
            preview.style.backgroundColor = secondaryColor;
        }
    });
}

function saveChanges() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('name', document.getElementById('name-input').value);
    formData.append('about', document.getElementById('about-input').value);
    formData.append('location', document.getElementById('location-input').value);
    formData.append('website', document.getElementById('website-input').value);
    formData.append('contact_email', document.getElementById('email-input').value);
    formData.append('contact_phone', document.getElementById('phone-input').value);
    formData.append('social_facebook', document.getElementById('facebook-input')?.value || '');
    formData.append('social_instagram', document.getElementById('instagram-input')?.value || '');
    formData.append('social_twitter', document.getElementById('twitter-input')?.value || '');
    formData.append('color_primary', document.getElementById('primary-color-input')?.value || '#3b82f6');
    formData.append('color_secondary', document.getElementById('secondary-color-input')?.value || '#8b5cf6');

    // Mostrar loading
    const editButton = document.querySelector('button[onclick="toggleEditMode()"]');
    editButton.innerHTML = `
        <div class="flex items-center space-x-2">
            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <span>Guardando...</span>
        </div>
    `;

    fetch('{{ route("spaces.update-profile", $space->subdomain) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar valores mostrados
            updateDisplayValues();
            exitEditMode();
            showSuccessMessage('Información actualizada correctamente');
            // Recargar la página para aplicar los nuevos colores
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showErrorMessage('Error al actualizar: ' + (data.message || 'Error desconocido'));
            exitEditMode();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Error de conexión');
        exitEditMode();
    });
}

function updateDisplayValues() {
    // Actualizar nombre
    const nameInput = document.getElementById('name-input');
    if (nameInput) {
        const nameDisplay = document.createElement('span');
        nameDisplay.id = 'space-name-display';
        nameDisplay.textContent = nameInput.value;
        nameDisplay.className = 'text-2xl font-bold text-gray-900';
        nameInput.parentNode.replaceChild(nameDisplay, nameInput);
    }

    // Actualizar descripción
    const aboutInput = document.getElementById('about-input');
    if (aboutInput) {
        const aboutDisplay = document.createElement('p');
        aboutDisplay.id = 'about-display';
        const aboutValue = aboutInput.value;
        aboutDisplay.textContent = aboutValue || 'No hay descripción disponible';
        aboutDisplay.className = aboutValue ? 'text-gray-700 mb-6 leading-relaxed' : 'text-gray-700 mb-6 leading-relaxed italic text-gray-500';
        aboutInput.parentNode.replaceChild(aboutDisplay, aboutInput);
    }

    // Actualizar campos de contacto
    updateContactFieldsDisplay();

    // Actualizar redes sociales
    updateSocialMediaFieldsDisplay();

    // Actualizar colores
    updateColorDisplays();
}

function updateContactFieldsDisplay() {
    const fields = [
        { inputId: 'location-input', displayId: 'location-display', defaultText: 'No especificada' },
        { inputId: 'website-input', displayId: 'website-display', defaultText: 'No especificado' },
        { inputId: 'email-input', displayId: 'email-display', defaultText: 'No especificado' },
        { inputId: 'phone-input', displayId: 'phone-display', defaultText: 'No especificado' }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.inputId);
        if (input) {
            const display = document.createElement('span');
            display.id = field.displayId;
            const value = input.value;
            display.textContent = value || field.defaultText;
            display.className = 'font-medium' + (field.displayId.includes('website') || field.displayId.includes('email') || field.displayId.includes('phone') ? ' text-blue-600' : '');
            input.parentNode.replaceChild(display, input);
        }
    });
}

function updateSocialMediaFieldsDisplay() {
    const socialFields = [
        { inputId: 'facebook-input', displayId: 'facebook-display', defaultText: 'No especificado', classColor: 'text-blue-600' },
        { inputId: 'instagram-input', displayId: 'instagram-display', defaultText: 'No especificado', classColor: 'text-pink-600' },
        { inputId: 'twitter-input', displayId: 'twitter-display', defaultText: 'No especificado', classColor: 'text-blue-400' }
    ];

    socialFields.forEach(field => {
        const input = document.getElementById(field.inputId);
        if (input) {
            const display = document.createElement('span');
            display.id = field.displayId;
            const value = input.value;
            display.textContent = value || field.defaultText;
            display.className = `font-medium ${field.classColor}`;
            input.parentNode.replaceChild(display, input);
        }
    });
}

function updateColorDisplays() {
    const primaryColorInput = document.getElementById('primary-color-input');
    const secondaryColorInput = document.getElementById('secondary-color-input');

    if (primaryColorInput) {
        const primaryColorDisplay = document.createElement('div');
        primaryColorDisplay.id = 'primary-color-display';
        primaryColorDisplay.className = 'flex items-center space-x-3';
        primaryColorDisplay.innerHTML = `
            <div class="w-12 h-12 rounded-xl border-2 border-gray-300 shadow-sm"
                 style="background-color: ${primaryColorInput.value}"></div>
            <span class="text-gray-600 font-mono text-sm">${primaryColorInput.value}</span>
        `;
        primaryColorInput.parentNode.parentNode.replaceChild(primaryColorDisplay, primaryColorInput.parentNode);
    }

    if (secondaryColorInput) {
        const secondaryColorDisplay = document.createElement('div');
        secondaryColorDisplay.id = 'secondary-color-display';
        secondaryColorDisplay.className = 'flex items-center space-x-3';
        secondaryColorDisplay.innerHTML = `
            <div class="w-12 h-12 rounded-xl border-2 border-gray-300 shadow-sm"
                 style="background-color: ${secondaryColorInput.value}"></div>
            <span class="text-gray-600 font-mono text-sm">${secondaryColorInput.value}</span>
        `;
        secondaryColorInput.parentNode.parentNode.replaceChild(secondaryColorDisplay, secondaryColorInput.parentNode);
    }
}

function cancelEdit() {
    exitEditMode();
}

function exitEditMode() {
    isEditMode = false;

    // Ocultar controles de color
    const colorControls = document.getElementById('color-controls');
    if (colorControls) {
        colorControls.remove();
    }

    // Restaurar botón original
    const editButtonContainer = document.querySelector('.edit-organization-button');
    editButtonContainer.innerHTML = `
        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm2 6a2 2 0 114 0 2 2 0 01-4 0zm8 0a2 2 0 114 0 2 2 0 01-4 0z" clip-rule="evenodd"></path>
            </svg>
        <span class="text-sm font-semibold text-gray-800">Editar</span>
        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
    `;
    editButtonContainer.setAttribute('onclick', 'toggleEditMode()');

    // Restaurar elementos originales
    restoreOriginalElements();
}

function restoreOriginalElements() {
    // Restaurar nombre
    const nameInput = document.getElementById('name-input');
    if (nameInput) {
        const nameDisplay = document.createElement('span');
        nameDisplay.id = 'space-name-display';
        nameDisplay.textContent = originalValues.name;
        nameDisplay.className = 'text-2xl font-bold text-gray-900';
        nameInput.parentNode.replaceChild(nameDisplay, nameInput);
    }

    // Restaurar descripción
    const aboutInput = document.getElementById('about-input');
    if (aboutInput) {
        const aboutDisplay = document.createElement('p');
        aboutDisplay.id = 'about-display';
        aboutDisplay.textContent = originalValues.about;
        aboutDisplay.className = originalValues.about === 'No hay descripción disponible' ? 'text-gray-700 mb-6 leading-relaxed italic text-gray-500' : 'text-gray-700 mb-6 leading-relaxed';
        aboutInput.parentNode.replaceChild(aboutDisplay, aboutInput);
    }

    // Restaurar campos de contacto
    restoreContactFields();

    // Restaurar redes sociales
    restoreSocialMediaFields();

    // Restaurar colores
    restoreColorFields();
}

function restoreContactFields() {
    const fields = [
        { inputId: 'location-input', displayId: 'location-display', value: originalValues.location },
        { inputId: 'website-input', displayId: 'website-display', value: originalValues.website },
        { inputId: 'email-input', displayId: 'email-display', value: originalValues.email },
        { inputId: 'phone-input', displayId: 'phone-display', value: originalValues.phone }
    ];

    fields.forEach(field => {
        const input = document.getElementById(field.inputId);
        if (input) {
            const display = document.createElement('span');
            display.id = field.displayId;
            display.textContent = field.value;
            display.className = 'font-medium' + (field.displayId.includes('website') || field.displayId.includes('email') || field.displayId.includes('phone') ? ' text-blue-600' : '');
            input.parentNode.replaceChild(display, input);
        }
    });
}

function restoreSocialMediaFields() {
    const socialFields = [
        { inputId: 'facebook-input', displayId: 'facebook-display', value: originalValues.facebook, defaultText: 'No especificado', classColor: 'text-blue-600' },
        { inputId: 'instagram-input', displayId: 'instagram-display', value: originalValues.instagram, defaultText: 'No especificado', classColor: 'text-pink-600' },
        { inputId: 'twitter-input', displayId: 'twitter-display', value: originalValues.twitter, defaultText: 'No especificado', classColor: 'text-blue-400' }
    ];

    socialFields.forEach(field => {
        const input = document.getElementById(field.inputId);
        if (input) {
            const display = document.createElement('span');
            display.id = field.displayId;
            display.textContent = field.value || field.defaultText;
            display.className = `font-medium ${field.classColor}`;
            input.parentNode.replaceChild(display, input);
        }
    });
}

function restoreColorFields() {
    const colorFields = [
        { inputId: 'primary-color-input', displayId: 'primary-color-display', value: originalValues.primaryColor, defaultText: '#3b82f6' },
        { inputId: 'secondary-color-input', displayId: 'secondary-color-display', value: originalValues.secondaryColor, defaultText: '#8b5cf6' }
    ];

    colorFields.forEach(field => {
        const inputContainer = document.getElementById(field.inputId)?.parentNode;
        if (inputContainer) {
            const display = document.createElement('div');
            display.id = field.displayId;
            display.className = 'flex items-center space-x-3';
            display.innerHTML = `
                <div class="w-12 h-12 rounded-xl border-2 border-gray-300 shadow-sm"
                     style="background-color: ${field.value || field.defaultText}"></div>
                <span class="text-gray-600 font-mono text-sm">${field.value || field.defaultText}</span>
            `;
            inputContainer.parentNode.replaceChild(display, inputContainer);
        }
    });
}

function showSuccessMessage(message) {
    // Crear notificación de éxito
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function showErrorMessage(message) {
    // Crear notificación de error
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            ${message}
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animaciones personalizadas */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

/* Mejoras para el logo */
.logo-container {
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 50;
}

.logo-container:hover {
    transform: translateY(-5px);
}

/* Asegurar que el logo esté por encima de todo */
.logo-container .logo-main {
    position: relative;
    z-index: 10;
    background: white;
    border: 4px solid white;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.logo-container::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6, #06b6d4, #10b981);
    border-radius: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.logo-container:hover::before {
    opacity: 0.3;
}

/* Botón de edición de organización */
.edit-organization-button {
    background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(255,255,255,0.95));
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.edit-organization-button:hover {
    background: linear-gradient(135deg, rgba(255,255,255,1), rgba(255,255,255,1));
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px) scale(1.02);
    border-color: rgba(59, 130, 246, 0.3);
}

.edit-organization-button:active {
    transform: translateY(0) scale(0.98);
}

/* Indicador de edición */
.edit-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.1);
    }
}

/* Mejoras para el gradiente del logo */
.logo-gradient {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    position: relative;
    overflow: hidden;
}

.logo-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shine 3s infinite;
}

@keyframes shine {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

/* Estilos para el banner con blur */
.hero-banner {
    position: relative;
    overflow: hidden;
}

.hero-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: inherit;
    filter: blur(2px);
    transform: scale(1.1);
    z-index: -1;
}

.hero-overlay {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.8), rgba(139, 92, 246, 0.8));
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}

/* Estilos para la foto estilo Facebook */
.facebook-style-photo {
    position: relative;
    overflow: hidden;
    border-radius: 0.5rem;
}

.facebook-style-photo img {
    transition: transform 0.3s ease;
}

.photo-overlay {
    background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.5rem;
}
</style>
@endsection
