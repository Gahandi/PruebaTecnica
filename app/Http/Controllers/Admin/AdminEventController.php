<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;

class AdminEventController extends Controller
{
    /**
     * Lista de eventos
     */
    public function index()
    {
        $events = Event::with(['space', 'state', 'type_event'])
            ->withCount(['tickets_events', 'orders'])
            ->latest()
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Eliminar evento (Soft Delete)
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Evento eliminado correctamente.');
    }
}
