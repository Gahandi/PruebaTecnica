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

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b sticky top-0 left-0 right-0 z-50 border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center flex-row-reverse gap-6">
                                                    <!-- Admin Menu -->
                                                    @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('staff')))
                            <div class="relative group" id="admin-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center" onclick="toggleAdminDropdown()">
                                    Administración
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="admin-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-52 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200" id="admin-menu" style="display: none;">
                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors border-b border-gray-100">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Dashboard
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.events.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Eventos
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.ticket-types.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                                </svg>
                                                Tipos de Boletos
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.coupons.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                                Cupones
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                Órdenes
                                            </div>
                                        </a>
                                    @endif

                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.checkins.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Check-ins (Admin)
                                        </div>
                                    </a>
                                    @endif

                                    @if(auth()->check() && auth()->user()->hasRole('staff'))
                                    <a href="{{ route('staff.checkins.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Check-ins (Staff)
                                        </div>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        <a href="{{ route('events.public') }}" class="text-xl font-bold text-gray-900">
                            {{ config('app.name', 'Boletos') }}
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Cart Dropdown -->
                        <div class="relative group" id="cart-dropdown">
                            <button class="text-gray-700 hover:text-gray-900 relative p-2 rounded-lg hover:bg-gray-100 transition-colors group" onclick="toggleCartDropdown()">
                            <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                            </svg>

                            @if(\App\Helpers\CartHelper::getCartCount() > 0)
                                <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center
                                            bg-red-500 text-white text-xs font-bold
                                            min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg
                                            animate-pulse">
                                    {{ \App\Helpers\CartHelper::getCartCount() }}
                                </span>
                            @endif
                            </button>

                            <!-- Cart Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-xl z-50 border border-gray-200" id="cart-menu" style="display: none;">
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Carrito de Compras</h3>
                                            <p class="text-sm text-gray-500">{{ \App\Helpers\CartHelper::getCartCount() }} item(s)</p>
                                        </div>
                                        <button onclick="closeCartDropdown()" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Cart Items with Scroll -->
                                <div class="max-h-80 overflow-y-auto">
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
                                            <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                                                <div class="flex items-center space-x-3">
                                                    <!-- Icono de boleto -->
                                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                                        </svg>
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900">{{ $item['ticket_type_name'] ?? 'Boleto' }}</p>
                                                        <p class="text-xs text-gray-500">{{ $item['event_name'] ?? 'Evento' }}</p>
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
                                                        <p class="text-sm font-medium text-gray-900">{{ $item['quantity'] }}x</p>
                                                        <p class="text-sm text-gray-500">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                @if(!empty($cart))
                                    <!-- Footer with Total and Actions -->
                                    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-sm font-medium text-gray-900">Total:</span>
                                            <span class="text-lg font-bold text-gray-900">${{ number_format(\App\Helpers\CartHelper::getCartTotal(), 2) }}</span>
                                        </div>

                                        <div class="flex space-x-2">
                                            <a href="{{ \App\Helpers\CartHelper::getCartViewRoute() }}"
                                               class="flex-1 bg-gray-600 text-white text-center px-3 py-2 rounded-md text-sm hover:bg-gray-700 transition-colors">
                                                Ver Carrito
                                            </a>
                                            <a href="{{ \App\Helpers\CartHelper::getCheckoutRoute() }}"
                                               class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                                                Comprar
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <a href="{{ config('app.url') }}/" class="text-gray-700 hover:text-gray-900">
                            Eventos
                        </a>
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

                                    <!-- Menu Items -->
                                    <a href="{{ route('scanner.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <img class="w-5 h-4 mr-2" src="https://img.icons8.com/parakeet-line/48/portrait-mode-scanning.png" alt="portrait-mode-scanning"/>
                                            Scanner
                                        </div>
                                    </a>

                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Perfil
                                        </div>
                                    </a>

                                    <a href="{{ route('tickets.my') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            Mis Boletos
                                        </div>
                                    </a>


                                    <!-- Logout -->
                                    <form method="POST" action="{{ route('logout') }}" class="block">
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
                            <a href="{{ config('app.url') }}/login" class="text-gray-700 hover:text-gray-900">
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

        function toggleCartDropdown() {
            const menu = document.getElementById('cart-menu');

            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(event) {
            const cartDropdown = document.getElementById('cart-dropdown');
            const cartMenu = document.getElementById('cart-menu');

            if (cartDropdown && cartMenu && !cartDropdown.contains(event.target)) {
                cartMenu.style.display = 'none';
            }
        });

        // Función para cerrar el dropdown del carrito
        function closeCartDropdown() {
            const cartMenu = document.getElementById('cart-menu');
            if (cartMenu) {
                cartMenu.style.display = 'none';
            }
        }

        // Cerrar dropdown cuando se hace clic en los enlaces del carrito
        document.addEventListener('click', function(event) {
            if (event.target.closest('a[href*="cart"]') || event.target.closest('a[href*="checkout"]')) {
                closeCartDropdown();
            }
        });

        // Función para mostrar notificación de item agregado al carrito
        function showCartNotification() {
            const cartButton = document.querySelector('#cart-dropdown button');
            const cartCount = document.querySelector('#cart-dropdown .bg-red-500');

            if (cartButton) {
                // Agregar clase de animación
                cartButton.classList.add('animate-bounce');

                // Remover la animación después de 1 segundo
                setTimeout(() => {
                    cartButton.classList.remove('animate-bounce');
                }, 1000);
            }

            if (cartCount) {
                // Animación del contador
                cartCount.classList.add('animate-pulse');
                setTimeout(() => {
                    cartCount.classList.remove('animate-pulse');
                }, 1000);
            }
        }

        // Escuchar eventos de agregar al carrito
        document.addEventListener('cartUpdated', function() {
            showCartNotification();
            // Actualizar el contador del carrito
            updateCartCount();
        });

        // Función para actualizar el contador del carrito
        function updateCartCount() {
            // Hacer una petición AJAX para obtener el nuevo conteo
            fetch('{{ route("cart.count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Actualizar el contador visual
                const cartCount = document.querySelector('#cart-dropdown .bg-red-500');
                if (cartCount) {
                    cartCount.textContent = data.count;
                    if (data.count > 0) {
                        cartCount.style.display = 'inline-flex';
                    } else {
                        cartCount.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
        }

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
</body>
</html>
