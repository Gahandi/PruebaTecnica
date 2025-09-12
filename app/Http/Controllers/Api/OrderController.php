<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Verificar disponibilidad de tickets
            $event = Event::findOrFail($request->event_id);
            $total = 0;
            $orderItems = [];

            foreach ($request->tickets as $ticketData) {
                $ticketType = TicketType::findOrFail($ticketData['ticket_type_id']);
                
                if ($ticketType->event_id != $event->id) {
                    throw new \Exception('Ticket type does not belong to this event');
                }

                if ($ticketType->quantity < $ticketData['quantity']) {
                    throw new \Exception('Not enough tickets available for ' . $ticketType->name);
                }

                $subtotal = $ticketType->price * $ticketData['quantity'];
                $total += $subtotal;

                $orderItems[] = [
                    'ticket_type_id' => $ticketType->id,
                    'quantity' => $ticketData['quantity'],
                    'price' => $ticketType->price,
                    'subtotal' => $subtotal
                ];
            }

            // Aplicar cupón si existe
            $discount = 0;
            $coupon = null;
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('expires_at', '>', now())
                    ->first();
                
                if ($coupon) {
                    $discount = ($total * $coupon->discount_percentage) / 100;
                }
            }

            $taxes = ($total - $discount) * 0.16; // 16% IVA
            $finalTotal = $total - $discount + $taxes;

            // Crear o obtener usuario temporal para la API
            $user = User::firstOrCreate(
                ['email' => $request->customer_email],
                [
                    'name' => $request->customer_name,
                    'password' => bcrypt('temporary'),
                    'role' => 'viewer'
                ]
            );

            // Crear orden
            $order = Order::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'event_id' => $event->id,
                'coupon_id' => $coupon?->id,
                'total' => $finalTotal,
                'taxes' => $taxes,
            ]);

            // Crear items de la orden
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            // Generar tickets con QR
            $tickets = [];
            foreach ($orderItems as $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $ticket = $order->tickets()->create([
                        'id' => Str::uuid(),
                        'qr_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($order->id . '-' . $i),
                    ]);
                    $tickets[] = $ticket;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $order->load(['event', 'items.ticketType', 'tickets']),
                    'tickets' => $tickets,
                    'total' => $finalTotal,
                    'discount' => $discount,
                    'taxes' => $taxes
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::with(['event', 'items.ticketType', 'tickets', 'coupon'])
            ->find($id);
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
