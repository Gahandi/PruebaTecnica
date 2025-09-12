<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center flex-row-reverse gap-6">
                                                    <!-- Admin Menu -->
                                                    @can('view admin panel')
                            <div class="relative group" id="admin-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center" onclick="toggleAdminDropdown()">
                                    Administración
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="admin-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-52 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200" id="admin-menu" style="display: none;">
                                    @can('view dashboard')
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors border-b border-gray-100">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            Dashboard
                                        </div>
                                    </a>
                                    @endcan
                                    @can('view events')
                                    <a href="{{ route('admin.events.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Eventos
                                        </div>
                                    </a>
                                    @endcan
                                    @can('view ticket types')
                                    <a href="{{ route('admin.ticket-types.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            Tipos de Boletos
                                        </div>
                                    </a>
                                    @endcan
                                    @can('view coupons')
                                    <a href="{{ route('admin.coupons.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            Cupones
                                        </div>
                                    </a>
                                    @endcan
                                    @can('view orders')
                                    <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            Órdenes
                                        </div>
                                    </a>
                                    @endcan
                                    @can('view checkins')
                                    <a href="{{ route('admin.checkins.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Check-ins
                                        </div>
                                    </a>
                                    @endcan
                                </div>
                            </div>
                            @endcan
                        <a href="{{ route('events.public') }}" class="text-xl font-bold text-gray-900">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Cart Link -->
                        <a href="{{ route('checkout.cart') }}" class="text-gray-700 hover:text-gray-900 flex items-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <span class="ml-1">Carrito</span>
                            @if(session('cart'))
                                <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-1">
                                    {{ count(session('cart')) }}
                                </span>
                            @endif
                        </a>
                        
                        @auth

                            
                            <!-- Public Menu -->
                            <a href="{{ route('events.public') }}" class="text-gray-700 hover:text-gray-900">
                                Eventos
                            </a>
                            <a href="{{ route('tickets.my') }}" class="text-gray-700 hover:text-gray-900">
                                Mis Boletos
                            </a>
                            <a href="{{ route('profile') }}" class="text-gray-700 hover:text-gray-900">
                                Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    Cerrar sesión
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">
                                Iniciar sesión
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    @stack('scripts')
    
    <script>
        function toggleAdminDropdown() {
            const menu = document.getElementById('admin-menu');
            const arrow = document.getElementById('admin-arrow');
            
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('admin-dropdown');
            const menu = document.getElementById('admin-menu');
            const arrow = document.getElementById('admin-arrow');
            
            if (dropdown && !dropdown.contains(event.target)) {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        });
    </script>
</body>
</html>
