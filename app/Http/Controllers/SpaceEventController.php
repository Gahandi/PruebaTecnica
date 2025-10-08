<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Space;
use App\Models\TicketType;
use App\Models\TypeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpaceEventController extends Controller
{
    public function show(Request $request, $subdomain, Event $event)
    {
        // Verificar que el evento pertenece al espacio correcto
        $space = Space::where('subdomain', $subdomain)->first();

        if (!$space || $event->spaces_id !== $space->id) {
            abort(404, 'Evento no encontrado');
        }

        // Cargar la relación ticketTypes con información de la tabla intermedia
        $event->load(['ticketTypes' => function($query) {
            $query->withPivot('quantity', 'price');
        }]);

        return view('events.show', compact('event', 'space'));
    }

    public function create(Request $request, $subdomain)
    {
        // El espacio ya está disponible en la request por el middleware
        $space = $request->get('space');

        $ticketTypes = TicketType::all();
        $typeEvents = TypeEvent::all();

        return view('spaces.events.create', compact('space', 'ticketTypes', 'typeEvents'));
    }

    public function store(Request $request, $subdomain)
    {
        // El espacio ya está disponible en la request por el middleware
        $space = $request->get('space');

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after:now',
            'address' => 'required|string|max:255',
            'coordinates' => 'nullable|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type_event_id' => 'required|exists:type_events,id',
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.name' => 'required|string|max:255',
            'ticket_types.*.price' => 'required|numeric|min:0',
            'ticket_types.*.quantity' => 'required|integer|min:1',
        ]);

        // Crear el evento
        $eventData = [
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
            'address' => $request->address,
            'coordinates' => $request->coordinates,
            'spaces_id' => $space->id,
            'type_events_id' => $request->type_event_id,
            'state_id' => 1, // Estado por defecto "Activo"
            'slug' => Str::slug($request->name),
            'active' => true,
        ];

        // Manejar upload de banner
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('events/banners', 'public');
            $eventData['banner'] = $bannerPath;
        } else {
            $eventData['banner'] = 'test.jpg';
        }

        // Manejar upload de imagen
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events/images', 'public');
            $eventData['image'] = $imagePath;
        } else {
            $eventData['image'] = 'test.jpg';
        }

        // Asignar campos de imagen por defecto
        $eventData['banner_app'] = 'test.jpg';
        $eventData['icon'] = 'test.jpg';

        $event = Event::create($eventData);

        // Crear tipos de boletos y asociarlos al evento
        foreach ($request->ticket_types as $ticketTypeData) {
            // Buscar o crear el tipo de boleto
            $ticketType = TicketType::firstOrCreate([
                'name' => $ticketTypeData['name']
            ]);

            // Asociar el tipo de boleto al evento con precio y cantidad específicos
            $event->ticketTypes()->attach($ticketType->id, [
                'price' => $ticketTypeData['price'],
                'quantity' => $ticketTypeData['quantity']
            ]);
        }

        return redirect()->route('spaces.profile', $space->subdomain)
                        ->with('success', 'Evento creado exitosamente');
    }
}
