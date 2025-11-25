<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\TicketsEvent;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Openpay\Data\Openpay;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Traits\S3ImageManager;
use App\Models\User;
use App\Models\UsersCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendTicketPurchaseEmail;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    use S3ImageManager;

    public function __construct()
    {
        $this->middleware('auth')->except([
            'addToCart',
            'cart',
            'checkout',
            'processPayment',
            'handlePaymentCallback',
            'quickLoginOrRegister',
            'getCartCount',
            'getCartDropdown',
            'syncCart',
            'applyCoupon',
            'removeCoupon'
        ]);
    }

    /**
     * Mostrar el carrito de compras
     */
    public function cart()
    {
        // Obtener carrito de la sesión
        $cart = session()->get('cart', []);

        // Si el carrito está vacío, intentar sincronizar desde localStorage
        // (esto se hará automáticamente desde el frontend)

        // Obtener carrito con información completa
        $cart = \App\Helpers\CartHelper::getCartWithEventInfo();

        $events = Event::with('ticketTypes')->get();

        return view('checkout.cart', compact('cart', 'events'));
    }

    /**
     * Sincronizar carrito desde localStorage con la sesión del servidor
     */
    public function syncCart(Request $request)
    {
        // Manejar preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            $origin = $request->headers->get('Origin');
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $origin ?: '*')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }

        $cartData = $request->input('cart', []);

        // Validar y limpiar datos del carrito, manteniendo las claves originales
        $validatedCart = [];
        foreach ($cartData as $key => $item) {
            if (isset($item['ticket_type_id']) && isset($item['event_id']) && isset($item['quantity'])) {
                // Usar la clave original si es válida, o generar una nueva
                $cartKey = $key;
                if (!preg_match('/^\d+_\d+$/', $key)) {
                    // Si la clave no es del formato correcto, generar una nueva
                    $cartKey = $item['ticket_type_id'] . '_' . $item['event_id'];
                }

                $validatedCart[$cartKey] = [
                    'ticket_type_id' => (int)$item['ticket_type_id'],
                    'event_id' => (int)$item['event_id'],
                    'quantity' => (int)$item['quantity'],
                    'price' => isset($item['price']) ? (float)$item['price'] : 0,
                    'ticket_type_name' => $item['ticket_type_name'] ?? 'Boleto',
                    'event_name' => $item['event_name'] ?? 'Evento',
                    'event_date' => $item['event_date'] ?? null,
                    'event_image' => $item['event_image'] ?? null,
                ];
            }
        }

        // Guardar en sesión (reemplazar completamente)
        session()->put('cart', $validatedCart);

        $origin = $request->headers->get('Origin');
        $response = response()->json([
            'success' => true,
            'message' => 'Carrito sincronizado correctamente.',
            'cart' => $validatedCart,
            'cart_count' => count($validatedCart)
        ]);

        // Agregar headers CORS
        if ($origin) {
            $response->header('Access-Control-Allow-Origin', $origin)
                     ->header('Access-Control-Allow-Credentials', 'true')
                     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                     ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }

        return $response;
    }

    /**
     * Obtener el conteo del carrito via AJAX
     */
    public function getCartCount()
    {
        $count = \App\Helpers\CartHelper::getCartCount();

        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Obtener el HTML del dropdown del carrito via AJAX
     */
    public function getCartDropdown()
    {
        $cart = \App\Helpers\CartHelper::getCartWithEventInfo();
        $cartCount = \App\Helpers\CartHelper::getCartCount();
        $cartTotal = \App\Helpers\CartHelper::getCartTotal();

        $html = view('partials.cart-dropdown', [
            'cart' => $cart,
            'cartCount' => $cartCount,
            'cartTotal' => $cartTotal
        ])->render();

        return response()->json([
            'html' => $html,
            'count' => $cartCount
        ]);
    }

    /**
     * Agregar item al carrito
     */
    public function addToCart(Request $request)
    {
        // Manejar preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $request->headers->get('Origin'))
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }

        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Buscar el ticket_event específico para este evento y tipo de boleto
        $ticket_event = \App\Models\TicketsEvent::where('ticket_types_id', $request->ticket_type_id)
            ->where('event_id', $request->event_id)
            ->with(['event', 'ticket_type'])
            ->first();

        if (!$ticket_event) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de boleto no encontrado para este evento.'
                ], 404);
            }
            return back()->with('error', 'Tipo de boleto no encontrado para este evento.');
        }

        $cart = session()->get('cart', []);
        $cartKey = $ticket_event->ticket_types_id . '_' . $ticket_event->event_id;

        // Verificar disponibilidad considerando reservas activas
        $currentQuantity = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $request->quantity;

        // Verificar reservas activas de otros usuarios (excluyendo la sesión actual)
        $reservedByOthers = \App\Models\TicketReservation::where('ticket_types_id', $ticket_event->ticket_types_id)
            ->where('event_id', $ticket_event->event_id)
            ->where('reserved_until', '>', now())
            ->where('is_active', true)
            ->where('session_id', '!=', session()->getId())
            ->sum('quantity');

        // Verificar reservas del usuario actual
        $reservedByCurrentUser = \App\Models\TicketReservation::where('ticket_types_id', $ticket_event->ticket_types_id)
            ->where('event_id', $ticket_event->event_id)
            ->where('reserved_until', '>', now())
            ->where('is_active', true)
            ->where('session_id', session()->getId())
            ->sum('quantity');

        // Calcular disponibilidad real
        $availableQuantity = $ticket_event->quantity - $reservedByOthers - $reservedByCurrentUser;

        if ($totalQuantity > $availableQuantity) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficientes boletos disponibles. Solo quedan ' . $availableQuantity . ' boletos.'
                ], 400);
            }
            return back()->with('error', 'No hay suficientes boletos disponibles. Solo quedan ' . $availableQuantity . ' boletos.');
        }

        // Crear reserva temporal
        \App\Helpers\CartHelper::createReservation(
            $ticket_event->ticket_types_id,
            $ticket_event->event_id,
            $totalQuantity,
            15 // 15 minutos
        );

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $totalQuantity;
            // Actualizar precio desde TicketsEvent para asegurar que sea el correcto para este evento
            $cart[$cartKey]['price'] = $ticket_event->price;
        } else {
            $cart[$cartKey] = [
                'ticket_type_id' => $ticket_event->ticket_types_id,
                'event_id' => $ticket_event->event_id,
                'quantity' => $request->quantity,
                'price' => $ticket_event->price,
                'ticket_type_name' => $ticket_event->ticket_type->name,
                'event_name' => $ticket_event->event->name,
                'event_date' => $ticket_event->event->date,
                'event_image' => $ticket_event->event->image,
            ];
        }

        session()->put('cart', $cart);

        if (request()->ajax()) {
            // Preparar datos del item para localStorage
            $itemData = [
                'ticket_type_id' => $ticket_event->ticket_types_id,
                'event_id' => $ticket_event->event_id,
                'price' => $ticket_event->price,
                'ticket_type_name' => $ticket_event->ticket_type->name,
                'event_name' => $ticket_event->event->name,
                'event_date' => $ticket_event->event->date,
                'event_image' => $ticket_event->event->image,
            ];

            $origin = $request->headers->get('Origin');
            $response = response()->json([
                'success' => true,
                'message' => 'Boletos agregados al carrito.',
                'cart_count' => \App\Helpers\CartHelper::getCartCount(),
                'cart' => $cart,
                'item_data' => $itemData
            ]);

            // Agregar headers CORS si hay un origin
            if ($origin) {
                $response->header('Access-Control-Allow-Origin', $origin)
                         ->header('Access-Control-Allow-Credentials', 'true')
                         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                         ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
            }

            return $response;
        }

        return back()->with('success', 'Boletos agregados al carrito.');
    }

    /**
     * Actualizar cantidad en el carrito
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);
        $cartKey = $request->cart_key;

        if (!isset($cart[$cartKey])) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado en el carrito.'
                ], 404);
            }
            return back()->with('error', 'Item no encontrado en el carrito.');
        }

        $item = $cart[$cartKey];

        if ($request->quantity == 0) {
            unset($cart[$cartKey]);
        } else {
            // Obtener el precio desde TicketsEvent si hay event_id
            if (isset($item['event_id']) && isset($item['ticket_type_id'])) {
                $ticketEvent = \App\Models\TicketsEvent::where('ticket_types_id', $item['ticket_type_id'])
                    ->where('event_id', $item['event_id'])
                    ->first();

                if (!$ticketEvent) {
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No se encontró la relación del boleto con el evento.'
                        ], 404);
                    }
                    return back()->with('error', 'No se encontró la relación del boleto con el evento.');
                }

                // Verificar disponibilidad
                if ($request->quantity > $ticketEvent->quantity) {
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No hay suficientes boletos disponibles. Solo quedan ' . $ticketEvent->quantity . ' boletos.'
                        ], 400);
                    }
                    return back()->with('error', 'No hay suficientes boletos disponibles. Solo quedan ' . $ticketEvent->quantity . ' boletos.');
                }

                // Actualizar precio desde TicketsEvent para asegurar que sea el correcto
                $cart[$cartKey]['quantity'] = $request->quantity;
                $cart[$cartKey]['price'] = $ticketEvent->price; // Actualizar precio desde la BD
            } else {
                // Si no hay event_id, usar TicketType directamente
                if (isset($item['ticket_type_id'])) {
                    $ticketType = TicketType::findOrFail($item['ticket_type_id']);

                    if ($request->quantity > $ticketType->quantity) {
                        if (request()->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'No hay suficientes boletos disponibles.'
                            ], 400);
                        }
                        return back()->with('error', 'No hay suficientes boletos disponibles.');
                    }

                    $cart[$cartKey]['quantity'] = $request->quantity;
                    // Actualizar precio si no existe
                    if (!isset($cart[$cartKey]['price'])) {
                        $cart[$cartKey]['price'] = $ticketType->price;
                    }
                } else {
                    // Si no hay ticket_type_id, simplemente actualizar cantidad
                    $cart[$cartKey]['quantity'] = $request->quantity;
                }
            }
        }

        session()->put('cart', $cart);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Carrito actualizado.',
                'cart' => $cart,
                'cart_count' => \App\Helpers\CartHelper::getCartCount()
            ]);
        }

        return back()->with('success', 'Carrito actualizado.');
    }

    /**
     * Remover item del carrito
     */
    public function removeFromCart($key)
    {
        $cart = session()->get('cart', []);

        // Log para debugging
        \Log::info('removeFromCart called', [
            'key' => $key,
            'cart_keys' => array_keys($cart),
            'cart' => $cart
        ]);

        // Buscar el item por la clave exacta o por ticket_type_id y event_id
        $found = false;
        $removedKey = null;

        // Primero intentar por clave exacta
        if (isset($cart[$key])) {
            unset($cart[$key]);
            $found = true;
            $removedKey = $key;
        } else {
            // Si no se encuentra por clave, buscar por ticket_type_id y event_id
            // La clave puede venir como "ticket_type_id_event_id"
            $keyParts = explode('_', $key);
            if (count($keyParts) === 2) {
                $ticketTypeId = (int)$keyParts[0];
                $eventId = (int)$keyParts[1];

                foreach ($cart as $cartKey => $item) {
                    $itemTicketTypeId = isset($item['ticket_type_id']) ? (int)$item['ticket_type_id'] : null;
                    $itemEventId = isset($item['event_id']) ? (int)$item['event_id'] : null;

                    if ($itemTicketTypeId === $ticketTypeId && $itemEventId === $eventId) {
                        unset($cart[$cartKey]);
                        $found = true;
                        $removedKey = $cartKey;
                        break;
                    }
                }
            }
        }

        if ($found) {
            session()->put('cart', $cart);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removido del carrito.',
                    'cart' => $cart,
                    'cart_count' => \App\Helpers\CartHelper::getCartCount(),
                    'removed_key' => $removedKey
                ]);
            }

            return back()->with('success', 'Item removido del carrito.');
        }

        // Si no se encontró, intentar sincronizar desde localStorage primero
        if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Item no encontrado en el carrito. Clave buscada: ' . $key . '. Claves disponibles: ' . implode(', ', array_keys($cart)),
                'cart' => $cart,
                'cart_keys' => array_keys($cart)
            ], 404);
        }

        return back()->with('error', 'Item no encontrado en el carrito.');
    }


    /**
     * Mostrar el formulario de checkout
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('checkout.cart')->with('error', 'Tu carrito está vacío.');
        }

        $coupons = Coupon::where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })->get();

        $appliedCoupon = session()->get('applied_coupon');

        return view('checkout.checkout', compact('cart', 'coupons', 'appliedCoupon'));
    }

    /**
     * Procesar el pago
     */
    public function processPayment(Request $request)
    {
        try {
            // Validar email y nombre
            $validationRules = [
                'customer_email' => 'required|email|max:255',
                'customer_name' => 'required|string|max:255',
                'payment_method' => 'required|in:openpay',
                'openpay_token' => 'required',
                'device_session_id' => 'required',
            ];

            // Si se solicita crear cuenta, validar contraseña
            if ($request->has('create_account') && $request->create_account == '1') {
                $validationRules['customer_password'] = 'required|string|min:8|confirmed';
            }

            $request->validate($validationRules);

            $cart = session()->get('cart', []);
            if (empty($cart)) {
                return redirect()->route('checkout.cart')->with('error', 'Tu carrito está vacío.');
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $appliedCoupon = session()->get('applied_coupon');
            $discountAmount = 0;
            $couponId = null;

            if ($appliedCoupon) {
                $discountAmount = ($subtotal * $appliedCoupon->discount_percentage) / 100;
                $couponId = $appliedCoupon->id;
            }

            $taxableAmount = $subtotal - $discountAmount;
            $taxes = $taxableAmount * 0.16; // 16% IVA
            $total = $taxableAmount + $taxes;

            // Guardar datos del cliente en sesión para usar después
            session([
                'checkout_customer_email' => $request->customer_email,
                'checkout_customer_name' => $request->customer_name,
                'checkout_customer_password' => $request->customer_password,
            ]);

            // Procesar pago con Openpay
            if ($request->payment_method === 'openpay') {
                // Validar que las credenciales de Openpay estén configuradas
                $merchantId = config('services.openpay.merchant_id');
                $privateKey = config('services.openpay.private_key');

                if (empty($merchantId) || empty($privateKey)) {
                    \Log::error('Credenciales de Openpay no configuradas');
                    return back()->with('error', 'Error de configuración: Las credenciales de Openpay no están configuradas. Por favor, contacta al administrador.');
                }

                $ip_user = $request->ip();

                $openpay = Openpay::getInstance($merchantId, $privateKey, 'MX', $ip_user);
                Openpay::setSandboxMode(config('services.openpay.sandbox_mode', false));

                $customer = [
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                ];

                $redirectUrl = route('checkout.callback');

                $chargeRequest = [
                    'method' => 'card',
                    'source_id' => $request->openpay_token,
                    'amount' => number_format($total, 2, '.', ''),
                    'currency' => 'MXN',
                    'description' => 'Compra de boletos para evento',
                    'device_session_id' => $request->device_session_id,
                    'customer' => $customer,
                    'redirect_url' => $redirectUrl,
                ];

                $charge = $openpay->charges->create($chargeRequest);

                // Si la respuesta contiene una URL, es un cargo 3DS
                if (isset($charge->payment_method->url)) {
                    session(['openpay_charge_id' => $charge->id]);
                    return redirect()->away($charge->payment_method->url);
                }

                // Si no hay URL, fue un cargo directo
                if ($charge->status === 'completed') {
                    return $this->finalizeOrderAndCreateTickets($charge, $cart, $couponId, $subtotal, $discountAmount, $total, $taxes, $request->customer_email, $request->customer_name, $request);
                } else {
                    return back()->with('error', 'El pago no fue completado. Estado: ' . $charge->status);
                }
            }

            return back()->with('error', 'Método de pago no válido.');

        } catch (\OpenpayApiTransactionError $e) {
            \Log::error('Error de transacción Openpay', [
                'description' => $e->getDescription(),
                'error_code' => $e->getErrorCode(),
            ]);
            return back()->with('error', 'Error con la tarjeta: ' . $e->getDescription());
        } catch (\Exception $e) {
            \Log::error('Error al procesar pago: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al procesar tu pago.');
        }
    }

    /**
     * Maneja el callback de Openpay después de la autenticación 3D Secure.
     * El usuario es redirigido aquí por su banco.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handlePaymentCallback(Request $request)
    {
        \Log::info('=== INICIO DEL CALLBACK DE PAGO ===');
        $chargeIdFromSession = session('openpay_charge_id');
        $chargeIdFromRequest = $request->input('id');

        // Verificación de seguridad básica
        if (!$chargeIdFromSession || $chargeIdFromSession !== $chargeIdFromRequest) {
           \Log::error('Error de validación en callback: ID de cargo no coincide o no existe en sesión.');
            return redirect()->route('checkout.cart')->with('error', 'Hubo un problema al verificar tu pago.');
        }

        try {
            // Validar que las credenciales de Openpay estén configuradas
            $merchantId = config('services.openpay.merchant_id');
            $privateKey = config('services.openpay.private_key');

            if (empty($merchantId) || empty($privateKey)) {
                \Log::error('❌ Credenciales de Openpay no configuradas en callback', [
                    'merchant_id_set' => !empty($merchantId),
                    'private_key_set' => !empty($privateKey),
                ]);
                return redirect()->route('checkout.cart')->with('error', 'Error de configuración: Las credenciales de Openpay no están configuradas.');
            }

            $ip_user = $request->ip();

            $openpay = Openpay::getInstance($merchantId, $privateKey, 'MX', $ip_user);
            Openpay::setSandboxMode(config('services.openpay.sandbox_mode', false));

            // Consultar el estado final del cargo en Openpay
            $charge = $openpay->charges->get($chargeIdFromSession);

            if ($charge->status === 'completed') {
                $cart = session()->get('cart', []);
                $appliedCoupon = session()->get('applied_coupon');
                $customerEmail = session('checkout_customer_email');
                $customerName = session('checkout_customer_name');

                // Recalcular totales
                $subtotal = 0;
                foreach ($cart as $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                }
                $discountAmount = 0;
                $couponId = null;
                if ($appliedCoupon) {
                    $discountAmount = ($subtotal * $appliedCoupon->discount_percentage) / 100;
                    $couponId = $appliedCoupon->id;
                }
                $taxableAmount = $subtotal - $discountAmount;
                $taxes = $taxableAmount * 0.16;
                $total = $taxableAmount + $taxes;

                return $this->finalizeOrderAndCreateTickets($charge, $cart, $couponId, $subtotal, $discountAmount, $total, $taxes, $customerEmail, $customerName, $request);
            } else {
                \Log::warning('Pago 3DS falló o fue declinado', ['status' => $charge->status]);
                return redirect()->route('checkout.cart')->with('error', 'La autenticación del pago falló. Por favor, intenta de nuevo.');
            }

        } catch (\Exception $e) {
            \Log::error('Error en handlePaymentCallback: ' . $e->getMessage());
            return redirect()->route('checkout.cart')->with('error', 'Ocurrió un error al verificar el resultado de tu pago.');
        }
    }

    /**
     * Método privado para crear la orden, el pago y los tickets.
     * Se llama únicamente después de que un cargo de Openpay ha sido confirmado como 'completed'.
     * Incluye transacciones DB para rollback en caso de errores y envío de correos.
     *
     * @param object $charge El objeto del cargo exitoso de Openpay.
     * @param array $cart El carrito de compras de la sesión.
     * @param mixed $couponId El ID del cupón aplicado.
     * @param float $subtotal
     * @param float $discountAmount
     * @param float $total
     * @param float $taxes
     * @param string|null $customerEmail
     * @param string|null $customerName
     * @param string|null $customerPassword Contraseña opcional si se crea cuenta
     * @return \Illuminate\Http\RedirectResponse
     */
    private function finalizeOrderAndCreateTickets($charge, $cart, $couponId, $subtotal, $discountAmount, $total, $taxes, $customerEmail = null, $customerName = null, Request $request)
    {
        // Validar que el cargo esté completado
        if ($charge->status !== 'completed') {
            Log::error('Intento de finalizar orden con cargo no completado', [
                'charge_id' => $charge->id,
                'status' => $charge->status
            ]);
            return redirect()->route('checkout.cart')->with('error', 'El pago no fue completado correctamente.');
        }

        // Validar que el carrito no esté vacío
        if (empty($cart)) {
            Log::error('Intento de finalizar orden con carrito vacío', [
                'charge_id' => $charge->id
            ]);
            return redirect()->route('checkout.cart')->with('error', 'El carrito está vacío.');
        }

        // Obtener datos del cliente de los parámetros o sesión
        if (!$customerEmail) {
            $customerEmail = session('checkout_customer_email');
        }
        if (!$customerName) {
            $customerName = session('checkout_customer_name');
        }

        // Obtener contraseña desde sesión (nunca llega por callback)
        $sessionPassword = session('checkout_customer_password');

        // Si no hay datos, intentar obtener del usuario autenticado
        if (!$customerEmail && auth()->check()) {
            $customerEmail = auth()->user()->email;
            $customerName = auth()->user()->name;
        }
        // Si aún no hay datos, usar datos del charge de Openpay
        if (!$customerEmail && isset($charge->customer)) {
            $customerEmail = $charge->customer->email ?? null;
            $customerName = $charge->customer->name ?? 'Cliente';
        }
        // Buscar o crear usuario por email
        $user = null;
        if ($customerEmail) {
            $user = User::where('email', $customerEmail)->first();
            if (!$user) {
                // Determinar contraseña: formulario → sesión → default
                if ($sessionPassword) {
                    $password = Hash::make($sessionPassword);
                } else {
                    $password = Hash::make('accesoconcedido123');
                }

                // Crear usuario nuevo
                $user = User::create([
                    'name' => $customerName ?? 'Cliente',
                    'last_name' => '',
                    'email' => $customerEmail,
                    'password' => $password,
                    'role' => 'viewer',
                    'verified' => false,
                ]);
            } else {
                // Si el usuario ya existe pero se proporcionó una contraseña nueva, actualizarla
                if ($request->has('customer_password') && $request->customer_password) {
                    $user->update([
                        'password' => Hash::make($request->customer_password)
                    ]);

                    // Generar y enviar código de verificación para nuevo usuario
                    $this->sendVerificationCodeToUser($user);
                } else {
                    // Si el usuario ya existe pero se proporcionó una contraseña nueva, actualizarla
                    if ($customerPassword) {
                        $user->update([
                            'password' => Hash::make($customerPassword)
                        ]);
                    }
                }
            }

            $event_id_json = [];
            foreach ($cart as $item) {
                $event_id_json[] = $item['event_id'];
            }
            $event_id_json = array_unique($event_id_json); // Guardar solo IDs únicos

            // --- Crear la Orden ---
            $order = Order::create([
                'id' => Str::uuid(),
                'user_id' => $user ? $user->id : null,
                'event_id' => json_encode($event_id_json),
                'coupon_id' => $couponId,
                'state_id' => 4, // Asumiendo que 4 es un estado válido
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'taxes' => $taxes,
                'status' => 'completed',
            ]);

            // Registrar el pago
            $payment = Payment::create([
                'state_id' => 4,
                'coupon_id' => $couponId,
                'order_id' => $order->id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'taxes' => $taxes,
                'payment_gateway' => 'openpay',
                'gateway_transaction_id' => $charge->id,
                'gateway_authorization' => $charge->authorization ?? null,
            ]);

            // Validar que el pago se creó correctamente
            if (!$payment) {
                throw new \Exception('Error al registrar el pago');
            }

            // Crear items de la orden y tickets
            $ticketsCreated = [];
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $item['ticket_type_id'],
                    'quantity' => $item['quantity'],
                ]);

                for ($i = 0; $i < $item['quantity']; $i++) {
                    $ticket = Ticket::create([
                        'id' => Str::uuid(),
                        'order_id' => $order->id,
                        'ticket_types_id' => $item['ticket_type_id'],
                        'event_id' => $item['event_id'],
                        'used' => false,
                    ]);

                    // Generar QR en memoria (sin archivo local)
                    $qrBinary = QrCode::format('png')
                        ->size(300)
                        ->generate($ticket->id);

                    // Subir a S3 directamente
                    $qrS3Url = $this->saveImages($qrBinary, 'tickets_qr', $ticket->id);

                    // Guardar URL de S3 en el ticket
                    $ticket->qr_url = $qrS3Url;
                    $ticket->save();

                    $ticketsCreated[] = $ticket;
                }
            }

            // Validar que se crearon tickets
            if (empty($ticketsCreated)) {
                throw new \Exception('No se pudieron crear los tickets');
            }

            // Confirmar transacción
            DB::commit();

            Log::info('Orden finalizada exitosamente', [
                'order_id' => $order->id,
                'user_id' => $user?->id,
                'tickets_count' => count($ticketsCreated),
                'charge_id' => $charge->id
            ]);

            // Enviar correo con PDFs de boletos (en background)
            if ($customerEmail) {
                try {
                    SendTicketPurchaseEmail::dispatch($order);
                    Log::info('Job de envío de correo despachado', [
                        'order_id' => $order->id,
                        'email' => $customerEmail
                    ]);
                } catch (\Exception $e) {
                    // No fallar la compra si falla el envío de correo, solo loguear
                    Log::error('Error al despachar job de envío de correo', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Limpiar sesión
            session()->forget([
                'cart', 
                'applied_coupon', 
                'openpay_charge_id', 
                'checkout_customer_email', 
                'checkout_customer_name',
                'checkout_customer_password'
            ]);
            
            // Guardar flag en sesión para limpiar localStorage en el frontend
            session()->put('clear_cart_localstorage', true);

            // Si hay usuario, iniciar sesión automáticamente
            if ($user) {
                Auth::login($user);
                
                // Si el usuario no está verificado, redirigir a verificación
                if (!$user->verified_at) {
                    return redirect()->route('verify.email')
                        ->with('success', '¡Compra realizada con éxito! Por favor, verifica tu correo electrónico para recibir tus boletos.')
                        ->with('clear_cart_localstorage', true);
                }
                
                // Si está verificado, redirigir a boletos
                return redirect()->route('tickets.my')
                    ->with('success', '¡Compra realizada con éxito! Revisa tu correo para descargar tus boletos.')
                    ->with('clear_cart_localstorage', true);
            }
            
            // Si no hay usuario, redirigir a página de éxito con el ID de la orden
            return redirect()->route('checkout.success', $order)
                ->with('success', '¡Compra realizada con éxito! Revisa tu correo para descargar tus boletos.')
                ->with('clear_cart_localstorage', true);

        } catch (\Exception $e) {
            // Rollback en caso de error
            DB::rollBack();
            
            Log::error('Error al finalizar orden y crear tickets', [
                'charge_id' => $charge->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Intentar reembolsar en Openpay si es posible
            try {
                $merchantId = config('services.openpay.merchant_id');
                $privateKey = config('services.openpay.private_key');
                if ($merchantId && $privateKey) {
                    $openpay = Openpay::getInstance($merchantId, $privateKey, 'MX');
                    // Nota: Openpay no permite reembolsos automáticos, esto requeriría intervención manual
                    Log::warning('Orden falló después de pago exitoso - requiere reembolso manual', [
                        'charge_id' => $charge->id,
                        'order_error' => $e->getMessage()
                    ]);
                }
            } catch (\Exception $refundError) {
                Log::error('Error al intentar procesar reembolso', [
                    'charge_id' => $charge->id,
                    'error' => $refundError->getMessage()
                ]);
            }

            return redirect()->route('checkout.cart')
                ->with('error', 'Ocurrió un error al procesar tu compra. El pago fue procesado pero no se pudieron crear los boletos. Por favor, contacta a soporte con el ID de transacción: ' . $charge->id);
        }
    }

        // Limpiar sesión
        session()->forget(['cart', 'applied_coupon', 'openpay_charge_id', 'checkout_customer_email', 'checkout_customer_name']);

        // Guardar flag en sesión para limpiar localStorage en el frontend
        session()->put('clear_cart_localstorage', true);
    /**
     * Enviar código de verificación a un usuario nuevo
     */
    private function sendVerificationCodeToUser(User $user)
    {
        // Generar código de 6 dígitos
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalidar códigos anteriores del mismo tipo
        UsersCode::where('user_id', $user->id)
            ->where('type', 'email_verification')
            ->where('used', false)
            ->update(['used' => true]);

        // Crear nuevo código (siempre se guarda, incluso si falla el correo)
        UsersCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'email_verification',
            'used' => false,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // Guardar código en el usuario (opcional, para referencia rápida)
        $user->verification_code = $code;
        $user->save();

        // Intentar enviar correo con el código
        try {
            // Validar configuración de correo antes de intentar enviar
            $mailHost = config('mail.mailers.smtp.host');
            $mailPort = config('mail.mailers.smtp.port');
            
            if (empty($mailHost) || empty($mailPort)) {
                Log::warning('Configuración de correo incompleta', [
                    'user_id' => $user->id,
                    'mail_host' => $mailHost,
                    'mail_port' => $mailPort
                ]);
                // No lanzar excepción, solo loguear
                return;
            }

            \Illuminate\Support\Facades\Mail::send('emails.verification-code', [
                'user' => $user,
                'code' => $code,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Código de verificación de email')
                    // El from se toma de config/mail.php (debe ser del dominio SMTP)
                    ->replyTo(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Código de verificación enviado a nuevo usuario', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

        } catch (\Swift_TransportException $e) {
            // Error de conexión SMTP
            Log::error('Error de conexión SMTP al enviar código de verificación', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'suggestion' => 'Verifica la configuración SMTP en .env (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD). Para Zoho usa puerto 587 (TLS) o 465 (SSL).'
            ]);
            // No lanzar excepción para no interrumpir el flujo de compra
        } catch (\Exception $e) {
            Log::error('Error al enviar código de verificación', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // No lanzar excepción para no interrumpir el flujo de compra
        }

        // Si no hay usuario, redirigir a página de éxito con el ID de la orden
        return redirect()->route('checkout.success', $order)->with('success', '¡Compra realizada con éxito!')->with('clear_cart_localstorage', true);
    }

    /**
     * Aplicar cupón de descuento
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Código de cupón no válido o expirado.'
            ], 400);
        }

        // Guardar cupón en sesión
        session()->put('applied_coupon', $coupon);

        // Calcular descuento
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = ($subtotal * $coupon->discount_percentage) / 100;
        $taxes = ($subtotal - $discount) * 0.16;
        $total = $subtotal - $discount + $taxes;

        return response()->json([
            'success' => true,
            'message' => 'Cupón aplicado exitosamente.',
            'coupon' => [
                'code' => $coupon->code,
                'discount_percentage' => $coupon->discount_percentage,
                'discount_amount' => $discount
            ],
            'totals' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'taxes' => $taxes,
                'total' => $total
            ]
        ]);
    }

    /**
     * Remover cupón de descuento
     */
    public function removeCoupon()
    {
        session()->forget('applied_coupon');

        // Recalcular totales sin cupón
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $taxes = $subtotal * 0.16;
        $total = $subtotal + $taxes;

        return response()->json([
            'success' => true,
            'message' => 'Cupón removido exitosamente.',
            'totals' => [
                'subtotal' => $subtotal,
                'discount' => 0,
                'taxes' => $taxes,
                'total' => $total
            ]
        ]);
    }

    /**
     * Página de éxito
     */
    public function success(Order $order)
    {
        $order->load(['orderItems.ticketType', 'tickets', 'coupon']);

        return view('checkout.success', compact('order'));
    }

    /**
     * Mostrar detalles de una orden
     */
    public function showOrder(Order $order)
    {
        $order->load(['orderItems.ticketType', 'tickets', 'coupon']);

        return view('checkout.order', compact('order'));
    }

    /**
     * Login o registro rápido desde el checkout
     */
    public function quickLoginOrRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'action' => 'required|in:login,register'
        ]);

        if ($request->action === 'register') {
            // Verificar si el usuario ya existe
            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este correo electrónico ya está registrado. Por favor, inicia sesión.'
                ], 400);
            }

            // Crear nuevo usuario
            $user = User::create([
                'name' => $request->name,
                'last_name' => '', // Se puede dejar vacío o pedir después
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'viewer',
                'verified' => false,
            ]);

            Auth::login($user);

            return response()->json([
                'success' => true,
                'message' => 'Cuenta creada exitosamente. Redirigiendo...',
                'redirect' => route('checkout.checkout')
            ]);
        } else {
            // Login
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return response()->json([
                    'success' => true,
                    'message' => 'Sesión iniciada exitosamente. Redirigiendo...',
                    'redirect' => route('checkout.checkout')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Las credenciales proporcionadas no coinciden con nuestros registros.'
            ], 401);
        }
    }
}
