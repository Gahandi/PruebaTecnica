@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
                <p class="text-gray-600">Completa tu compra de boletos</p>
            </div>
            <a href="{{ route('checkout.cart') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                ← Volver al Carrito
            </a>
        </div>

        <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Inicialización de OpenPay ---
        // Estas son tus claves públicas de prueba
        OpenPay.setId('mwhnrmwyovfltcwhbs8r');
        OpenPay.setApiKey('pk_233292d84bc64d3fbfb290a22d9de4bc');
        OpenPay.setSandboxMode(true);

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
        document.querySelector('input[name="payment_method"]:checked').dispatchEvent(new Event('change'));


        // --- 5. Manejador del envío del formulario (SOLO UNO) ---
        form.addEventListener('submit', function(event) {
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
</script>

        <form id="payment-form" method="POST" action="{{ route('checkout.process-payment') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Payment Form -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Resumen de la Orden</h2>
                        </div>
                        <div class="p-6">
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
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Código de Descuento</h2>
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
                            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('staff'))
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
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">Información de Pago</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Simulated Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="payment_method" value="card" checked
                                                class="mr-2">
                                            <span class="text-sm text-gray-900">Tarjeta de Crédito/Débito</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="payment_method" value="openpay" class="mr-2">
                                            <span class="text-sm text-gray-900">Tarjeta con Openpay</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="payment_method" value="paypal" class="mr-2">
                                            <span class="text-sm text-gray-900">PayPal</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Card Information (Simulated) -->
                                <div id="card-info">
                                    <div class="mb-4">
                                        <button type="button" onclick="fillSimulatedData()"
                                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm">
                                            🔧 Rellenar Datos Simulados
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de
                                                Tarjeta</label>
                                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                            <input type="text" name="card_cvv" placeholder="123"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de
                                            Expiración</label>
                                        <input type="text" name="card_expiry" placeholder="MM/YY"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre en la
                                            Tarjeta</label>
                                        <input type="text" name="card_name" placeholder="Juan Pérez"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    @if (session('error'))
                                        <div class="my-4 rounded-md bg-red-50 p-4 border border-red-200">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-red-800">
                                                        {{ session('error') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- OpenPay Information (Hidden by default) -->
                                <div id="openpay-card-info" class="hidden">
                                    {{-- Los scripts de Openpay es mejor cargarlos en el <head> o al final del <body>,
         pero si los dejas aquí, asegúrate de que se carguen antes de usarlos. --}}
                                    <script src="https://js.openpay.mx/openpay.v1.min.js"></script>
                                    <script src="https://js.openpay.mx/openpay-data.v1.min.js"></script>

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
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-medium text-gray-900">Total a Pagar</span>
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($total, 2) }}</span>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                    <div class="flex">
                                        <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <div>
                                            <h3 class="text-sm font-medium text-yellow-800">Pago Simulado</h3>
                                            <p class="text-sm text-yellow-700">Este es un sistema de demostración. No se
                                                procesará ningún pago real.</p>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" id="pay-button"
                                    class="w-full bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 transition-colors font-medium">
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

        function fillSimulatedData() {
            // Datos simulados para pruebas
            document.querySelector('input[name="card_number"]').value = '4111 1111 1111 1111';
            document.querySelector('input[name="card_cvv"]').value = '123';
            document.querySelector('input[name="card_expiry"]').value = '12/25';
            document.querySelector('input[name="card_name"]').value = '{{ auth()->user()->name }}';

            // Mostrar mensaje de confirmación
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '✅ Datos Rellenados';
            button.classList.remove('bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-green-500');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
            }, 2000);
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
