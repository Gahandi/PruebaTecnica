<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ config('app.url') }}">

    <title>@yield('title', isset($space) && $space ? $space->name : config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Sidebar overlay */
        .sidebar-overlay {
            background: rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease;
        }
    </style>
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-100">
    @php
        $user = auth()->user();
        $isAdmin = $user && isset($space) && $user->isAdminOfSpace($space->id);
        $isStaff = false;
        $hasPermission = false;
        
        if ($user && isset($space)) {
            $userSpace = $user->spaces()->where('spaces.id', $space->id)->first();
            $isStaff = $userSpace && $userSpace->pivot->role_space_id == 2;
            $hasPermission = \App\Models\RoleSpacePermission::hasPermission($space->id, 'create checkins');
        }
        $canSeeScanner = $isAdmin || $hasPermission;
    @endphp

    <div class="min-h-screen flex">
        @if($isAdmin || $isStaff)
        <!-- Mobile Sidebar Overlay (Admin/Staff only) -->
        <div id="sidebar-overlay" 
             class="fixed inset-0 z-40 sidebar-overlay hidden lg:hidden"
             onclick="closeSidebar()"></div>

        <!-- Sidebar - Fixed on mobile, static on desktop (Admin/Staff only) -->
        <aside id="sidebar" 
               class="fixed lg:relative inset-y-0 left-0 z-50 w-64 bg-white shadow-xl 
                      transform -translate-x-full lg:translate-x-0 
                      transition-transform duration-300 ease-in-out 
                      flex flex-col">
            
            <!-- Sidebar Header - Simple Clean Design -->
            <div class="p-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between">
                    <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}" class="flex items-center space-x-3 group">
                        @if(isset($space) && $space->logo)
                            <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" 
                                 alt="{{ $space->name }}" 
                                 class="w-10 h-10 rounded-lg object-cover shadow-sm">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center shadow-sm">
                                <span class="text-white font-bold text-lg">{{ substr($space->name ?? 'S', 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h2 class="text-gray-900 font-semibold text-sm truncate group-hover:text-pink-600 transition-colors">{{ $space->name ?? 'Space' }}</h2>
                            <p class="text-gray-400 text-xs truncate">{{ $space->subdomain ?? '' }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}</p>
                        </div>
                    </a>
                    <!-- Close button mobile -->
                    <button onclick="closeSidebar()" class="lg:hidden text-gray-400 hover:text-gray-600 p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <!-- Inicio -->
                <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                          {{ request()->routeIs('spaces.profile') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('spaces.profile') ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Inicio
                </a>

                @if($canSeeScanner)
                <a href="{{ route('scanner.index', ['subdomain' => $space->subdomain ?? '']) }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                          {{ request()->routeIs('scanner.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('scanner.*') ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    Scanner
                </a>
                @endif

                @if($isAdmin)
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</p>
                    </div>

                    <!-- Dashboard -->
                    <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}?tab=dashboard" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->get('tab') === 'dashboard' ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->get('tab') === 'dashboard' ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Tablero
                    </a>

                    <!-- Eventos -->
                    <a href="{{ route('spaces.events.create', $space->subdomain ?? '') }}" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('spaces.events.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('spaces.events.*') ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Eventos
                    </a>

                    <!-- Usuarios -->
                    <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}?tab=users" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->get('tab') === 'users' ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->get('tab') === 'users' ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Usuarios
                    </a>

                    <!-- Órdenes -->
                    <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}?tab=orders" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->get('tab') === 'orders' ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->get('tab') === 'orders' ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Órdenes
                    </a>

                    <!-- Cupones -->
                    <a href="{{ route('spaces.coupons.index', $space->subdomain ?? '') }}" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->routeIs('spaces.coupons.*') ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('spaces.coupons.*') ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Cupones
                    </a>

                    <!-- Roles y Permisos -->
                    <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}?tab=roles" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->get('tab') === 'roles' ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->get('tab') === 'roles' ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Roles y Permisos
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Configuración</p>
                    </div>

                    <!-- Editar Espacio -->
                    <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}?tab=edit" 
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                              {{ request()->get('tab') === 'edit' ? 'bg-pink-50 text-pink-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->get('tab') === 'edit' ? 'text-pink-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Configuración
                    </a>
                @endif
            </nav>

            <!-- Sidebar Footer - Back to main -->
            <div class="p-4 border-t border-gray-200">
                <a href="{{ config('app.url') }}" 
                   class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    Volver a Eventos
                </a>
            </div>
        </aside>
        @endif

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Navbar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Left: Mobile menu + Search/Breadcrumb -->
                        <div class="flex items-center">
                            @if($isAdmin || $isStaff)
                            <!-- Mobile menu button (Admin/Staff only) -->
                            <button onclick="openSidebar()" class="lg:hidden p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 mr-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            @endif
                            
                            <!-- Page Title / Breadcrumb -->
                            <h1 class="text-lg font-semibold text-gray-900">@yield('page_title', 'Inicio')</h1>
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex items-center space-x-2">
                            <!-- Spaces Dropdown -->
                             @auth
                            <div class="relative" id="spaces-dropdown-top">
                                <button onclick="toggleSpacesDropdownTop()" class="flex items-center text-sm text-gray-700 hover:text-gray-900 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Mis Espacios</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="spaces-menu-top" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50 hidden max-h-96 overflow-y-auto">
                                    @if($user && $user->spaces->count() > 0)
                                        @php
                                            $adminSpaces = $user->spaces->filter(fn($s) => $s->pivot->role_space_id == 1);
                                            $staffSpaces = $user->spaces->filter(fn($s) => $s->pivot->role_space_id == 2);
                                            $followingSpaces = $user->spaces->filter(fn($s) => $s->pivot->role_space_id == 3);
                                        @endphp
                                        
                                        {{-- Mis Espacios (Admin) --}}
                                        @if($adminSpaces->count() > 0)
                                            <div class="px-3 py-2 bg-pink-50 border-b border-pink-100">
                                                <span class="text-xs font-semibold text-pink-700 uppercase tracking-wider">Mis Espacios</span>
                                            </div>
                                            @foreach($adminSpaces as $adminSpace)
                                                <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($adminSpace->subdomain) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ isset($space) && $space->id === $adminSpace->id ? 'bg-pink-50 text-pink-700' : '' }}">
                                                    @if($adminSpace->logo)
                                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($adminSpace->logo) }}" class="w-6 h-6 rounded mr-2 object-cover">
                                                    @else
                                                        <div class="w-6 h-6 rounded bg-pink-200 mr-2 flex items-center justify-center text-xs font-bold text-pink-600">
                                                            {{ substr($adminSpace->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span class="truncate">{{ $adminSpace->name }}</span>
                                                    <svg class="w-4 h-4 ml-auto text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </a>
                                            @endforeach
                                        @endif

                                        {{-- Donde Soy Staff --}}
                                        @if($staffSpaces->count() > 0)
                                            <div class="px-3 py-2 bg-blue-50 border-b border-blue-100 {{ $adminSpaces->count() > 0 ? 'border-t' : '' }}">
                                                <span class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Donde Soy Staff</span>
                                            </div>
                                            @foreach($staffSpaces as $staffSpace)
                                                <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($staffSpace->subdomain) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ isset($space) && $space->id === $staffSpace->id ? 'bg-blue-50 text-blue-700' : '' }}">
                                                    @if($staffSpace->logo)
                                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($staffSpace->logo) }}" class="w-6 h-6 rounded mr-2 object-cover">
                                                    @else
                                                        <div class="w-6 h-6 rounded bg-blue-200 mr-2 flex items-center justify-center text-xs font-bold text-blue-600">
                                                            {{ substr($staffSpace->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span class="truncate">{{ $staffSpace->name }}</span>
                                                    <svg class="w-4 h-4 ml-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </a>
                                            @endforeach
                                        @endif

                                        {{-- Espacios que Sigo --}}
                                        @if($followingSpaces->count() > 0)
                                            <div class="px-3 py-2 bg-purple-50 border-b border-purple-100 {{ ($adminSpaces->count() > 0 || $staffSpaces->count() > 0) ? 'border-t' : '' }}">
                                                <span class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Espacios que Sigo</span>
                                            </div>
                                            @foreach($followingSpaces as $followSpace)
                                                <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($followSpace->subdomain) }}" 
                                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ isset($space) && $space->id === $followSpace->id ? 'bg-purple-50 text-purple-700' : '' }}">
                                                    @if($followSpace->logo)
                                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($followSpace->logo) }}" class="w-6 h-6 rounded mr-2 object-cover">
                                                    @else
                                                        <div class="w-6 h-6 rounded bg-purple-200 mr-2 flex items-center justify-center text-xs font-bold text-purple-600">
                                                            {{ substr($followSpace->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span class="truncate">{{ $followSpace->name }}</span>
                                                    <svg class="w-4 h-4 ml-auto text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </a>
                                            @endforeach
                                        @endif
                                        <div class="border-t border-gray-100"></div>
                                    @endif
                                    <a href="{{ config('app.url') }}/spaces" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Gestionar Espacios
                                    </a>
                                </div>
                            </div>
                            @endauth
                            <!-- User Menu -->
                            @auth
                            <div class="relative" id="user-dropdown-top">
                                <button onclick="toggleUserDropdownTop()" class="flex items-center text-gray-700 hover:text-gray-900">
                                    @if($user->image)
                                        <img src="{{ \App\Helpers\ImageHelper::getImageUrl($user->image) }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-white text-sm font-bold">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div id="user-menu-top" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50 hidden">
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }} {{ $user->last_name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                    <a href="{{ config('app.url') }}/profile" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Mi Perfil
                                    </a>
                                    <a href="{{ config('app.url') }}/my-tickets" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        Mis Boletos
                                    </a>
                                    <form method="POST" action="{{ config('app.url') }}/logout" class="border-t border-gray-100">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                                <!-- Guest Navigation -->
                                <a href="{{ config('app.url') }}" class="text-sm text-gray-700 hover:text-pink-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Inicio</span>
                                </a>
                                <a href="{{ config('app.url') }}/events" class="text-sm text-gray-700 hover:text-pink-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Eventos</span>
                                </a>
                                <a href="{{ config('app.url') }}/login" class="text-sm bg-pink-600 text-white hover:bg-pink-700 px-4 py-2 rounded-lg font-medium transition-colors">
                                    Iniciar sesión
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')

    <script>
        // Sidebar functions
        function openSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Dropdown functions
        function toggleSpacesDropdownTop() {
            const menu = document.getElementById('spaces-menu-top');
            menu.classList.toggle('hidden');
        }

        function toggleUserDropdownTop() {
            const menu = document.getElementById('user-menu-top');
            menu.classList.toggle('hidden');
        }

        // Close dropdowns on click outside
        document.addEventListener('click', function(event) {
            const spacesDropdown = document.getElementById('spaces-dropdown-top');
            const spacesMenu = document.getElementById('spaces-menu-top');
            if (spacesDropdown && spacesMenu && !spacesDropdown.contains(event.target)) {
                spacesMenu.classList.add('hidden');
            }

            const userDropdown = document.getElementById('user-dropdown-top');
            const userMenu = document.getElementById('user-menu-top');
            if (userDropdown && userMenu && !userDropdown.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Close sidebar on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
