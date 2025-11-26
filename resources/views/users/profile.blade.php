@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mi Perfil</h1>
                <p class="text-gray-600 mt-1">Gestiona tu información personal</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Información Personal</h2>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-600">Activo</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   readonly
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Apellido</label>
                            <input type="text" 
                                   name="last_name" 
                                   id="last_name"
                                   value="{{ old('last_name', auth()->user()->last_name) }}"
                                   readonly
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email"
                                   value="{{ old('email', auth()->user()->email) }}"
                                   readonly
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" 
                                   name="phone" 
                                   id="phone"
                                   value="{{ old('phone', auth()->user()->phone) }}"
                                   readonly
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                            <input type="text" 
                                   value="{{ ucfirst(auth()->user()->role) }}"
                                   disabled
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-gray-100 text-gray-500">
                        </div>
                    </div>

                    <!-- Botones de acción dentro del formulario -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" 
                                id="editBtn" 
                                onclick="toggleEdit()" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar Información
                        </button>
                        <button type="button" 
                                id="cancelBtn" 
                                onclick="toggleEdit()" 
                                class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors hidden">
                            Cancelar
                        </button>
                        <button type="submit" 
                                id="saveBtn"
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors hidden flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white shadow-xl rounded-xl p-8 mt-6 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Cambiar Contraseña</h2>
                <form method="POST" action="{{ route('profile.password.update') }}" id="passwordForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password"
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('current_password') border-red-500 @enderror">
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
                                   minlength="8"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   required
                                   minlength="8"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-blue-500 focus:border-blue-500">
                            <p id="passwordMatchMessage" class="mt-1 text-xs"></p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" 
                                id="changePasswordBtn"
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Stats -->
            <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Estadísticas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Miembro desde:</span>
                        <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse(auth()->user()->created_at)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Rol:</span>
                        <span class="font-semibold text-gray-900 capitalize">{{ auth()->user()->role }}</span>
                    </div>
                    @if(auth()->user()->orders)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Órdenes:</span>
                            <span class="font-semibold text-gray-900">{{ auth()->user()->orders->count() }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Estado:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Activo
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Acciones Rápidas</h3>
                <div class="space-y-3">
                    <a href="{{ route('events.public') }}" 
                       class="block w-full bg-green-600 text-white text-center py-3 px-4 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Ver Eventos
                    </a>
                    
                    @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('dashboard') }}" 
                           class="block w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEdit() {
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.getElementById('saveBtn');
    const inputs = document.querySelectorAll('#profileForm input[readonly]');
    
    if (editBtn.classList.contains('hidden')) {
        // Cancelar edición
        editBtn.classList.remove('hidden');
        cancelBtn.classList.add('hidden');
        saveBtn.classList.add('hidden');
        
        // Hacer campos readonly
        inputs.forEach(input => {
            input.readOnly = true;
            input.classList.add('bg-gray-50');
            input.classList.remove('bg-white');
        });
    } else {
        // Activar edición
        editBtn.classList.add('hidden');
        cancelBtn.classList.remove('hidden');
        saveBtn.classList.remove('hidden');
        
        // Hacer campos editables
        inputs.forEach(input => {
            input.readOnly = false;
            input.classList.remove('bg-gray-50');
            input.classList.add('bg-white');
        });
    }
}

// Validación en tiempo real de contraseñas
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    const passwordMatchMessage = document.getElementById('passwordMatchMessage');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    
    function validatePasswords() {
        const passwordValue = password.value;
        const confirmationValue = passwordConfirmation.value;
        
        if (confirmationValue.length === 0) {
            passwordMatchMessage.textContent = '';
            passwordConfirmation.classList.remove('border-red-500', 'border-green-500');
            changePasswordBtn.disabled = false;
            return;
        }
        
        if (passwordValue !== confirmationValue) {
            passwordMatchMessage.textContent = 'Las contraseñas no coinciden';
            passwordMatchMessage.classList.remove('text-green-600');
            passwordMatchMessage.classList.add('text-red-600');
            passwordConfirmation.classList.remove('border-green-500');
            passwordConfirmation.classList.add('border-red-500');
            changePasswordBtn.disabled = true;
        } else {
            passwordMatchMessage.textContent = 'Las contraseñas coinciden';
            passwordMatchMessage.classList.remove('text-red-600');
            passwordMatchMessage.classList.add('text-green-600');
            passwordConfirmation.classList.remove('border-red-500');
            passwordConfirmation.classList.add('border-green-500');
            changePasswordBtn.disabled = false;
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);
    
    // Validar al cargar si hay valores
    validatePasswords();
});
</script>
@endsection
