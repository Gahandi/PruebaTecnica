@extends('layouts.app')

@section('title', $title ?? 'Admin Panel')

@section('content')
    <div class="flex bg-gray-100 min-h-screen relative">
        {{-- Mobile Menu Button --}}
        <button onclick="toggleAdminSidebar()"
            class="md:hidden fixed bottom-4 right-4 z-50 bg-gradient-to-r from-pink-500 to-purple-600 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all">
            <svg id="admin-menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg id="admin-close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        {{-- Mobile Backdrop --}}
        <div id="admin-sidebar-backdrop" onclick="toggleAdminSidebar()"
            class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

        {{-- Sidebar --}}
        <aside id="admin-sidebar"
            class="fixed md:sticky top-0 left-0 h-screen w-72 md:w-64 bg-gradient-to-b from-gray-100 to-gray-200 text-white flex-shrink-0 flex-col overflow-hidden z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:flex">
            {{-- Logo/Brand --}}
            <div class="p-4 md:p-6 border-b border-gray-300 flex items-center justify-between">
                <div>
                    <h1
                        class="text-xl md:text-2xl font-bold bg-gradient-to-r from-pink-400 to-purple-400 bg-clip-text text-transparent">
                        Admin Global
                    </h1>
                    <p class="text-xs text-gray-600 mt-1">{{ auth()->user()->name }}</p>
                </div>
                <button onclick="toggleAdminSidebar()" class="md:hidden text-gray-600 hover:text-gray-900 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto py-4">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Tablero
                </a>

                {{-- Divider --}}
                <div class="px-6 py-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestión</p>
                </div>

                {{-- Users --}}
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Usuarios
                </a>

                {{-- Espacios (includes events) --}}
                <a href="{{ route('admin.spaces.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.spaces.*') || request()->routeIs('admin.events.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Espacios
                </a>

                {{-- Check-ins --}}
                <a href="{{ route('admin.checkins.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.checkins.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Check-ins
                </a>

                {{-- Cupones --}}
                <a href="{{ route('admin.coupons.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.coupons.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Cupones
                </a>

                {{-- Divider --}}
                <div class="px-6 py-2 mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Reportes</p>
                </div>

                {{-- Activity Log --}}
                <a href="{{ route('admin.activity-log.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.activity-log.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Activity Log
                </a>

                {{-- Statistics --}}
                <a href="{{ route('admin.checkins.stats') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.checkins.stats') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Estadísticas
                </a>

                {{-- Reports --}}
                <a href="{{ route('admin.reports.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Reportes
                </a>

                {{-- Divider --}}
                <div class="px-6 py-2 mt-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sistema</p>
                </div>

                {{-- Settings --}}
                <a href="{{ route('admin.settings.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Configuración
                </a>

                {{-- Event Types --}}
                <a href="{{ route('admin.type_events.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.type_events.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Tipos de Evento
                </a>

                {{-- Ticket Types --}}
                <a href="{{ route('admin.ticket_types.index') }}"
                    class="flex items-center px-6 py-3 text-black hover:bg-gray-300 transition-colors {{ request()->routeIs('admin.ticket_types.*') ? 'bg-gray-200 text-black border-l-4 border-pink-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                    Tipos de Boleto
                </a>
            </nav>

            {{-- User Profile Footer --}}
            <div class="p-4 border-t border-gray-300 flex-shrink-0 bg-gray-200">
                <div class="flex items-center">
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-400 to-purple-500 flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-black">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-800 transition-colors"
                            title="Cerrar sesión">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden md:ml-0">
            {{-- Content Area --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 pb-20 md:pb-0">
                @yield('admin-content')
            </main>
        </div>
    </div>

    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const menuIcon = document.getElementById('admin-menu-icon');
            const closeIcon = document.getElementById('admin-close-icon');

            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }
    </script>
@endsection