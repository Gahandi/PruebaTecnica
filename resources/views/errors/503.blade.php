@extends('layouts.app')

@section('title', 'Servicio no disponible')

@section('content')
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center px-4 py-12">
        <!-- Decorative elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-200/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-200/30 rounded-full blur-3xl"></div>
        </div>

        <div class="relative text-center max-w-2xl mx-auto">
            <!-- Error Code with gradient -->
            <div class="relative mb-8">
                <h1
                    class="text-[180px] md:text-[220px] font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-500 via-indigo-500 to-blue-500 leading-none select-none">
                    503
                </h1>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-32 h-32 md:w-40 md:h-40 text-blue-500/20" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-8 md:p-12 border border-blue-100">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                    En Mantenimiento
                </h2>
                <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
                    Estamos realizando mejoras en nuestro sistema. El servicio estará disponible en breve.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button onclick="window.location.reload()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Verificar estado
                    </button>
                </div>
            </div>

            <!-- Status indicator -->
            <div class="mt-8 flex flex-col items-center gap-4">
                <div class="flex items-center gap-3 bg-white px-6 py-3 rounded-full shadow-md border border-blue-100">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
                    </span>
                    <span class="text-sm text-gray-600 font-medium">Mantenimiento en progreso</span>
                </div>
                <p class="text-xs text-gray-400">Gracias por tu paciencia</p>
            </div>
        </div>
    </div>
@endsection