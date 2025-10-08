<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CheckoutController extends Controller
{
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
    public function removeFromCart($ticketTypeId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$ticketTypeId]);
        session()->put('cart', $cart);

        return back()->with('success', 'Item removido del carrito.');
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
     * Procesar el pago - VERSIÓN SIMPLE PARA DEBUG
     */
    public function processPayment(Request $request)
    {
        \Log::info('=== SIMPLE PAYMENT TEST ===');
        \Log::info('User ID: ' . auth()->id());
        \Log::info('User authenticated: ' . (auth()->check() ? 'Yes' : 'No'));

        try {
            $cart = session()->get('cart', []);

            \Log::info('Cart contents in processPayment', $cart);
            \Log::info('Cart count', ['count' => count($cart)]);

            if (empty($cart)) {
                \Log::info('Cart is empty, redirecting to cart');
                return redirect()->route('checkout.cart')->with('error', 'Tu carrito está vacío.');
            }

        // Calcular totales
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Aplicar cupón si existe
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

            // Crear la orden
            $order = Order::create([
                'user_id' => auth()->id(),
                'event_id' => 1, // Temporal
                'coupon_id' => $couponId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'taxes' => $taxes,
                'status' => 'completed',
            ]);

            \Log::info('Order created successfully', ['order_id' => $order->id]);

            // Crear tickets para cada item del carrito
            foreach ($cart as $ticketTypeId => $item) {
                // Crear item de la orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $ticketTypeId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Crear tickets individuales
                for ($i = 0; $i < $item['quantity']; $i++) {
                    // Generar QR code en formato PNG para mejor compatibilidad con PDF
                    $ticketId = Str::uuid();
                    $qrCodeData = route('tickets.checkin', $ticketId);

                    try {
                        // Generar QR code como SVG (no requiere imagick ni GD)
                        $qrCodeSvg = QrCode::format('svg')
                            ->size(200)
                            ->margin(1)
                            ->errorCorrection('M')
                            ->generate($qrCodeData);

                        // Guardar QR code como archivo SVG
                        $qrCodePath = 'qrcodes/ticket_' . $ticketId . '.svg';

                        if (!file_exists(public_path('qrcodes'))) {
                            mkdir(public_path('qrcodes'), 0755, true);
                        }

                        file_put_contents(public_path($qrCodePath), $qrCodeSvg);

                        \Log::info('QR Code generated successfully', ['path' => $qrCodePath]);

                    } catch (\Exception $e) {
                        \Log::error('QR Code generation failed: ' . $e->getMessage());
                        // Usar un placeholder simple como texto
                        $qrCodePath = 'qrcodes/ticket_' . $ticketId . '.txt';

                        if (!file_exists(public_path('qrcodes'))) {
                            mkdir(public_path('qrcodes'), 0755, true);
                        }

                        // Crear un archivo de texto con la información del QR
                        file_put_contents(public_path($qrCodePath), "QR Code Data: " . $qrCodeData);
                    }

                    // Crear el ticket con el QR URL ya generado
                    \Log::info('Creating ticket with ID: ' . $ticketId);
                    $ticket = Ticket::create([
                        'id' => $ticketId,
                        'order_id' => $order->id,
                        'ticket_type_id' => $ticketTypeId,
                        'qr_code' => 'ticket-' . $ticketId,
                        'qr_url' => $qrCodePath,
                    ]);
                    \Log::info('Ticket created with actual ID: ' . $ticket->id);

                    \Log::info('Ticket created successfully', [
                        'ticket_id' => $ticket->id,
                        'order_id' => $order->id,
                        'ticket_type_id' => $ticketTypeId
                    ]);
                }
            }

            // Limpiar carrito y cupón
            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('tickets.my')->with('success', '¡Compra realizada con éxito! Puedes ver tus boletos aquí.');

        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
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
