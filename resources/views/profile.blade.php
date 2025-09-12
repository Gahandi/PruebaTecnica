@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Mi Perfil</h1>
        <p class="text-gray-600">Gestiona tu información personal</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información Personal</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   value="{{ old('email', auth()->user()->email) }}"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                            <input type="text" 
                                   value="{{ ucfirst(auth()->user()->role) }}"
                                   disabled
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Actualizar Perfil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white shadow-lg rounded-lg p-6 mt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Cambiar Contraseña</h2>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="submit" 
                                class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Stats -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Miembro desde:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rol:</span>
                        <span class="font-medium capitalize">{{ auth()->user()->role }}</span>
                    </div>
                    @if(auth()->user()->orders)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Órdenes:</span>
                            <span class="font-medium">{{ auth()->user()->orders->count() }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('dashboard') }}" 
                       class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Dashboard
                    </a>
                    <a href="{{ route('events.public') }}" 
                       class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                        Ver Eventos
                    </a>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.events.index') }}" 
                           class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded-md hover:bg-purple-700 transition-colors">
                            Administrar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
