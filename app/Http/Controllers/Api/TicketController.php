<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    /**
     * Display the specified ticket.
     */
    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::with(['order.event', 'order.items.ticketType', 'checkin'])
            ->find($id);
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Validate ticket for check-in.
     */
    public function validateTicket(string $id): JsonResponse
    {
        $ticket = Ticket::with(['order.event', 'checkin'])->find($id);
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        if ($ticket->used) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already used',
                'used_at' => $ticket->updated_at,
            ], 400);
        }

        //Marcar como usado
        $ticket->update(['used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket valid',
            'data' => [
                'ticket' => $ticket,
                'event' => $ticket->order->event->name ?? null,
                'used_at' => now()->toDateTimeString(),
            ]
        ]);
    }
}
