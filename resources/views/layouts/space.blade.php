<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ config('app.url') }}">

    <title>@yield('title', $space->name ?? config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Space Header -->
        <header class="bg-white shadow-sm border-b sticky top-0 left-0 right-0 z-50 border-gray-200">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Mobile menu button -->
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                        <!-- Space Logo/Name -->
                    <div class="flex items-center flex-1 md:flex-none min-w-0">
                        <div class="flex items-center space-x-2 sm:space-x-4 min-w-0">
                            @if(isset($space) && $space->logo)
                                <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" alt="{{ $space->name }}" class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-bold text-sm sm:text-lg">{{ substr($space->name ?? 'S', 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h1 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 truncate">{{ $space->name ?? 'Space' }}</h1>
                                <p class="text-xs sm:text-sm text-gray-500 truncate hidden sm:block">{{ $space->subdomain ?? '' }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-2 lg:space-x-4">
                        <!-- Cart Dropdown -->
                        <div class="relative group" id="cart-dropdown">
                            <button class="text-gray-700 hover:text-gray-900 relative p-2 rounded-lg hover:bg-gray-100 transition-colors group" onclick="toggleCartDropdown()">
                            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <!-- Header -->
                                <div class="px-3 py-2 sm:px-4 sm:py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg flex-shrink-0">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate">Carrito de Compras</h3>
                                            <p class="text-xs sm:text-sm text-gray-500">{{ \App\Helpers\CartHelper::getCartCount() }} item(s)</p>
                                        </div>
                                        <button onclick="closeCartDropdown()" class="text-gray-400 hover:text-gray-600 ml-2 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Cart Items with Scroll -->
                                <div class="flex-1 overflow-y-auto max-h-64 sm:max-h-80">
                                    @php
                                        $cart = \App\Helpers\CartHelper::getCartWithEventInfo();
                                    @endphp

                                    @if(empty($cart))
                                        <div class="px-4 py-8 text-center">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                                                </svg>
                                            </div>
                                            <p class="text-gray-500 text-sm">Tu carrito está vacío</p>
                                        </div>
                                    @else
                                        @foreach($cart as $key => $item)
                                            <div class="px-3 py-2 sm:px-4 sm:py-3 border-b border-gray-100 hover:bg-gray-50">
                                                <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                                                    <!-- Icono de boleto -->
                                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                                        </svg>
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item['ticket_type_name'] ?? 'Boleto' }}</p>
                                                        <p class="text-xs text-gray-500 truncate">{{ $item['event_name'] ?? 'Evento' }}</p>
                                                        @if(isset($item['event_date']))
                                                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($item['event_date'])->format('d M Y') }}</p>
                                                        @endif
                                                        <!-- Mostrar disponibilidad -->
                                                        @php
                                                            $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $item['ticket_type_id'])
                                                                ->where('event_id', $item['event_id'])
                                                                ->first();
                                                            $reservedQuantity = \App\Models\TicketReservation::where('ticket_types_id', $item['ticket_type_id'])
                                                                ->where('event_id', $item['event_id'])
                                                                ->where('reserved_until', '>', now())
                                                                ->where('is_active', true)
                                                                ->where('session_id', '!=', session()->getId())
                                                                ->sum('quantity');
                                                            $available = $ticketEvent ? ($ticketEvent->quantity - $reservedQuantity) : 0;
                                                        @endphp
                                                        <p class="text-xs text-green-600 font-medium">
                                                            Disponibles: {{ $available }} boletos
                                                        </p>
                                                    </div>

                                                    <div class="text-right flex-shrink-0">
                                                        <p class="text-xs sm:text-sm font-medium text-gray-900">{{ $item['quantity'] }}x</p>
                                                        <p class="text-xs sm:text-sm text-gray-500">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                @if(!empty($cart))
                                    <!-- Footer with Total and Actions -->
                                    <div class="px-3 py-2 sm:px-4 sm:py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg flex-shrink-0">
                                        <div class="flex justify-between items-center mb-2 sm:mb-3">
                                            <span class="text-xs sm:text-sm font-medium text-gray-900">Total (IVA incluido):</span>
                                            <span class="text-base sm:text-lg font-bold text-gray-900">${{ number_format($cartTotal, 2) }}</span>
                                        </div>

                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <a href="{{ \App\Helpers\CartHelper::getCartViewRoute() }}"
                                               class="flex-1 bg-gray-600 text-white text-center px-3 py-2 rounded-md text-xs sm:text-sm hover:bg-gray-700 transition-colors">
                                                Ver Carrito
                                            </a>
                                            <a href="{{ \App\Helpers\CartHelper::getCheckoutRoute() }}"
                                               class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-md text-xs sm:text-sm hover:bg-blue-700 transition-colors">
                                                Comprar
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Navigation Links -->
                        <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}" class="text-gray-700 hover:text-gray-900 text-sm lg:text-base">
                            Inicio
                        </a>
                        <a href="{{ config('app.url') }}" class="text-gray-700 hover:text-gray-900 text-sm lg:text-base">
                            Todos los Eventos
                        </a>

                        @auth
                            @if(isset($space))
                                @php
                                    $user = auth()->user();
                                    // Verificar si es admin del space (role_space_id = 1)
                                    $isAdmin = $user->spaces()
                                        ->where('spaces.id', $space->id)
                                        ->wherePivot('role_space_id', 1)
                                        ->wherePivotNull('deleted_at')
                                        ->exists();
                                    
                                    // Verificar permisos adicionales
                                    $hasPermission = \App\Models\RoleSpacePermission::hasPermission($space->id, 'create checkins');
                                    
                                    $canSeeScanner = $isAdmin || $hasPermission;
                                @endphp
                                @if($canSeeScanner)
                                    <a href="{{ route('scanner.index', ['subdomain' => $space->subdomain]) }}" class="text-gray-700 hover:text-gray-900 flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                        Scanner
                                    </a>
                                @endif
                                @if($isAdmin)
                                    <a href="{{ route('spaces.coupons.index', $space->subdomain) }}" class="text-gray-700 hover:text-gray-900 flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        Cupones
                                    </a>
                                @endif
                            @endif
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

                                    <!-- Menu Items -->
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

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 bg-white">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="{{ route('spaces.profile', $space->subdomain ?? '') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            Inicio
                        </a>
                        <a href="{{ config('app.url') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                            Todos los Eventos
                        </a>
                        @auth
                            @if(isset($space))
                                @php
                                    $user = auth()->user();
                                    $isAdmin = $user->spaces()
                                        ->where('spaces.id', $space->id)
                                        ->wherePivot('role_space_id', 1)
                                        ->wherePivotNull('deleted_at')
                                        ->exists();
                                    $hasPermission = \App\Models\RoleSpacePermission::hasPermission($space->id, 'create checkins');
                                    $canSeeScanner = $isAdmin || $hasPermission;
                                @endphp
                                @if($canSeeScanner)
                                    <a href="{{ route('scanner.index', ['subdomain' => $space->subdomain]) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                        Scanner
                                    </a>
                                @endif
                                @if($isAdmin)
                                    <a href="{{ route('spaces.coupons.index', $space->subdomain) }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                        Cupones
                                    </a>
                                @endif
                            @endif
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mi Cuenta</p>
                                <a href="{{ config('app.url') }}/profile" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                    Perfil
                                </a>
                                <a href="{{ config('app.url') }}/my-tickets" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                    Mis Boletos
                                </a>
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
