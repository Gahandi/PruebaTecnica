<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(): JsonResponse
    {
        $events = Event::with('ticketTypes')->get();
        
        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    /**
     * Display the specified event.
     */
    public function show(string $id): JsonResponse
    {
        $event = Event::with('ticketTypes')->find($id);
        
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Evento no encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }
}
