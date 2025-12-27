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
        [x-cloak] {
            display: none !important;
        }
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
                    <button id="mobile-menu-button"
                        class="md:hidden p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100"
                        onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Logo/Brand -->
                    <div class="flex items-center flex-1 md:flex-none justify-center md:justify-start">
                        <a href="{{ config('app.url') }}/" class="text-lg sm:text-xl font-bold text-gray-900">
                            <img src="{{ asset('images/logo/Logo_merrycolor.png') }}" alt="Logo Merrycolor"
                                class="h-12 w-auto">
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center gap-4 lg:gap-6">
                        <!-- Admin Menu -->
                        @php
                            $user = auth()->user();
                            $isAdmin = $user && ($user->role === 'admin');
                            $isStaff = $user && ($user->role === 'staff');
                            $isVerified = $user && ($user->verified_at || $user->verified);
                        @endphp
                        @if(auth()->check() && $isVerified && ($isAdmin || $isStaff))
                            <div class="relative group" id="admin-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center text-sm lg:text-base"
                                    onclick="toggleAdminDropdown()">
                                    <span class="hidden lg:inline">Administración</span>
                                    <span class="lg:hidden">Admin</span>
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="admin-arrow" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-52 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                                    id="admin-menu" style="display: none;">
                                    @if($isAdmin)
                                        <a href="{{ config('app.url') }}/dashboard"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                    </path>
                                                </svg>
                                                Tablero
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
                            <button
                                class="text-gray-700 hover:text-gray-900 relative p-2 rounded-lg hover:bg-gray-100 transition-colors group"
                                onclick="toggleCartDropdown()">
                                <svg class="w-5 h-5 lg:w-6 lg:h-6 transition-transform group-hover:scale-110"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    $taxes = $subtotal * 0.16; // 16% IVA
                                    $cartTotal = $subtotal + $taxes; // Total con IVA
                                @endphp
                                @include('partials.cart-dropdown', ['cart' => $cart, 'cartCount' => $cartCount, 'cartTotal' => $cartTotal])
                            </div>
                        </div>
                        @auth
                            @php
                                $currentUser = auth()->user();
                                $isUserVerified = $currentUser && ($currentUser->verified_at || $currentUser->verified);
                            @endphp

                            @if($isUserVerified)
                                <!-- Spaces Menu Dropdown -->
                                <div class="relative group" id="spaces-dropdown">
                                    <button class="text-gray-700 hover:text-gray-900 flex items-center"
                                        onclick="toggleSpacesDropdown()">
                                        <svg class="w-6 h-6 transition-transform duration-200 hover:scale-110" id="spaces-icon"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <span class="ml-1">Espacios</span>
                                        <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="spaces-arrow"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100 max-h-96 overflow-y-auto"
                                        id="spaces-menu" style="display: none;">
                                        @php
                                            // Use pivot data directly (already loaded) - no extra queries
                                            $mySpacesNav = $currentUser->spaces->filter(fn($s) => $s->pivot->role_space_id == 1);
                                            $staffSpacesNav = $currentUser->spaces->filter(fn($s) => $s->pivot->role_space_id == 2);
                                            $followingSpacesNav = $currentUser->spaces->filter(fn($s) => $s->pivot->role_space_id == 3);
                                        @endphp

                                        @if($mySpacesNav->count() > 0)
                                            <div class="mb-2">
                                                <p
                                                    class="px-4 py-2 text-[10px] font-bold text-pink-600 uppercase tracking-wider bg-gradient-to-r from-pink-50 to-pink-100 border-l-3 border-pink-500 flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                                        </path>
                                                    </svg>
                                                    Mi Espacio
                                                </p>
                                                @foreach($mySpacesNav as $space)
                                                    <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}"
                                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-pink-50 transition-colors mx-1 rounded-lg">
                                                        <span class="w-2 h-2 rounded-full bg-pink-500 mr-2.5 flex-shrink-0"></span>
                                                        <div class="min-w-0">
                                                            <div class="font-medium truncate">{{ $space->name }}</div>
                                                            <div class="text-xs text-gray-400 truncate">
                                                                {{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($staffSpacesNav->count() > 0)
                                            <div class="mb-2">
                                                <p
                                                    class="px-4 py-2 text-[10px] font-bold text-blue-600 uppercase tracking-wider bg-gradient-to-r from-blue-50 to-blue-100 border-l-3 border-blue-500 flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                                            clip-rule="evenodd"></path>
                                                        <path
                                                            d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z">
                                                        </path>
                                                    </svg>
                                                    Donde Soy Staff
                                                </p>
                                                @foreach($staffSpacesNav as $space)
                                                    <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}"
                                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 transition-colors mx-1 rounded-lg">
                                                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2.5 flex-shrink-0"></span>
                                                        <div class="min-w-0">
                                                            <div class="font-medium truncate">{{ $space->name }}</div>
                                                            <div class="text-xs text-gray-400 truncate">
                                                                {{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($followingSpacesNav->count() > 0)
                                            <div class="mb-2">
                                                <p
                                                    class="px-4 py-2 text-[10px] font-bold text-purple-600 uppercase tracking-wider bg-gradient-to-r from-purple-50 to-purple-100 border-l-3 border-purple-500 flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                        </path>
                                                    </svg>
                                                    Espacios que Sigo
                                                </p>
                                                @foreach($followingSpacesNav as $space)
                                                    <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}"
                                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-purple-50 transition-colors mx-1 rounded-lg">
                                                        <span class="w-2 h-2 rounded-full bg-purple-500 mr-2.5 flex-shrink-0"></span>
                                                        <div class="min-w-0">
                                                            <div class="font-medium truncate">{{ $space->name }}</div>
                                                            <div class="text-xs text-gray-400 truncate">
                                                                {{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="border-t border-gray-100 mx-2 my-2"></div>

                                        <a href="{{ route('user.spaces.index') }}"
                                            class="flex items-center px-4 py-2.5 text-sm font-medium text-pink-600 hover:bg-pink-50 transition-colors mx-1 rounded-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Gestionar Mis Espacios
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <!-- User Menu Dropdown -->
                            <div class="relative group" id="user-dropdown">
                                <button class="text-gray-700 hover:text-gray-900 flex items-center"
                                    onclick="toggleUserDropdown()">
                                    <svg class="w-6 h-6 transition-transform duration-200 hover:scale-110" id="user-icon"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <svg class="w-4 h-4 ml-1 transition-transform duration-200" id="user-arrow" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200"
                                    id="user-menu" style="display: none;">
                                    <!-- User Info -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ $currentUser->name }}
                                            {{ $currentUser->last_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $currentUser->email }}</p>
                                    </div>

                                    @if($isUserVerified)
                                        <a href="{{ config('app.url') }}/profile"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                Perfil
                                            </div>
                                        </a>

                                        <a href="{{ config('app.url') }}/my-tickets"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                                    </path>
                                                </svg>
                                                Mis Boletos
                                            </div>
                                        </a>
                                    @endif

                                    <!-- Logout -->
                                    <form method="POST" action="{{ config('app.url') }}/logout" class="block">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                    </path>
                                                </svg>
                                                Cerrar sesión
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ config('app.url') }}/login"
                                class="text-gray-700 hover:text-gray-900 text-sm lg:text-base">
                                Iniciar sesión
                            </a>
                        @endauth
                    </div>

                    {{-- Mobile Cart Button with Dropdown (visible on mobile, right side of navbar) --}}
                    <div class="md:hidden relative" id="mobile-cart-dropdown">
                        <button
                            class="text-gray-700 hover:text-gray-900 relative p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            onclick="toggleMobileCartDropdown()">
                            <svg class="w-6 h-6 transition-transform hover:scale-110" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1 7a2 2 0 01-2 2H8a2 2 0 01-2-2L5 9z"></path>
                            </svg>
                            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center
                                            bg-red-500 text-white text-xs font-bold
                                            min-w-[18px] h-[18px] px-1 rounded-full border-2 border-white shadow-lg"
                                id="mobile-cart-count" style="display: none;">
                                0
                            </span>
                        </button>

                        {{-- Mobile Cart Dropdown Menu --}}
                        <div class="fixed left-0 right-0 top-16 w-full bg-white shadow-xl z-50 border-t border-gray-200 max-h-[calc(100vh-4rem)] overflow-hidden flex flex-col"
                            id="mobile-cart-menu" style="display: none;">
                            @php
                                $mobileCart = \App\Helpers\CartHelper::getCartWithEventInfo();
                                $mobileCartCount = \App\Helpers\CartHelper::getCartCount();
                                $mobileSubtotal = \App\Helpers\CartHelper::getCartTotal();
                                $mobileTaxes = $mobileSubtotal * 0.16;
                                $mobileCartTotal = $mobileSubtotal + $mobileTaxes;
                            @endphp
                            @include('partials.cart-dropdown', ['cart' => $mobileCart, 'cartCount' => $mobileCartCount, 'cartTotal' => $mobileCartTotal])
                        </div>
                    </div>

                    <!-- Mobile Menu -->
                    <div id="mobile-menu" class="hidden md:hidden absolute left-0 right-0 top-16 bg-white border-t border-gray-200 shadow-lg z-40 max-h-[calc(100vh-4rem)] overflow-y-auto">
                        <div class="px-2 pt-2 pb-3 space-y-1">
                            <a href="{{ config('app.url') }}/"
                                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                Eventos
                            </a>
                            @php
                                $mobileUser = auth()->user();
                                $mobileIsAdmin = $mobileUser && ($mobileUser->role === 'admin');
                                $mobileIsStaff = $mobileUser && ($mobileUser->role === 'staff');
                                $mobileIsVerified = $mobileUser && ($mobileUser->verified_at || $mobileUser->verified);
                            @endphp
                            @if(auth()->check() && $mobileIsVerified && ($mobileIsAdmin || $mobileIsStaff))
                                <div class="border-t border-gray-200 pt-2 mt-2">
                                    <p class="px-3 py-2 text-xs font-semibold text-pink-600 uppercase tracking-wider bg-pink-50 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Panel de Administración
                                    </p>
                                    
                                    {{-- Tablero --}}
                                    <a href="{{ route('dashboard') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('dashboard') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        Tablero
                                    </a>
                                    
                                    {{-- Usuarios --}}
                                    <a href="{{ route('admin.users.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.users.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Usuarios
                                    </a>
                                    
                                    {{-- Espacios --}}
                                    <a href="{{ route('admin.spaces.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.spaces.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h6v6H4V6zm10 0h6v6h-6V6zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"></path>
                                        </svg>
                                        Espacios
                                    </a>
                                    
                                    {{-- Eventos --}}
                                    <a href="{{ route('admin.events.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.events.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Eventos
                                    </a>
                                    
                                    {{-- Check-ins --}}
                                    <a href="{{ route('admin.checkins.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.checkins.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Check-ins
                                    </a>
                                    
                                    {{-- Activity Log --}}
                                    <a href="{{ route('admin.activity-log.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.activity-log.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Activity Log
                                    </a>
                                    
                                    {{-- Estadísticas --}}
                                    <a href="{{ route('admin.checkins.stats') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.checkins.stats') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Estadísticas
                                    </a>
                                    
                                    {{-- Reportes --}}
                                    <a href="{{ route('admin.reports.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.reports.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Reportes
                                    </a>
                                    
                                    {{-- Configuración --}}
                                    <a href="{{ route('admin.settings.index') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50 {{ request()->routeIs('admin.settings.*') ? 'bg-pink-50 text-pink-600 border-l-2 border-pink-500' : '' }}">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Configuración
                                    </a>
                                </div>
                            @endif
                            @auth
                                @if($mobileIsVerified)
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mi
                                            Cuenta</p>
                                        <a href="{{ config('app.url') }}/profile"
                                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                            Perfil
                                        </a>
                                        <a href="{{ config('app.url') }}/my-tickets"
                                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
                                            Mis Boletos
                                        </a>
                                        @if($mobileUser->spaces->count() > 0)
                                            @php
                                                // Filtrar espacios por rol para el móvil
                                                $mobileMySpaces = $mobileUser->spaces->filter(fn($s) => $s->pivot->role_space_id == 1);
                                                $mobileStaffSpaces = $mobileUser->spaces->filter(fn($s) => $s->pivot->role_space_id == 2);
                                                $mobileFollowingSpaces = $mobileUser->spaces->filter(fn($s) => $s->pivot->role_space_id == 3);
                                            @endphp
                                            <div class="border-t border-gray-200 mt-2 pt-2">
                                                <p
                                                    class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-pink-500" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                    Mis Espacios
                                                </p>

                                                @if($mobileMySpaces->count() > 0)
                                                    <div class="mb-2">
                                                        <p
                                                            class="px-4 py-1.5 text-[10px] font-semibold text-pink-600 uppercase tracking-wider bg-pink-50 border-l-2 border-pink-400">
                                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                                                </path>
                                                            </svg>
                                                            Mi Espacio
                                                        </p>
                                                        @foreach($mobileMySpaces as $space)
                                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}"
                                                                class="flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-pink-50">
                                                                <span class="w-2 h-2 rounded-full bg-pink-500 mr-2"></span>
                                                                <div>
                                                                    <span class="font-medium">{{ $space->name }}</span>
                                                                    <span
                                                                        class="text-xs text-gray-400 block">{{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($mobileStaffSpaces->count() > 0)
                                                    <div class="mb-2">
                                                        <p
                                                            class="px-4 py-1.5 text-[10px] font-semibold text-blue-600 uppercase tracking-wider bg-blue-50 border-l-2 border-blue-400">
                                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                                                    clip-rule="evenodd"></path>
                                                                <path
                                                                    d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z">
                                                                </path>
                                                            </svg>
                                                            Donde Soy Staff
                                                        </p>
                                                        @foreach($mobileStaffSpaces as $space)
                                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}"
                                                                class="flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-blue-50">
                                                                <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>
                                                                <div>
                                                                    <span class="font-medium">{{ $space->name }}</span>
                                                                    <span
                                                                        class="text-xs text-gray-400 block">{{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($mobileFollowingSpaces->count() > 0)
                                                    <div class="mb-2">
                                                        <p
                                                            class="px-4 py-1.5 text-[10px] font-semibold text-purple-600 uppercase tracking-wider bg-purple-50 border-l-2 border-purple-400">
                                                            <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                                </path>
                                                            </svg>
                                                            Espacios que Sigo
                                                        </p>
                                                        @foreach($mobileFollowingSpaces as $space)
                                                            <a href="{{ \App\Helpers\SubdomainHelper::getSubdomainUrl($space->subdomain) }}"
                                                                class="flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-purple-50">
                                                                <span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span>
                                                                <div>
                                                                    <span class="font-medium">{{ $space->name }}</span>
                                                                    <span
                                                                        class="text-xs text-gray-400 block">{{ $space->subdomain }}.{{ \App\Helpers\SubdomainHelper::getBaseDomain() }}</span>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <a href="{{ route('user.spaces.index') }}"
                                                    class="flex items-center px-4 py-2 rounded-md text-sm font-medium text-pink-600 hover:text-pink-700 hover:bg-pink-50 mt-1">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Gestionar Mis Espacios
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Mobile Cart -->
                                <form method="POST" action="{{ config('app.url') }}/logout"
                                    class="border-t border-gray-200 pt-2 mt-2">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:text-red-900 hover:bg-red-50">
                                        Cerrar sesión
                                    </button>
                                </form>
                            @else
                                <a href="{{ config('app.url') }}/login"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">
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
            const mobileCartMenu = document.getElementById('mobile-cart-menu');
            
            // Cerrar carrito móvil si está abierto
            if (mobileCartMenu && mobileCartMenu.style.display !== 'none') {
                mobileCartMenu.style.display = 'none';
            }
            
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

        // Función para el dropdown del carrito móvil
        function toggleMobileCartDropdown() {
            const mobileCartMenu = document.getElementById('mobile-cart-menu');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (!mobileCartMenu) {
                console.error('Mobile cart menu not found');
                return;
            }
            
            // Cerrar menú móvil si está abierto
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
            }
            
            if (mobileCartMenu.style.display === 'none' || mobileCartMenu.style.display === '') {
                mobileCartMenu.style.display = 'block';
                // Actualizar contenido del carrito
                if (typeof window.updateCartDropdown === 'function') {
                    window.updateCartDropdown();
                } else if (typeof window.renderCartDropdown === 'function') {
                    window.renderCartDropdown();
                }
            } else {
                mobileCartMenu.style.display = 'none';
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

        function goToCart() {
            window.location.href = '{{ config('app.url') }}/cart';
        }

        function closeCartDropdown() {
            // Close desktop cart menu
            const cartMenu = document.getElementById('cart-menu');
            if (cartMenu) {
                cartMenu.style.display = 'none';
            }
            // Close mobile cart menu
            const mobileCartMenu = document.getElementById('mobile-cart-menu');
            if (mobileCartMenu) {
                mobileCartMenu.style.display = 'none';
            }
        }
        // Hacer funciones disponibles globalmente
        window.toggleCartDropdown = toggleCartDropdown;
        window.closeCartDropdown = closeCartDropdown;
        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function (event) {
            // Desktop cart
            const cartDropdown = document.getElementById('cart-dropdown');
            const cartMenu = document.getElementById('cart-menu');
            if (cartDropdown && cartMenu && !cartDropdown.contains(event.target)) {
                cartMenu.style.display = 'none';
            }
            // Mobile cart
            const mobileCartDropdown = document.getElementById('mobile-cart-dropdown');
            const mobileCartMenu = document.getElementById('mobile-cart-menu');
            if (mobileCartDropdown && mobileCartMenu && !mobileCartDropdown.contains(event.target)) {
                mobileCartMenu.style.display = 'none';
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