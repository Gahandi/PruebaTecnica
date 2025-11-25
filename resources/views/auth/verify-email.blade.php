@extends('layouts.app')

@section('title', 'Verificar Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verificar tu Email
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ingresa el código de verificación que enviamos a tu correo electrónico
            </p>
        </div>

        @if(session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="rounded-md bg-yellow-50 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">
                            {{ session('warning') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">
                            {{ session('info') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form class="mt-8 space-y-6" id="verify-form" method="POST" action="{{ route('verify.code') }}">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Correo Electrónico
                </label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    required 
                    value="{{ $email }}"
                    @if(auth()->check() && !empty($email)) readonly @endif
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror @if(auth()->check() && !empty($email)) bg-gray-50 @endif"
                    placeholder="tu@email.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Código de Verificación
                </label>
                <input 
                    id="code" 
                    name="code" 
                    type="text" 
                    required 
                    maxlength="6"
                    pattern="[0-9]{6}"
                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm text-center text-2xl font-mono tracking-widest @error('code') border-red-500 @enderror"
                    placeholder="000000">
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div id="error-message" class="hidden rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800"></p>
            </div>

            <div id="success-message" class="hidden rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800"></p>
            </div>

            <div>
                <button 
                    type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Verificar Código
                </button>
            </div>

            <div class="text-center space-y-2">
                <button 
                    type="button"
                    id="resend-code-btn"
                    class="text-sm text-indigo-600 hover:text-indigo-500 underline">
                    Reenviar código
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('code');
    const verifyForm = document.getElementById('verify-form');
    const resendBtn = document.getElementById('resend-code-btn');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const emailInput = document.getElementById('email');

    // Auto-avanzar en el input de código
    codeInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6) {
            // Opcional: auto-submit cuando se completa el código
            // verifyForm.submit();
        }
    });
    
    // Manejar clic en el botón de reenviar código
    if (resendBtn) {
        resendBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener el email del campo visible
            const email = emailInput ? emailInput.value.trim() : '';
            
            // Validar que el email no esté vacío
            if (!email) {
                errorMessage.querySelector('p').textContent = 'Por favor ingresa tu correo electrónico en el campo de arriba';
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');
                if (emailInput) {
                    emailInput.focus();
                }
                return false;
            }
            
            // Validar formato de email básico
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errorMessage.querySelector('p').textContent = 'Por favor ingresa un correo electrónico válido';
                errorMessage.classList.remove('hidden');
                successMessage.classList.add('hidden');
                if (emailInput) {
                    emailInput.focus();
                }
                return false;
            }
            
            // Ocultar mensajes anteriores
            errorMessage.classList.add('hidden');
            successMessage.classList.add('hidden');
            
            // Deshabilitar botón mientras se envía
            resendBtn.disabled = true;
            resendBtn.textContent = 'Enviando...';
            resendBtn.classList.add('opacity-50', 'cursor-not-allowed');
            
            // Crear formulario dinámico y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("verify.send-code") }}';
            
            // Agregar CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Agregar email
            const emailFormInput = document.createElement('input');
            emailFormInput.type = 'hidden';
            emailFormInput.name = 'email';
            emailFormInput.value = email;
            form.appendChild(emailFormInput);
            
            // Agregar formulario al body y enviarlo
            document.body.appendChild(form);
            form.submit();
        });
    }
});
</script>
@endsection

