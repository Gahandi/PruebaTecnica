@extends('layouts.app')

@section('title', 'Error del servidor')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-gray-100 flex items-center justify-center px-4 py-12">
        <!-- Decorative elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-slate-200/40 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gray-200/40 rounded-full blur-3xl"></div>
        </div>

        <div class="relative text-center max-w-2xl mx-auto">
            <!-- Error Code with gradient -->
            <div class="relative mb-8">
                <h1
                    class="text-[180px] md:text-[220px] font-black text-transparent bg-clip-text bg-gradient-to-r from-slate-600 via-gray-500 to-slate-600 leading-none select-none">
                    500
                </h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 md:w-40 md:h-40 text-slate-400/30" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8 md:p-12 border border-slate-200">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                    ¡Algo salió mal!
                </h2>
                <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
                    Estamos experimentando problemas técnicos. Nuestro equipo ya fue notificado. Por favor, intenta de nuevo
                    más tarde.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-slate-600 to-gray-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Intentar de nuevo
                    </button>
                    <a href="{{ config('app.url') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-slate-400 hover:bg-slate-50 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Ir al inicio
                    </a>
                </div>
            </div>

            <!-- Status indicator -->
            <div class="mt-8 flex flex-col items-center gap-4">
                <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-full shadow-md border border-gray-100">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                    </span>
                    <span class="text-sm text-gray-600 font-medium">Estamos trabajando en ello</span>
                </div>
                <p class="text-xs text-gray-400">Si el problema persiste, contacta a soporte técnico</p>
            </div>
        </div>
    </div>
@endsection