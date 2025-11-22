<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
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

class CheckoutController extends Controller
{
    use S3ImageManager;

    public function __construct()
    {
        $this->middleware('auth')->except(['addToCart', 'cart']);
    }

    /**
     * Mostrar el carrito de compras
     */
    public function cart()
    {
        $cart = \App\Helpers\CartHelper::getCartWithEventInfo();

        $events = Event::with('ticketTypes')->get();

        return view('checkout.cart', compact('cart', 'events'));
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
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticketType = TicketType::findOrFail($request->ticket_type_id);
        $cart = session()->get('cart', []);

        // Verificar disponibilidad
        $currentQuantity = isset($cart[$ticketType->id]) ? $cart[$ticketType->id]['quantity'] : 0;
        $totalQuantity = $currentQuantity + $request->quantity;

        if ($totalQuantity > $ticketType->quantity) {
            return back()->with('error', 'No hay suficientes boletos disponibles.');
        }

        if (isset($cart[$ticketType->id])) {
            $cart[$ticketType->id]['quantity'] += $request->quantity;
        } else {
            $cart[$ticketType->id] = [
                'ticket_type' => $ticketType,
                'quantity' => $request->quantity,
                'price' => $ticketType->price,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Boletos agregados al carrito.');
    }

    /**
     * Actualizar cantidad en el carrito
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);

        if ($request->quantity == 0) {
            unset($cart[$request->ticket_type_id]);
        } else {
            $ticketType = TicketType::findOrFail($request->ticket_type_id);

            if ($request->quantity > $ticketType->quantity) {
                return back()->with('error', 'No hay suficientes boletos disponibles.');
            }

            $cart[$request->ticket_type_id]['quantity'] = $request->quantity;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Carrito actualizado.');
    }

    /**
     * Remover item del carrito
     */
    public function removeFromCart($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);

            return back()->with('success', 'Item removido del carrito.');
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
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
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
                    return $this->finalizeOrderAndCreateTickets($charge, $cart, $couponId, $subtotal, $discountAmount, $total, $taxes);
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

                return $this->finalizeOrderAndCreateTickets($charge, $cart, $couponId, $subtotal, $discountAmount, $total, $taxes);
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
     *
     * @param object $charge El objeto del cargo exitoso de Openpay.
     * @param array $cart El carrito de compras de la sesión.
     * @param mixed $couponId El ID del cupón aplicado.
     * @param float $subtotal
     * @param float $discountAmount
     * @param float $total
     * @param float $taxes
     * @return \Illuminate\Http\RedirectResponse
     */
    private function finalizeOrderAndCreateTickets($charge, $cart, $couponId, $subtotal, $discountAmount, $total, $taxes)
    {

        $event_id_json = [];
        foreach ($cart as $item) {
            $event_id_json[] = $item['event_id'];
        }
        $event_id_json = array_unique($event_id_json); // Guardar solo IDs únicos

        // --- Crear la Orden ---
        $order = Order::create([
            'id' => Str::uuid(),
            'user_id' => auth()->id(),
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
        Payment::create([
            'state_id' => 4,
            'coupon_id' => $couponId,
            'order_id' => $order->id,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'taxes' => $taxes,
            'payment_gateway' => 'openpay',
            'gateway_transaction_id' => $charge->id,
            'gateway_authorization' => $charge->authorization,
        ]);

        // Crear items de la orden y tickets
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

            }
        }

        // Limpiar sesión
        session()->forget(['cart', 'applied_coupon', 'openpay_charge_id']);

        // Redirigir al usuario a su página de boletos
        return redirect()->route('tickets.my')->with('success', '¡Compra realizada con éxito! Puedes ver tus boletos aquí.');
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
}
