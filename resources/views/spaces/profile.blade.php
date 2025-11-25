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
    <!-- Banner Header -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="relative facebook-style-photo">
                <div class="absolute top-8 right-8 z-30">
                    @auth
                        @if($isAdmin)
                            <button 
                                type="button"
                                onclick="window.location.href='{{ route('spaces.edit', $space->subdomain) }}'"
                                class="bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-6 py-3 shadow-xl flex items-center space-x-3 hover:bg-opacity-100 transition-all duration-300 hover:scale-105">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                                <span class="text-sm font-semibold text-gray-800">Editar</span>
                            </button>
                        @endif
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
                        </div>
                    </div>
                @endif
                <div class="photo-overlay">
                    <div class="text-white">
                        <h4 class="text-2xl font-bold mb-2">{{ $space->name }}</h4>
                        <p class="text-gray-200">{{ $space->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal con Tabs -->
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Sistema de Tabs -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" aria-label="Tabs">
                    <button onclick="switchTab('events')" id="tab-events" class="tab-button active px-6 py-4 text-sm font-medium text-center border-b-2 border-blue-500 text-blue-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Eventos</span>
                        </div>
                    </button>
                    @if($isAdmin)
                        <button onclick="switchTab('dashboard')" id="tab-dashboard" class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </div>
                        </button>
                        <button onclick="switchTab('users')" id="tab-users" class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span>Usuarios</span>
                            </div>
                        </button>
                        
                        <button onclick="switchTab('edit')" id="tab-edit" class="tab-button px-6 py-4 text-sm font-medium text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span>Editar</span>
                            </div>
                        </button>
                    @endif
                </nav>
            </div>

            <!-- Contenido de los Tabs -->
            <div class="p-8">
                <!-- Tab: Eventos -->
                <div id="content-events" class="tab-content">
                    @include('spaces.tabs.events', ['space' => $space, 'isAdmin' => $isAdmin])
                </div>

                <!-- Tab: Dashboard -->
                <div id="content-dashboard" class="tab-content hidden">
                    @include('spaces.tabs.dashboard', [
                        'space' => $space,
                        'totalEvents' => $totalEvents,
                        'totalMembers' => $totalMembers,
                        'totalTicketsAvailable' => $totalTicketsAvailable,
                        'totalTicketsSold' => $totalTicketsSold,
                        'totalRevenue' => $totalRevenue
                    ])
                </div>

                <!-- Tab: Usuarios -->
                <div id="content-users" class="tab-content hidden">
                    @include('spaces.tabs.users', ['usersWithStats' => $usersWithStats, 'space' => $space])
                </div>

                <!-- Tab: Editar (solo admin) -->
                @if($isAdmin)
                <div id="content-edit" class="tab-content hidden">
                    @include('spaces.tabs.edit', ['space' => $space])
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover active de todos los botones
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Mostrar el contenido seleccionado
    const content = document.getElementById('content-' + tabName);
    if (content) {
        content.classList.remove('hidden');
    }
    
    // Activar el botón seleccionado
    const button = document.getElementById('tab-' + tabName);
    if (button) {
        button.classList.add('active', 'border-blue-500', 'text-blue-600');
        button.classList.remove('border-transparent', 'text-gray-500');
    }
}
</script>

<style>
.tab-button.active {
    border-bottom-color: #3b82f6;
    color: #2563eb;
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
