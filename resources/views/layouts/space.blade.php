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
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Space Header -->
        <header class="bg-white shadow-sm border-b sticky top-0 left-0 right-0 z-50 border-gray-200">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Left: Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button id="mobile-menu-button"
                            class="p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none"
                            onclick="toggleMobileMenu()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Center (Mobile) / Left (Desktop): Space Logo/Name -->
                    <div class="flex-1 flex justify-center md:justify-start min-w-0">
                        <a href="{{ route('spaces.profile', isset($space) && $space ? $space->subdomain : '') }}"
                            class="flex items-center space-x-2 sm:space-x-4">
                            @if(isset($space) && $space && $space->logo)
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}"
                                    alt="{{ $space->name ?? 'Space' }}"
                                    class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div
                                    class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-white font-bold text-sm sm:text-lg">{{ substr(isset($space) && $space ? $space->name : 'S', 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0 flex flex-col items-center md:items-start">
                                <h1 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 truncate">
                                    {{ isset($space) && $space ? $space->name : 'Space' }}
                                </h1>
                                <p class="text-xs sm:text-sm text-gray-500 truncate hidden sm:block">
                                    {{ (isset($space) && $space ? $space->subdomain : '') }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}
                                </p>
                            </div>
                        </a>
                    </div>

                    <!-- Right: Cart & Desktop Navigation -->
                    <div class="flex items-center gap-2 md:gap-4">

                        <!-- Cart Dropdown (Visible on Mobile & Desktop) -->
                        <div class="relative group" id="cart-dropdown">
                            <button
                                class="text-gray-700 hover:text-gray-900 relative p-2 rounded-lg hover:bg-gray-100 transition-colors group"
                                onclick="toggleCartDropdown()">
                                <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                                </svg>

                                <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center
                                            bg-red-500 text-white text-xs font-bold
                                            min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg"
                                    id="cart-count-badge" style="display: none;">
                                    0
                                </span>
                            </button>

                            <!-- Cart Dropdown Menu -->
                            <div class="fixed sm:absolute right-0 top-16 sm:top-auto sm:mt-2 w-full sm:w-80 lg:w-96 max-w-sm bg-white rounded-lg shadow-xl z-50 border border-gray-200 max-h-[calc(100vh-4rem)] overflow-hidden flex flex-col"
                                id="cart-menu" style="display: none;">
                                @php
                                    $cart = \App\Helpers\CartHelper::getCartWithEventInfo();
                                    $cartCount = \App\Helpers\CartHelper::getCartCount();
                                    $subtotal = \App\Helpers\CartHelper::getCartTotal();
                                    $serviceChargePercentage = \App\Helpers\SettingsHelper::getServiceChargePercentage();
                                    $taxes = $subtotal * ($serviceChargePercentage / 100);
                                    $cartTotal = $subtotal + $taxes;
                                @endphp
                                @include('partials.cart-dropdown', ['cart' => $cart, 'cartCount' => $cartCount, 'cartTotal' => $cartTotal])
                            </div>
                        </div>

                        <!-- Desktop Navigation Links (Hidden on Mobile) -->
                        <div class="hidden md:flex items-center space-x-2 lg:space-x-4">
                            <!-- Helper Links -->
                            <a href="{{ route('spaces.profile', isset($space) && $space ? $space->subdomain : '') }}"
                                class="text-gray-700 hover:text-gray-900 text-sm lg:text-base">
                                Inicio
                            </a>
                            <a href="{{ config('app.url') }}"
                                class="text-gray-700 hover:text-gray-900 text-sm lg:text-base">
                                Todos los Eventos
                            </a>

                            @auth
                                @if(isset($space) && $space)
                                    @php
                                        $user = auth()->user();
                                        $isAdmin = $user->spaces()
                                            ->where('spaces.id', $space->id)
                                            ->wherePivot('role_space_id', 1)
                                            ->wherePivotNull('deleted_at')
                                            ->exists();
                                        $isStaff = $user->role === 'staff' || $user->role === 'admin'; // Global admin/staff
                                        $hasPermission = \App\Models\RoleSpacePermission::hasPermission($space->id, 'create checkins');
                                        $canSeeScanner = $isAdmin || $hasPermission;
                                    @endphp

                                    @if($canSeeScanner)
                                        <a href="{{ route('scanner.index', ['subdomain' => isset($space) && $space ? $space->subdomain : '']) }}"
                                            class="text-gray-700 hover:text-gray-900 flex items-center gap-1 text-sm lg:text-base">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                                </path>
                                            </svg>
                                            <span class="hidden lg:inline">Scanner</span>
                                        </a>
                                    @endif

                                    @if($isAdmin)
                                        <a href="{{ route('spaces.coupons.index', isset($space) && $space ? $space->subdomain : '') }}"
                                            class="text-gray-700 hover:text-gray-900 flex items-center gap-1 text-sm lg:text-base">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                </path>
                                            </svg>
                                            <span class="hidden lg:inline">Cupones</span>
                                        </a>
                                    @endif
                                @endif

                                <!-- User Menu Dropdown -->
                                <div class="relative group" id="user-dropdown">
                                    <button class="text-gray-700 hover:text-gray-900 flex items-center"
                                        onclick="toggleUserDropdown()">
                                        <svg class="w-6 h-6 transition-transform duration-200 hover:scale-110"
                                            id="user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <svg class="w-4 h-4 ml-1 transition-transform duration-200 hidden lg:block"
                                            id="user-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                                        id="user-menu" style="display: none;">
                                        <!-- User Info -->
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                        </div>

                                        <!-- Menu Items -->
                                        <a href="{{ config('app.url') }}/profile"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            Perfil
                                        </a>

                                        <a href="{{ config('app.url') }}/my-tickets"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            Mis Boletos
                                        </a>

                                        @if($isStaff)
                                            <a href="{{ config('app.url') }}/dashboard"
                                                class="block px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition-colors border-t border-gray-100 mt-1 pt-1">
                                                Admin General
                                            </a>
                                        @endif

                                        <!-- Logout -->
                                        <form method="POST" action="{{ config('app.url') }}/logout"
                                            class="block mt-1 pt-1 border-t border-gray-100">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                                Cerrar sesión
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ config('app.url') }}/login"
                                    class="text-gray-700 hover:text-gray-900 text-sm lg:text-base font-medium">
                                    Iniciar sesión
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu (Unified) -->
                <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <!-- Navigation Links Requested -->
                        <a href="{{ route('spaces.profile', isset($space) && $space ? $space->subdomain : '') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-pink-50 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-pink-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                Inicio del Espacio
                            </div>
                        </a>

                        <a href="{{ config('app.url') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-pink-50 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                                    </path>
                                </svg>
                                Inicio de la Página (Todos los Eventos)
                            </div>
                        </a>

                        @auth
                            @php
                                $user = auth()->user();
                                $isAdminSpace = isset($space) && $user->spaces()->where('spaces.id', $space->id)->wherePivot('role_space_id', 1)->exists();
                                $hasScanner = \App\Models\RoleSpacePermission::hasPermission($space->id, 'create checkins');
                            @endphp

                            <!-- Admin Space Tools -->
                            @if(isset($space) && ($isAdminSpace || $hasScanner))
                                <div class="border-t border-gray-200 my-2 pt-2">
                                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        Administración del Espacio</p>
                                    @if($isAdminSpace || $hasScanner)
                                        <a href="{{ route('scanner.index', ['subdomain' => $space->subdomain]) }}"
                                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                            Scanner
                                        </a>
                                    @endif

                                    @if($isAdminSpace)
                                        <a href="{{ route('spaces.coupons.index', $space->subdomain) }}"
                                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                            Cupones
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <!-- User Account -->
                            <div class="border-t border-gray-200 mt-2 pt-2">
                                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Mi Cuenta
                                </p>
                                <div class="px-3 mb-2 flex items-center">
                                    <div class="flex-shrink-0">
                                        <span
                                            class="h-8 w-8 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-bold">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-base font-medium text-gray-800">{{ $user->name }}</div>
                                        <div class="text-sm font-medium text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>

                                <a href="{{ config('app.url') }}/profile"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                    Perfil y Configuración
                                </a>
                                <a href="{{ config('app.url') }}/my-tickets"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                    Mis Boletos
                                </a>
                                @if($user->role === 'staff' || $user->role === 'admin')
                                    <a href="{{ config('app.url') }}/dashboard"
                                        class="block px-3 py-2 rounded-md text-base font-medium text-blue-600 hover:bg-blue-50">
                                        Admin General
                                    </a>
                                @endif
                            </div>

                            <form method="POST" action="{{ config('app.url') }}/logout"
                                class="mt-2 border-t border-gray-200 pt-2">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:text-red-900 hover:bg-red-50 flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    Cerrar sesión
                                </button>
                            </form>
                        @else
                            <div class="mt-4 border-t border-gray-200 pt-4">
                                <a href="{{ config('app.url') }}/login"
                                    class="flex w-full items-center justify-center rounded-md border border-transparent bg-pink-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-pink-700">
                                    Iniciar sesión
                                </a>
                                <p class="mt-2 text-center text-sm text-gray-500">
                                    ¿Aún no tienes cuenta?
                                    <a href="{{ config('app.url') }}/register"
                                        class="font-medium text-pink-600 hover:text-pink-500">
                                        Regístrate
                                    </a>
                                </p>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

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

            setTimeout(function () {
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
        document.addEventListener('click', function (event) {
            const cartDropdown = document.getElementById('cart-dropdown');
            const cartMenu = document.getElementById('cart-menu');

            if (cartDropdown && cartMenu && !cartDropdown.contains(event.target)) {
                closeCartDropdown();
            }
        });

        // Cerrar dropdown cuando se hace clic en los enlaces del carrito
        document.addEventListener('click', function (event) {
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
        document.addEventListener('click', function (event) {
            const userDropdown = document.getElementById('user-dropdown');
            const userMenu = document.getElementById('user-menu');
            const userArrow = document.getElementById('user-arrow');
            const userIcon = document.getElementById('user-icon');

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