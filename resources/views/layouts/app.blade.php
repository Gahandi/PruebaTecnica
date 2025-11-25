<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ config('app.url') }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- OpenPay Scripts -->
    <script src="https://js.openpay.mx/openpay.v1.min.js"></script>
    <script src="https://js.openpay.mx/openpay-data.v1.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b sticky top-0 left-0 right-0 z-50 border-gray-200">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Logo/Brand -->
                    <div class="flex items-center flex-1 md:flex-none justify-center md:justify-start">
                        <a href="{{ config('app.url') }}/" class="text-lg sm:text-xl font-bold text-gray-900">
                            {{ config('app.name', 'Boletos') }}
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center flex-row-reverse gap-4 lg:gap-6">
                                                    <!-- Admin Menu -->
                        @php
                            $user = auth()->user();
                            $isAdmin = $user && ($user->role === 'admin');
                            $isStaff = $user && ($user->role === 'staff');
                        @endphp
                        @if(auth()->check() && ($isAdmin || $isStaff))
                            <div class="relative group" id="admin-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center text-sm lg:text-base" onclick="toggleAdminDropdown()">
                                    <span class="hidden lg:inline">Administración</span>
                                    <span class="lg:hidden">Admin</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="admin-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-52 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200" id="admin-menu" style="display: none;">
                                    @if($isAdmin)
                                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Dashboard
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                    </div>

                    <!-- Desktop Right Menu -->
                    <div class="hidden md:flex items-center space-x-2 lg:space-x-4">
                        <!-- Cart Dropdown -->
                        <div class="relative group" id="cart-dropdown">
                            <button class="text-gray-700 hover:text-gray-900 relative p-2 rounded-lg hover:bg-gray-100 transition-colors group" onclick="toggleCartDropdown()">
                            <svg class="w-5 h-5 lg:w-6 lg:h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                            </svg>

                            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center
                                        bg-red-500 text-white text-xs font-bold
                                        min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg"
                                  id="cart-count-badge" style="display: none;">
                                0
                            </span>
                            </button>

                            <!-- Cart Dropdown Menu -->
                            <div class="fixed sm:absolute right-0 top-16 sm:top-auto sm:mt-2 w-full sm:w-80 lg:w-96 max-w-sm bg-white rounded-lg shadow-xl z-50 border border-gray-200 max-h-[calc(100vh-4rem)] overflow-hidden flex flex-col" id="cart-menu" style="display: none;">
                                @php
                                    $cart = \App\Helpers\CartHelper::getCartWithEventInfo();
                                    $cartCount = \App\Helpers\CartHelper::getCartCount();
                                    $subtotal = \App\Helpers\CartHelper::getCartTotal();
                                    $taxes = $subtotal * 0.16; // 16% IVA
                                    $cartTotal = $subtotal + $taxes; // Total con IVA
                                @endphp
                                @include('partials.cart-dropdown', ['cart' => $cart, 'cartCount' => $cartCount, 'cartTotal' => $cartTotal])
                            </div>
                        </div>
                        @auth
                            <!-- Spaces Menu Dropdown -->
                            <div class="relative group" id="spaces-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center" onclick="toggleSpacesDropdown()">
                                    <svg class="w-6 h-6 transition-transform duration-200 hover:scale-110" id="spaces-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="ml-1">Espacios</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="spaces-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200" id="spaces-menu" style="display: none;">
                                    @if(auth()->user()->spaces->count() > 0)
                                        @foreach(auth()->user()->spaces as $space)
                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="font-medium">{{ $space->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                        <div class="border-t border-gray-100 my-1"></div>
                                    @endif

                                    <a href="{{ route('user.spaces.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Gestionar Mis Espacios
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- User Menu Dropdown -->
                            <div class="relative group" id="user-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center" onclick="toggleUserDropdown()">
                                    <svg class="w-6 h-6 transition-transform duration-200 hover:scale-110" id="user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="user-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200" id="user-menu" style="display: none;">
                                    <!-- User Info -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }} {{ auth()->user()->last_name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>

                                    <a href="{{ config('app.url') }}/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Perfil
                                        </div>
                                    </a>

                                    <a href="{{ config('app.url') }}/my-tickets" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            Mis Boletos
                                        </div>
                                    </a>


                                    <!-- Logout -->
                                    <form method="POST" action="{{ config('app.url') }}/logout" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Cerrar sesión
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ config('app.url') }}/login" class="text-gray-700 hover:text-gray-900 text-sm lg:text-base">
                                Iniciar sesión
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ config('app.url') }}/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                        Eventos
                    </a>
                    @php
                        $mobileUser = auth()->user();
                        $mobileIsAdmin = $mobileUser && ($mobileUser->role === 'admin');
                        $mobileIsStaff = $mobileUser && ($mobileUser->role === 'staff');
                    @endphp
                    @if(auth()->check() && ($mobileIsAdmin || $mobileIsStaff))
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Administración</p>
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.events.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Eventos
                            </a>
                            <a href="{{ route('admin.ticket-types.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Tipos de Boletos
                            </a>
                            <a href="{{ route('admin.coupons.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Cupones
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Órdenes
                            </a>
                            @if($mobileIsAdmin)
                                <a href="{{ route('admin.checkins.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                    Check-ins
                                </a>
                            @endif
                            @if($mobileIsStaff)
                                <a href="{{ route('staff.checkins.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                    Check-ins
                                </a>
                            @endif
                        </div>
                    @endif
                    @auth
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mi Cuenta</p>
                            <a href="{{ config('app.url') }}/profile" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Perfil
                            </a>
                            <a href="{{ config('app.url') }}/my-tickets" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Mis Boletos
                            </a>
                            @if(auth()->user()->spaces->count() > 0)
                                <div class="border-t border-gray-200 mt-2 pt-2">
                                    <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mis Espacios</p>
                                    @foreach(auth()->user()->spaces as $space)
                                        <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                            {{ $space->name }}
                                        </a>
                                    @endforeach
                                    <a href="{{ route('user.spaces.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                        Gestionar Espacios
                                    </a>
                                </div>
                            @endif
                        </div>
                        <!-- Mobile Cart -->
                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <button onclick="toggleCartDropdown()" class="w-full flex items-center justify-between px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                <span>Carrito</span>
                                <span id="mobile-cart-count" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full" style="display: none;">0</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ config('app.url') }}/logout" class="border-t border-gray-200 pt-2 mt-2">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:text-red-900 hover:bg-red-50">
                                Cerrar sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ config('app.url') }}/login" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            Iniciar sesión
                        </a>
                    @endauth
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
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                button.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            } else {
                menu.classList.add('hidden');
                button.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>';
            }
        }

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

        function toggleSpacesDropdown() {
            const menu = document.getElementById('spaces-menu');
            const arrow = document.getElementById('spaces-arrow');
            const icon = document.getElementById('spaces-icon');

            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
                icon.style.transform = 'scale(1.1)';
            } else {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
                icon.style.transform = 'scale(1)';
            }
        }

        // Funciones del carrito - disponibles inmediatamente
        function toggleCartDropdown() {
            const cartMenu = document.getElementById('cart-menu');
            if (!cartMenu) {
                console.error('Cart menu not found');
                return;
            }
            
            if (cartMenu.style.display === 'none' || cartMenu.style.display === '') {
                // Abrir dropdown y actualizar contenido desde el servidor
                cartMenu.style.display = 'block';
                if (typeof window.updateCartDropdown === 'function') {
                    window.updateCartDropdown();
                } else if (typeof window.renderCartDropdown === 'function') {
                    window.renderCartDropdown();
                } else {
                    console.error('Cart functions not available');
                }
            } else {
                // Cerrar dropdown
                cartMenu.style.display = 'none';
            }
        }
        
        // Inicializar contador cuando el DOM esté listo (solo una vez)
        let cartCounterInitialized = false;
        function initCartCounter() {
            if (cartCounterInitialized) {
                return;
            }
            cartCounterInitialized = true;
            
            setTimeout(function() {
                if (typeof window.updateCartCount === 'function') {
                    window.updateCartCount().then(count => {
                        // Actualizar contador móvil también
                        const mobileCartCount = document.getElementById('mobile-cart-count');
                        if (mobileCartCount) {
                            if (count > 0) {
                                mobileCartCount.textContent = count;
                                mobileCartCount.style.display = 'inline-block';
                            } else {
                                mobileCartCount.style.display = 'none';
                            }
                        }
                    });
                }
            }, 500);
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCartCounter);
        } else {
            initCartCounter();
        }
        
        function closeCartDropdown() {
            const cartMenu = document.getElementById('cart-menu');
            if (cartMenu) {
                cartMenu.style.display = 'none';
            }
        }
        
        // Hacer funciones disponibles globalmente
        window.toggleCartDropdown = toggleCartDropdown;
        window.closeCartDropdown = closeCartDropdown;
        
        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(event) {
            const cartDropdown = document.getElementById('cart-dropdown');
            const cartMenu = document.getElementById('cart-menu');

            if (cartDropdown && cartMenu && !cartDropdown.contains(event.target)) {
                closeCartDropdown();
            }
        });

        // Cerrar dropdown cuando se hace clic en los enlaces del carrito
        document.addEventListener('click', function(event) {
            if (event.target.closest('a[href*="cart"]') || event.target.closest('a[href*="checkout"]')) {
                closeCartDropdown();
            }
        });

        function toggleUserDropdown() {
            const menu = document.getElementById('user-menu');
            const arrow = document.getElementById('user-arrow');
            const icon = document.getElementById('user-icon');

            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
                icon.style.transform = 'scale(1.1)';
            } else {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
                icon.style.transform = 'scale(1)';
            }
        }

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(event) {
            const adminDropdown = document.getElementById('admin-dropdown');
            const adminMenu = document.getElementById('admin-menu');
            const adminArrow = document.getElementById('admin-arrow');

            const spacesDropdown = document.getElementById('spaces-dropdown');
            const spacesMenu = document.getElementById('spaces-menu');
            const spacesArrow = document.getElementById('spaces-arrow');
            const spacesIcon = document.getElementById('spaces-icon');

            const userDropdown = document.getElementById('user-dropdown');
            const userMenu = document.getElementById('user-menu');
            const userArrow = document.getElementById('user-arrow');
            const userIcon = document.getElementById('user-icon');

            // Cerrar admin dropdown
            if (adminDropdown && !adminDropdown.contains(event.target)) {
                adminMenu.style.display = 'none';
                adminArrow.style.transform = 'rotate(0deg)';
            }

            // Cerrar spaces dropdown
            if (spacesDropdown && !spacesDropdown.contains(event.target)) {
                spacesMenu.style.display = 'none';
                spacesArrow.style.transform = 'rotate(0deg)';
                spacesIcon.style.transform = 'scale(1)';
            }

            // Cerrar user dropdown
            if (userDropdown && !userDropdown.contains(event.target)) {
                userMenu.style.display = 'none';
                userArrow.style.transform = 'rotate(0deg)';
                userIcon.style.transform = 'scale(1)';
            }
        });
    </script>
    @if(session('clear_cart_localstorage'))
        <script>
            // Invalidar cache del carrito después de una compra exitosa
            if (typeof window.invalidateCartCache === 'function') {
                window.invalidateCartCache();
            }
            // Actualizar contador del carrito
            if (typeof window.updateCartCount === 'function') {
                window.updateCartCount();
            }
        </script>
    @endif
    </body>
</html>
