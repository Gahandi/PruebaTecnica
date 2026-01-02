@extends('layouts.app')

@section('title', 'Mi Perfil')

@push('styles')
<style>
    .profile-gradient {
        background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #fbcfe8 100%);
    }
    .card-light {
        background: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(226, 73, 114, 0.1);
    }
    .input-light {
        background: #ffffff;
        border: 2px solid #d1d5db;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    .input-light:focus {
        background: white;
        border-color: #e24972;
        box-shadow: 0 0 0 3px rgba(226, 73, 114, 0.15), inset 0 1px 2px rgba(0, 0, 0, 0.05);
        outline: none;
    }
    .input-light:disabled, .input-light[readonly] {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #6b7280;
        cursor: not-allowed;
    }
    .btn-primary-glow {
        background: linear-gradient(135deg, #e24972 0%, #d63384 100%);
        box-shadow: 0 4px 15px rgba(226, 73, 114, 0.4);
        transition: all 0.3s ease;
    }
    .btn-primary-glow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(226, 73, 114, 0.5);
    }
    .btn-secondary {
        background: #f3f4f6;
        border: 2px solid #d1d5db;
        color: #374151;
        transition: all 0.3s ease;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        transition: all 0.3s ease;
    }
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
    }
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        transition: all 0.3s ease;
    }
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.5);
    }
    .stat-card {
        background: white;
        border: 2px solid rgba(226, 73, 114, 0.2);
        box-shadow: 0 2px 8px rgba(226, 73, 114, 0.1);
    }
    .avatar-ring {
        background: linear-gradient(135deg, #e24972, #d63384, #e24972);
        padding: 3px;
    }
    .space-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .space-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(226, 73, 114, 0.15);
    }
    .animate-pulse-slow {
        animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    /* File input styling */
    input[type="file"] {
        background: white;
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 0.5rem;
    }
    input[type="file"]:hover {
        border-color: #e24972;
    }
    input[type="file"]:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen profile-gradient">
    <!-- Decorative Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-pink-300/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute top-1/2 -left-40 w-96 h-96 bg-purple-300/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s"></div>
        <div class="absolute -bottom-40 right-1/3 w-72 h-72 bg-pink-400/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header Section -->
        <div class="mb-12">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div class="flex items-center gap-6">
                    <!-- Avatar with animated ring -->
                    <div class="avatar-ring rounded-full">
                        @if(auth()->user()->image)
                            <img class="h-24 w-24 lg:h-32 lg:w-32 object-cover rounded-full" 
                                 src="{{ \App\Helpers\ImageHelper::getImageUrl(auth()->user()->image) }}" 
                                 alt="Foto de perfil">
                        @else
                            <div class="h-24 w-24 lg:h-32 lg:w-32 rounded-full bg-gradient-to-br from-pink-400 to-pink-600 flex items-center justify-center text-white text-3xl lg:text-4xl font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                            {{ auth()->user()->name }} {{ auth()->user()->last_name }}
                        </h1>
                        <p class="text-gray-600 text-lg">{{ auth()->user()->email }}</p>
                        <div class="flex items-center gap-3 mt-3">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                Activo
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700 border border-pink-200 capitalize">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Quick Stats -->
                <div class="flex gap-4">
                    <div class="stat-card rounded-2xl px-6 py-4 text-center">
                        <p class="text-2xl lg:text-3xl font-bold text-[#e24972]">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->diffInDays(now()) }}</p>
                        <p class="text-gray-600 text-sm">Días en el sistema</p>
                    </div>
                    @if(auth()->user()->orders)
                    <div class="stat-card rounded-2xl px-6 py-4 text-center">
                        <p class="text-2xl lg:text-3xl font-bold text-[#e24972]">{{ auth()->user()->orders->count() }}</p>
                        <p class="text-gray-600 text-sm">Órdenes</p>
                    </div>
                    @endif
                    @if(isset($spacesFollowing))
                    <div class="stat-card rounded-2xl px-6 py-4 text-center">
                        <p class="text-2xl lg:text-3xl font-bold text-[#e24972]">{{ $spacesFollowing->count() }}</p>
                        <p class="text-gray-600 text-sm">Espacios</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-emerald-50 rounded-2xl p-4 border-l-4 border-emerald-500">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Main Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left Column - Forms -->
            <div class="xl:col-span-2 space-y-8">
                <!-- Personal Information Card -->
                <div class="card-light rounded-3xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-purple-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500 to-purple-500 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">Información Personal</h2>
                                    <p class="text-gray-500 text-sm">Gestiona tu información de perfil</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm" enctype="multipart/form-data" class="p-8">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Upload -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-8 pb-8 border-b border-gray-100">
                            <div class="relative group">
                                @if(auth()->user()->image)
                                    <img class="h-20 w-20 object-cover rounded-2xl border-2 border-pink-100 shadow-md" 
                                         src="{{ \App\Helpers\ImageHelper::getImageUrl(auth()->user()->image) }}" 
                                         alt="Foto de perfil">
                                @else
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="absolute inset-0 rounded-2xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Perfil</label>
                                <input type="file" name="image" id="image" accept="image/*" disabled
                                    class="block w-full text-sm text-gray-700 cursor-pointer
                                      file:mr-4 file:py-2.5 file:px-5
                                      file:rounded-xl file:border-2 file:border-pink-200
                                      file:text-sm file:font-semibold
                                      file:bg-pink-50 file:text-pink-700
                                      hover:file:bg-pink-100 hover:file:border-pink-300
                                      file:transition-all file:cursor-pointer
                                      disabled:opacity-50 disabled:cursor-not-allowed">
                                @error('image')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"
                                    readonly
                                    class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Apellido</label>
                                <input type="text" name="last_name" id="last_name"
                                    value="{{ old('last_name', auth()->user()->last_name) }}" readonly
                                    class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none @error('last_name') border-red-500 @enderror">
                                @error('last_name')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input type="email" name="email" id="email"
                                    value="{{ old('email', auth()->user()->email) }}" readonly
                                    class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                    readonly
                                    class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2 md:col-span-2">
                                <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
                                <input type="text" value="{{ ucfirst(auth()->user()->role) }}" disabled
                                    class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-500">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                            <button type="button" id="editBtn" onclick="toggleEdit()"
                                class="btn-primary-glow text-white px-6 py-3 rounded-xl bg-gradient-to-r from-pink-500 to-pink-600 font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                Editar Información
                            </button>
                            <button type="button" id="cancelBtn" onclick="toggleEdit()"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 from-pink-700 to-pink-900 py-3 rounded-xl font-semibold transition-all hidden">
                                Cancelar
                            </button>
                            <button type="submit" id="saveBtn"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 from-pink-700 to-pink-900 py-3 rounded-xl font-semibold transition-all hidden flex items-center gap-2 shadow-lg shadow-emerald-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Change Card -->
                <div class="card-light rounded-3xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-pink-50 to-pink-50">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500 to-pink-500 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Seguridad</h2>
                                <p class="text-gray-500 text-sm">Actualiza tu contraseña</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm" class="p-8">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
                                <input type="password" name="current_password" id="current_password" required
                                    class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                                    <input type="password" name="password" id="password" required minlength="8"
                                        class="input-light  border-1 bg-white border-gray-400 w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="text-xs text-gray-500">Mínimo 8 caracteres</p>
                                </div>

                                <div class="space-y-2">
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8"
                                        class="input-light border-1 border-gray-400 bg-white w-full rounded-xl px-4 py-3.5 text-gray-900 placeholder-gray-400 focus:outline-none">
                                    <p id="passwordMatchMessage" class="text-xs"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8 pt-6 border-t border-gray-100">
                            <button type="submit" id="changePasswordBtn"
                                class="btn-warning text-white px-6 py-3 from-pink-500 to-pink-500 bg-gradient-to-r rounded-xl font-semibold flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-8">
                <!-- Account Details -->
                <div class="card-light rounded-3xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Detalles de Cuenta</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-600 text-sm">Miembro desde</span>
                            </div>
                            <span class="text-gray-900 font-semibold">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('d M, Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-600 text-sm">Rol</span>
                            </div>
                            <span class="text-gray-900 font-semibold capitalize">{{ auth()->user()->role }}</span>
                        </div>

                        @if(auth()->user()->orders)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-600 text-sm">Total Órdenes</span>
                            </div>
                            <span class="text-gray-900 font-semibold">{{ auth()->user()->orders->count() }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-600 text-sm">Estado</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Activo
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card-light rounded-3xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Acciones Rápidas</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('events.public') }}"
                            class="group flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-pink-50 to-purple-50 border border-pink-200 hover:border-pink-400 transition-all hover:shadow-md">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-semibold">Ver Eventos</p>
                                <p class="text-gray-500 text-sm">Explora eventos disponibles</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>

                        @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('dashboard') }}"
                            class="group flex items-center gap-4 p-4 rounded-2xl bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 hover:border-blue-400 transition-all hover:shadow-md">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-900 font-semibold">Panel de Admin</p>
                                <p class="text-gray-500 text-sm">Accede al dashboard</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Spaces Following Section -->
        @if(isset($spacesFollowing) && $spacesFollowing->count() > 0)
        <div class="mt-12">
            <div class="card-light rounded-3xl overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Espacios que Sigo</h2>
                                <p class="text-gray-500 text-sm">Tus espacios favoritos</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-pink-100 text-pink-700 border border-pink-200">
                            {{ $spacesFollowing->count() }} espacio{{ $spacesFollowing->count() != 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($spacesFollowing as $space)
                        <div class="space-card bg-white rounded-2xl p-6 border border-gray-200 hover:border-pink-300 shadow-sm">
                            <div class="flex items-start gap-4 mb-5">
                                @if($space->logo)
                                    <img src="{{ \App\Helpers\ImageHelper::getImageUrl($space->logo) }}" alt="{{ $space->name }}"
                                        class="w-16 h-16 rounded-xl object-cover border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-md">
                                        {{ strtoupper(substr($space->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate text-lg">{{ $space->name }}</h4>
                                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                                        <svg class="w-4 h-4 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                        </svg>
                                        <span>{{ $space->users_count ?? 0 }} seguidores</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?: config('app.url');
                            @endphp
                            <div class="flex gap-3">
                                <a href="http://{{ $space->subdomain }}.{{ $baseHost }}/"
                                    class="flex-1 text-center py-3 px-4 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-xl text-sm font-semibold hover:from-pink-600 hover:to-pink-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-pink-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </a>
                                <button type="button" onclick="unfollowFromProfile('{{ $space->subdomain }}')"
                                    class="py-3 px-4 bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 rounded-xl text-sm font-semibold transition-all border border-gray-200 hover:border-red-200"
                                    title="Dejar de seguir">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function toggleEdit() {
        const editBtn = document.getElementById('editBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const saveBtn = document.getElementById('saveBtn');
        const inputs = document.querySelectorAll('#profileForm input[readonly]');

        if (editBtn.classList.contains('hidden')) {
            editBtn.classList.remove('hidden');
            cancelBtn.classList.add('hidden');
            saveBtn.classList.add('hidden');

            inputs.forEach(input => {
                input.readOnly = true;
                input.classList.add('bg-gray-100');
            });
            
            const fileInput = document.getElementById('image');
            if(fileInput) fileInput.disabled = true;
        } else {
            editBtn.classList.add('hidden');
            cancelBtn.classList.remove('hidden');
            saveBtn.classList.remove('hidden');

            inputs.forEach(input => {
                input.readOnly = false;
                input.classList.remove('bg-gray-100');
            });

            const fileInput = document.getElementById('image');
            if(fileInput) fileInput.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const passwordMatchMessage = document.getElementById('passwordMatchMessage');
        const changePasswordBtn = document.getElementById('changePasswordBtn');

        function validatePasswords() {
            const passwordValue = password.value;
            const confirmationValue = passwordConfirmation.value;

            if (confirmationValue.length === 0) {
                passwordMatchMessage.textContent = '';
                passwordConfirmation.classList.remove('border-red-500', 'border-emerald-500');
                changePasswordBtn.disabled = false;
                return;
            }

            if (passwordValue !== confirmationValue) {
                passwordMatchMessage.textContent = 'Las contraseñas no coinciden';
                passwordMatchMessage.classList.remove('text-emerald-600');
                passwordMatchMessage.classList.add('text-red-600');
                passwordConfirmation.classList.remove('border-emerald-500');
                passwordConfirmation.classList.add('border-red-500');
                changePasswordBtn.disabled = true;
            } else {
                passwordMatchMessage.textContent = 'Las contraseñas coinciden';
                passwordMatchMessage.classList.remove('text-red-600');
                passwordMatchMessage.classList.add('text-emerald-600');
                passwordConfirmation.classList.remove('border-red-500');
                passwordConfirmation.classList.add('border-emerald-500');
                changePasswordBtn.disabled = false;
            }
        }

        password.addEventListener('input', validatePasswords);
        passwordConfirmation.addEventListener('input', validatePasswords);
        validatePasswords();
    });

    function unfollowFromProfile(subdomain) {
        if (!confirm('¿Estás seguro de que deseas dejar de seguir este espacio?')) {
            return;
        }

        fetch(`/spaces/${subdomain}/unfollow`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al dejar de seguir');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
    }
</script>
@endsection
