<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Space;
use Illuminate\Http\Request;

class SpaceEventController extends Controller
{
    public function show(Request $request, $subdomain, Event $event)
    {
        // Verificar que el evento pertenece al espacio correcto
        $space = Space::where('subdomain', $subdomain)->first();
        
        if (!$space || $event->spaces_id !== $space->id) {
            abort(404, 'Evento no encontrado');
        }
        
        return view('events.show', compact('event', 'space'));
    }
}
