@extends('layouts.app')

@section('title', 'Sesión expirada')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-yellow-50 flex items-center justify-center px-4 py-12">
        <!-- Decorative elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-amber-200/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-yellow-200/30 rounded-full blur-3xl"></div>
        </div>

        <div class="relative text-center max-w-2xl mx-auto">
            <!-- Error Code with gradient -->
            <div class="relative mb-8">
                <h1
                    class="text-[180px] md:text-[220px] font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-500 leading-none select-none">
                    419
                </h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 md:w-40 md:h-40 text-amber-500/20" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8 md:p-12 border border-amber-100">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                    Sesión Expirada
                </h2>
                <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
                    Tu sesión ha expirado por inactividad. Por favor, recarga la página e inténtalo de nuevo.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-yellow-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Recargar página
                    </button>
                    <a href="{{ config('app.url') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-amber-300 hover:bg-amber-50 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Ir al inicio
                    </a>
                </div>
            </div>

            <!-- Info -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-sm">
                <span class="text-gray-500 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Por seguridad, las sesiones expiran después de un tiempo de inactividad
                </span>
            </div>
        </div>
    </div>
@endsection