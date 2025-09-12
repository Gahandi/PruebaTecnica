<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use App\Models\Event;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view ticket types')->only(['index', 'show']);
        $this->middleware('permission:create ticket types')->only(['create', 'store']);
        $this->middleware('permission:edit ticket types')->only(['edit', 'update']);
        $this->middleware('permission:delete ticket types')->only(['destroy']);
    }

    /**
     * Muestra una lista de todos los tipos de boletos.
     */
    public function index(Request $request)
    {
        $query = TicketType::with('event');
        
        // Filtrar por evento si se especifica
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }
        
        $ticketTypes = $query->get();
        $events = Event::all();
        
        return view('admin.ticket_types.index', compact('ticketTypes', 'events'));
    }

    /**
     * Muestra el formulario para crear un nuevo tipo de boleto.
     */
    public function create()
    {
        // Pasamos todos los eventos a la vista para poder seleccionarlos en un dropdown.
        $events = Event::all();
        return view('admin.ticket_types.create', compact('events'));
    }

    /**
     * Guarda un nuevo tipo de boleto en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        TicketType::create($request->all());

        return redirect()->route('admin.ticket-types.index')
                         ->with('success', 'Tipo de boleto creado correctamente.');
    }

    /**
     * Muestra los detalles de un tipo de boleto específico.
     */
    public function show(TicketType $ticketType)
    {
        return view('admin.ticket_types.show', compact('ticketType'));
    }

    /**
     * Muestra el formulario para editar un tipo de boleto.
     */
    public function edit(TicketType $ticketType)
    {
        $events = Event::all();
        return view('admin.ticket_types.edit', compact('ticketType', 'events'));
    }

    /**
     * Actualiza un tipo de boleto en la base de datos.
     */
    public function update(Request $request, TicketType $ticketType)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticketType->update($request->all());

        return redirect()->route('admin.ticket-types.index')
                         ->with('success', 'Tipo de boleto actualizado correctamente.');
    }

    /**
     * Elimina un tipo de boleto de la base de datos.
     */
    public function destroy(TicketType $ticketType)
    {
        $ticketType->delete();

        return redirect()->route('admin.ticket-types.index')
                         ->with('success', 'Tipo de boleto eliminado correctamente.');
    }
}
