@extends('layouts.app')

@section('title', 'Acceso denegado')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-red-50 via-white to-orange-50 flex items-center justify-center px-4 py-12">
        <!-- Decorative elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-red-200/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-orange-200/30 rounded-full blur-3xl"></div>
        </div>

        <div class="relative text-center max-w-2xl mx-auto">
            <!-- Error Code with gradient -->
            <div class="relative mb-8">
                <h1
                    class="text-[180px] md:text-[220px] font-black text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-orange-500 to-red-500 leading-none select-none">
                    403
                </h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 md:w-40 md:h-40 text-red-500/20" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8 md:p-12 border border-red-100">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                    Acceso Denegado
                </h2>
                <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
                    No tienes permiso para acceder a esta página. Si crees que esto es un error, contacta al administrador.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ config('app.url') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Ir al inicio
                    </a>
                    <button onclick="history.back()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-red-300 hover:bg-red-50 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver atrás
                    </button>
                </div>
            </div>

            <!-- Contact info -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm">
                <span class="text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    ¿Necesitas ayuda? Contacta al soporte
                </span>
                @auth
                    <a href="{{ route('profile') }}"
                        class="text-gray-500 hover:text-red-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Mi perfil
                    </a>
                @else
                    <a href="{{ config('app.url') }}/login"
                        class="text-gray-500 hover:text-red-600 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Iniciar sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>
@endsection