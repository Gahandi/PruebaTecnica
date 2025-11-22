<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view events')->only(['index', 'show']);
        $this->middleware('permission:create events')->only(['create', 'store']);
        $this->middleware('permission:edit events')->only(['edit', 'update']);
        $this->middleware('permission:delete events')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with(['space', 'ticketTypes'])->get();
        // Devuelve la vista con la lista de eventos.
        // Debes crear esta vista en: resources/views/admin/events/index.blade.php
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        // Creación del nuevo evento.
        Event::create($request->all());

        // Redirecciona a la lista de eventos con un mensaje de éxito.
        return redirect()->route('admin.events.index')
                         ->with('success', 'Evento creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Devuelve la vista con el formulario de edición.
        // Debes crear esta vista en: resources/views/admin/events/edit.blade.php
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Validación de los datos del formulario.
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
        ]);

        // Actualización del evento.
        $event->update($request->all());

        // Redirecciona a la lista de eventos con un mensaje de éxito.
        return redirect()->route('admin.events.index')
                         ->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Eliminación del evento.
        $event->delete();

        // Redirecciona a la lista de eventos con un mensaje de éxito.
        return redirect()->route('admin.events.index')
                         ->with('success', 'Evento eliminado correctamente.');
    }
}
