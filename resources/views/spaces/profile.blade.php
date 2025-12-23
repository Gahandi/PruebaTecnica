@extends('layouts.space-dashboard')

@section('title', $space->name)

@php
    use App\Models\RoleSpacePermission;
    $canSeeScanner = RoleSpacePermission::hasPermission($space->id, 'create checkins');
    $currentTab = request()->get('tab', 'events');
@endphp

@section('page_title')
    @switch($currentTab)
        @case('dashboard') Dashboard @break
        @case('users') Usuarios @break
        @case('orders') Órdenes @break
        @case('roles') Roles y Permisos @break
        @case('edit') Configuración @break
        @default Inicio @break
    @endswitch
@endsection

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="bg-gray-50">
    <!-- Banner Header with Modern Design -->
    <div class="relative">
        <!-- Banner Image -->
        @if($space->banner)
            <div class="h-48 md:h-64 lg:h-80 w-full overflow-hidden">
                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->banner) }}" 
                     alt="{{ $space->name }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            </div>
        @else
            <div class="h-48 md:h-64 lg:h-80 w-full bg-gradient-to-br from-pink-400 via-pink-500 to-pink-600">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            </div>
        @endif
        
        <!-- Overlay Content -->
        <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-end md:items-center gap-4">
                <!-- Logo -->
                <div class="flex-shrink-0 -mb-12 md:-mb-16 z-10">
                    @if($space->logo)
                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" 
                             alt="{{ $space->name }}" 
                             class="w-24 h-24 md:w-32 md:h-32 rounded-2xl object-cover border-4 border-white shadow-xl">
                    @else
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-2xl bg-white shadow-xl flex items-center justify-center">
                            <span class="text-pink-600 font-bold text-4xl md:text-5xl">{{ strtoupper(substr($space->name, 0, 1)) }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Space Info on Banner -->
                <div class="flex-1 text-white mb-2 md:ml-4">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold drop-shadow-lg">{{ $space->name }}</h1>
                    @if($space->description)
                        <p class="text-white/90 mt-1 text-sm md:text-base line-clamp-2 drop-shadow">{{ $space->description }}</p>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <!-- Stats Badge -->
                    <div class="bg-white/20 backdrop-blur-md rounded-full px-4 py-2 flex items-center space-x-2 border border-white/30">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                        </svg>
                        <span class="text-white font-semibold">{{ $followerCount ?? 0 }}</span>
                    </div>
                    
                    @auth
                        @if(!$isAdmin && !($isStaff ?? false) && !($isMember ?? false))
                            <button type="button" onclick="followSpace()" id="followBtn"
                                    class="inline-flex items-center px-5 py-2.5 bg-white text-pink-600 font-semibold rounded-full shadow-lg hover:bg-pink-50 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Seguir
                            </button>
                        @elseif(!$isAdmin && !($isStaff ?? false) && ($isMember ?? false))
                            <button type="button" onclick="confirmUnfollow()" id="unfollowBtn"
                                    class="inline-flex items-center px-5 py-2.5 bg-white/20 backdrop-blur-md text-white font-semibold rounded-full border border-white/30 hover:bg-white/30 transition-all">
                                <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Siguiendo
                            </button>
                        @endif
                    @else
                        <a href="{{ config('app.url') }}/login" 
                           class="inline-flex items-center px-5 py-2.5 bg-white text-pink-600 font-semibold rounded-full shadow-lg hover:bg-pink-50 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Seguir
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8 pt-16 md:pt-20 pb-8">
        <!-- Keywords Bar (only if keywords exist) -->
        @if($currentTab === 'events' && $space->keywords)
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-500 mr-2">Categorías:</span>
            @foreach(explode(',', $space->keywords) as $keyword)
                @if(trim($keyword))
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white shadow-sm text-gray-700 border border-gray-200 hover:border-pink-300 hover:text-pink-600 transition-colors cursor-default">
                        {{ trim($keyword) }}
                    </span>
                @endif
            @endforeach
        </div>
        @endif

        <!-- Tab Content -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden p-4 sm:p-8">
            <!-- Tab: Eventos -->
                @if($currentTab === 'events')
                    @include('spaces.tabs.events', ['space' => $space, 'isAdmin' => $isAdmin])
                @endif

                @if($isAdmin)
                    <!-- Tab: Dashboard -->
                    @if($currentTab === 'dashboard')
                        @include('spaces.tabs.dashboard', [
                            'space' => $space,
                            'totalEvents' => $totalEvents,
                            'totalMembers' => $totalMembers,
                            'totalTicketsAvailable' => $totalTicketsAvailable,
                            'totalTicketsSold' => $totalTicketsSold,
                            'totalRevenue' => $totalRevenue,
                            'monthlyRevenueData' => $monthlyRevenueData ?? ['months' => [], 'revenues' => []],
                            'ticketsByType' => $ticketsByType ?? collect(),
                            'recentOrders' => $recentOrders ?? collect(),
                            'recentCheckins' => $recentCheckins ?? collect(),
                            'totalCheckins' => $totalCheckins ?? 0,
                            'checkinRate' => $checkinRate ?? 0,
                            'upcomingEvents' => $upcomingEvents ?? collect(),
                            'totalOrders' => $totalOrders ?? 0,
                            'averageTicketPrice' => $averageTicketPrice ?? 0,
                            'dailySalesData' => $dailySalesData ?? ['days' => [], 'revenues' => []]
                        ])
                    @endif

                    <!-- Tab: Usuarios -->
                    @if($currentTab === 'users')
                        @include('spaces.tabs.users', [
                            'usersWithStats' => $usersWithStats,
                            'space' => $space,
                            'isAdmin' => $isAdmin,
                            'roleSpaces' => $roleSpaces ?? collect()
                        ])
                    @endif

                    <!-- Tab: Órdenes -->
                    @if($currentTab === 'orders')
                        @include('spaces.tabs.orders', [
                            'spaceOrders' => $spaceOrders ?? collect()
                        ])
                    @endif

                    <!-- Tab: Roles y Permisos -->
                    @if($currentTab === 'roles')
                        @include('spaces.tabs.roles', [
                            'roleSpaces' => $roleSpaces ?? collect(),
                            'allPermissions' => $allPermissions ?? collect()
                        ])
                    @endif

                    <!-- Tab: Configuración/Editar -->
                    @if($currentTab === 'edit')
                        @include('spaces.tabs.edit', ['space' => $space])
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<script>

// Follow/Unfollow Functions
function followSpace() {
    fetch('/follow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al seguir el espacio');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function unfollowSpace() {
    if (!confirm('¿Estás seguro de que deseas dejar de seguir este espacio?')) {
        return;
    }
    
    fetch('/unfollow', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error al dejar de seguir');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión');
    });
}

function confirmUnfollow() {
    if (confirm('¿Estás seguro de que deseas dejar de seguir este espacio?')) {
        unfollowSpace();
    }
}
</script>
@endsection
