@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
        <div class="max-w-7xl mx-auto py-4 sm:py-6 lg:py-8 px-2 sm:px-4 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 lg:mb-8 gap-3 sm:gap-0">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Finalizar Compra</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Revisa tu orden y completa el pago de forma segura</p>
            </div>
            <a href="{{ route('checkout.cart') }}"
                class="w-full sm:w-auto bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors flex items-center justify-center gap-2 text-sm sm:text-base whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al Carrito
            </a>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // --- 1. Inicialización de OpenPay ---
                OpenPay.setId('{{ config('services.openpay.merchant_id') }}');
                OpenPay.setApiKey('{{ config('services.openpay.public_key') }}');
                OpenPay.setSandboxMode({{ config('services.openpay.sandbox_mode', false) ? 'true' : 'false' }});

                // --- 2. Configurar Device Session ID (SOLO UNA VEZ al cargar la página) ---
                var deviceSessionId = OpenPay.deviceData.setup("payment-form", "device_session_id");

                // --- 3. Referencias a elementos del DOM ---
                const form = document.getElementById('payment-form');
                const payButton = document.getElementById('pay-button');
                const methodRadios = document.querySelectorAll('input[name="payment_method"]');

                // --- 4. Lógica para mostrar/ocultar los campos de pago ---
                methodRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const selectedValue = this.value;
                        document.getElementById('openpay-card-info').classList.toggle('hidden', selectedValue !== 'openpay');
                        document.getElementById('card-info').classList.toggle('hidden', selectedValue !== 'card');
                        document.getElementById('paypal-info').classList.toggle('hidden', selectedValue !== 'paypal');
                    });
                });
                // Para asegurar que al cargar la página se muestre el método correcto
                const checkedMethod = document.querySelector('input[name="payment_method"]:checked');
                if (checkedMethod) {
                    checkedMethod.dispatchEvent(new Event('change'));
                }

                // --- 5. Manejador del envío del formulario (SOLO UNO) ---
                form.addEventListener('submit', function(event) {
                     // Obtener valores actualizados de contraseña
                    const passwordInput = document.getElementById('customer_password');
                    const passwordConfirmationInput = document.getElementById('customer_password_confirmation');
                    // Validar que los campos de cliente estén llenos
                    const customerName = document.getElementById('customer_name').value;
                    const customerEmail = document.getElementById('customer_email').value;

                    if (!customerName || !customerEmail) {
                        event.preventDefault();
                        alert('Por favor completa tu nombre y correo electrónico antes de proceder al pago.');
                        return;
                    }

                    // Solo validar contraseña si los campos existen (usuario NO autenticado)
                    if (passwordInput && passwordConfirmationInput) {
                        const password = passwordInput.value || '';
                        const passwordConfirmation = passwordConfirmationInput.value || '';

                        if (!password || password.length < 8) {
                            event.preventDefault();
                            alert('La contraseña debe tener al menos 8 caracteres.');
                            return;
                        }

                        if (password !== passwordConfirmation) {
                            event.preventDefault();
                            alert('Las contraseñas no coinciden.');
                            return;
                        }
                    }

                    const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

                    // Solo intervenimos si el método de pago es OpenPay
                    if (selectedMethod === 'openpay') {
                        // Prevenimos el envío automático para generar el token primero
                        event.preventDefault();
                        payButton.disabled = true;
                        payButton.innerText = 'Procesando...'; // Feedback para el usuario

                        OpenPay.token.create({
                                "card_number": document.querySelector('[data-openpay-card="card_number"]').value,
                                "holder_name": document.querySelector('[data-openpay-card="holder_name"]').value,
                                "expiration_year": document.querySelector('[data-openpay-card="expiration_year"]').value,
                                "expiration_month": document.querySelector('[data-openpay-card="expiration_month"]').value,
                                "cvv2": document.querySelector('[data-openpay-card="cvv2"]').value,
                            },
                            success_callback, // Función si se crea el token
                            error_callback    // Función si hay un error
                        );
                    }
                    // Si el método no es 'openpay', el formulario se envía de forma normal.
                });

                // --- 6. Callbacks para la creación del token ---
                function success_callback(response) {
                    console.log("Token creado exitosamente:", response.data.id);
                    // Asignamos el token al campo oculto
                    document.getElementById('openpay_token').value = response.data.id;

                    // Ahora sí, enviamos el formulario al backend
                    form.submit();
                }

                function error_callback(response) {
                    console.error("Error creando el token:", response);
                    // Mostramos el error al usuario
                    alert('Error: ' + response.data.description);

                    // Reactivamos el botón para que el usuario pueda corregir los datos
                    payButton.disabled = false;
                    payButton.innerText = 'Completar Compra';
                }
            });
            // Poder ver la contraseña
            function togglePassword(id, btn) {
                const input = document.getElementById(id);

                if (input.type === "password") {
                    input.type = "text";
                    btn.textContent = "🙈"; // Cambia el icono
                } else {
                    input.type = "password";
                    btn.textContent = "👁️";
                }
            }
        </script>

        <form id="payment-form" method="POST" action="{{ route('checkout.process-payment') }}">
            @csrf

            <!-- Información del Cliente (siempre visible, no requiere login) -->
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200 mb-6">
                <div class="px-3 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Información de Contacto</h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Ingresa tus datos para completar la compra</p>
                </div>
                <div class="p-3 sm:p-4 lg:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="customer_name"
                                   id="customer_name"
                                   value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="customer_email"
                                   id="customer_email"
                                   value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Te enviaremos la confirmación de tu compra a este correo</p>
                        </div>
                    </div>

                    @if(!auth()->check())
                    <!-- Opción para crear cuenta (solo si no está logueado) -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <!-- Campos de contraseña  -->
                        <div id="password-fields" class=" mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative">
                                <label for="customer_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       name="customer_password"
                                       id="customer_password"
                                       minlength="8"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button"
                                        onclick="togglePassword('customer_password', this)"
                                        class="absolute right-3 top-9 text-gray-500">
                                    👁️
                                </button>

                                <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                            </div>
                            <div class="relative">
                                <label for="customer_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Contraseña <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       name="customer_password_confirmation"
                                       id="customer_password_confirmation"
                                       minlength="8"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                       <button type="button"
                                               onclick="togglePassword('customer_password_confirmation', this)"
                                               class="absolute right-3 top-9 text-gray-500">
                                           👁️
                                       </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Payment Form -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                        <div class="px-3 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Resumen de la Orden</h2>
                        </div>
                        <div class="p-3 sm:p-4 lg:p-6">
                            <div class="space-y-4">
                                @php
                                    $subtotal = 0;
                                    $totalItems = 0;
                                    foreach ($cart as $item) {
                                        $subtotal += $item['price'] * $item['quantity'];
                                        $totalItems += $item['quantity'];
                                    }
                                @endphp

                                @foreach ($cart as $item)
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $item['ticket_type_name'] ?? 'Boleto' }}</p>
                                            <p class="text-sm text-gray-500">{{ $item['quantity'] }} x
                                                ${{ number_format($item['price'], 2) }}</p>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">
                                            ${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                    </div>
                                @endforeach

                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between">
                                        <dt class="text-base font-medium text-gray-900">Subtotal</dt>
                                        <dd class="text-base font-medium text-gray-900">${{ number_format($subtotal, 2) }}
                                        </dd>
                                    </div>
                                    @php
                                        $discount = 0;
                                        if ($appliedCoupon) {
                                            $discount = ($subtotal * $appliedCoupon->discount_percentage) / 100;
                                        }
                                        $taxableAmount = $subtotal - $discount;
                                        $taxes = $taxableAmount * 0.16; // 16% IVA
                                        $total = $taxableAmount + $taxes;
                                    @endphp
                                    @if ($appliedCoupon)
                                        <div class="flex justify-between text-green-600">
                                            <dt class="text-base font-medium">Descuento ({{ $appliedCoupon->code }})</dt>
                                            <dd class="text-base font-medium">-${{ number_format($discount, 2) }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <dt class="text-base font-medium text-gray-900">IVA (16%)</dt>
                                        <dd class="text-base font-medium text-gray-900">${{ number_format($taxes, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2">
                                        <dt class="text-lg font-bold text-gray-900">Total</dt>
                                        <dd class="text-lg font-bold text-gray-900">${{ number_format($total, 2) }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon Code -->
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                        <div class="px-3 py-3 sm:px-6 sm:py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Código de Descuento</h2>
                        </div>
                        <div class="p-6">
                            @if ($appliedCoupon)
                                <!-- Applied Coupon -->
                                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-green-800">Cupón aplicado:</p>
                                            <p class="text-lg font-semibold text-green-900">{{ $appliedCoupon->code }}</p>
                                            <p class="text-sm text-green-600">{{ $appliedCoupon->discount_percentage }}% de
                                                descuento</p>
                                        </div>
                                        <button type="button" onclick="removeCoupon()"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- Coupon Input -->
                                <div class="flex space-x-2">
                                    <input type="text" id="coupon_code" placeholder="Ingresa tu código de descuento"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <button type="button" onclick="applyCoupon()"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Aplicar
                                    </button>
                                </div>
                                <div id="coupon_message" class="mt-2 text-sm hidden"></div>
                            @endif
                            @if (auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('staff')))
                                @if ($coupons->count() > 0)
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500 mb-2">Cupones disponibles:</p>
                                        <div class="space-y-2">
                                            @foreach ($coupons as $coupon)
                                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                                    <div>
                                                        <span
                                                            class="font-mono text-sm font-medium">{{ $coupon->code }}</span>
                                                        <span
                                                            class="text-sm text-gray-500 ml-2">{{ $coupon->discount_percentage }}%
                                                            de descuento</span>
                                                    </div>
                                                    <button type="button" onclick="useCoupon('{{ $coupon->code }}')"
                                                        class="text-blue-600 hover:text-blue-900 text-sm">
                                                        Usar
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                    <p class="text-sm text-yellow-800">Los cupones solo están disponibles para
                                        administradores y staff.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="space-y-6">
                    <!-- Payment Method -->
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Información de Pago</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Simulated Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="payment_method" value="openpay" checked>
                                            <span class="text-sm text-gray-900">Tarjeta de Crédito/Débito (Openpay)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Card Information (Simulated) - Hidden in production -->
                                <div id="card-info" class="hidden">
                                    <div class="p-4 bg-blue-50 rounded-md">
                                        <p class="text-sm text-blue-800">Este método de pago no está disponible actualmente. Por favor, utiliza Openpay.</p>
                                    </div>
                                </div>

                                <!-- OpenPay Information -->
                                <div id="openpay-card-info">

                                    <input type="hidden" name="openpay_token" id="openpay_token">
                                    <input type="hidden" name="device_session_id" id="device_session_id">

                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-1">Número de Tarjeta</label>
                                            {{-- ATRIBUTO AÑADIDO --}}
                                            <input type="text" class="w-full px-3 py-2 border rounded"
                                                autocomplete="off" data-openpay-card="card_number">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-1">CVV</label>
                                            {{-- ATRIBUTO AÑADIDO --}}
                                            <input type="text" class="w-full px-3 py-2 border rounded"
                                                autocomplete="off" data-openpay-card="cvv2">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium mb-1">Fecha de Expiración</label>
                                        <div class="flex gap-2">
                                            {{-- ATRIBUTOS AÑADIDOS --}}
                                            <input type="text" placeholder="MM" class="w-1/2 px-3 py-2 border rounded"
                                                data-openpay-card="expiration_month">
                                            <input type="text" placeholder="YY" class="w-1/2 px-3 py-2 border rounded"
                                                data-openpay-card="expiration_year">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium mb-1">Nombre del Titular</label>
                                        {{-- ATRIBUTO AÑADIDO --}}
                                        <input type="text" class="w-full px-3 py-2 border rounded"
                                            data-openpay-card="holder_name">
                                    </div>
                                </div>

                                <!-- PayPal Information (Hidden by default) -->
                                <div id="paypal-info" class="hidden">
                                    <div class="p-4 bg-blue-50 rounded-md">
                                        <p class="text-sm text-blue-800">Serás redirigido a PayPal para completar el pago.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Total and Submit -->
                    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-medium text-gray-900">Total a Pagar</span>
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($total, 2) }}</span>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-blue-800">Pago Seguro</h3>
                                            <p class="text-sm text-blue-700">Tus pagos están protegidos con Openpay. Todos los datos son procesados de forma segura.</p>
                                        </div>
                                    </div>
                                </div>
                                    <button type="submit" id="pay-button"
                                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-3 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all font-semibold shadow-md hover:shadow-lg transform hover:scale-[1.02] flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                        Completar Compra
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function applyCoupon() {
            // Simulated coupon application
            alert('Cupón aplicado correctamente');
        }

        function useCoupon(code) {
            document.querySelector('input[name="coupon_code"]').value = code;
        }


        // Payment method toggle
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const method = this.value;

                const sections = {
                    card: document.getElementById('card-info'),
                    paypal: document.getElementById('paypal-info'),
                    openpay: document.getElementById('openpay-card-info'),
                };

                Object.values(sections).forEach(section => section.classList.add('hidden'));

                if (sections[method]) {
                    sections[method].classList.remove('hidden');
                }
            });
        });


        // Coupon functions
        async function applyCoupon() {
            const couponCode = document.getElementById('coupon_code').value;
            const messageDiv = document.getElementById('coupon_message');

            if (!couponCode) {
                messageDiv.textContent = 'Por favor ingresa un código de cupón';
                messageDiv.className = 'mt-2 text-sm text-red-600';
                messageDiv.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch('{{ route('checkout.apply-coupon') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        coupon_code: couponCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    messageDiv.textContent = data.message;
                    messageDiv.className = 'mt-2 text-sm text-green-600';
                    messageDiv.classList.remove('hidden');

                    // Reload page to show applied coupon
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    messageDiv.textContent = data.message;
                    messageDiv.className = 'mt-2 text-sm text-red-600';
                    messageDiv.classList.remove('hidden');
                }
            } catch (error) {
                messageDiv.textContent = 'Error al aplicar el cupón';
                messageDiv.className = 'mt-2 text-sm text-red-600';
                messageDiv.classList.remove('hidden');
            }
        }

        async function removeCoupon() {
            try {
                const response = await fetch('{{ route('checkout.remove-coupon') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Reload page to remove applied coupon
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error removing coupon:', error);
            }
        }

        // Manejar login/registro rápido
        async function handleQuickAuth(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const action = event.submitter.value;
            formData.append('action', action);

            const messageDiv = document.getElementById('auth-message');
            messageDiv.classList.add('hidden');

            try {
                const response = await fetch('{{ route('checkout.quick-login-register') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    messageDiv.className = 'mt-4 text-sm p-3 rounded bg-green-100 text-green-800';
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('hidden');

                    // Redirigir después de 1 segundo
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    messageDiv.className = 'mt-4 text-sm p-3 rounded bg-red-100 text-red-800';
                    messageDiv.textContent = data.message;
                    messageDiv.classList.remove('hidden');
                }
            } catch (error) {
                messageDiv.className = 'mt-4 text-sm p-3 rounded bg-red-100 text-red-800';
                messageDiv.textContent = 'Error al procesar la solicitud. Por favor, intenta de nuevo.';
                messageDiv.classList.remove('hidden');
            }
        }
    </script>
@endsection

@push('scripts')
    <script>
        // Refrescar token CSRF cada 5 minutos
        setInterval(function() {
            fetch('/refresh-csrf', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.csrf_token) {
                        document.querySelector('input[name="_token"]').value = data.csrf_token;
                    }
                })
                .catch(error => console.log('Error refreshing CSRF token:', error));
        }, 300000); // 5 minutos
    </script>
@endpush
