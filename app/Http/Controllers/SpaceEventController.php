<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Space;
use App\Models\TicketType;
use App\Models\TypeEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\S3ImageManager;

class SpaceEventController extends Controller
{
    use S3ImageManager; 

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
        try {
            $space = $request->get('space');
        
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date|after:now',
                'address' => 'required|string|max:255',
                'coordinates' => 'nullable|string|max:255',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type_event_id' => 'required|exists:type_events,id',
                'ticket_types' => 'required|array|min:1',
                'ticket_types.*.name' => 'required|string|max:255',
                'ticket_types.*.price' => 'required|numeric|min:0',
                'ticket_types.*.quantity' => 'required|integer|min:1',
            ]);
        
            $eventData = [
                'name' => $request->name,
                'description' => $request->description,
                'date' => $request->date,
                'address' => $request->address,
                'coordinates' => $request->coordinates,
                'spaces_id' => $space->id,
                'type_events_id' => $request->type_event_id,
                'state_id' => 1, // Estado "Activo"
                'slug' => Str::slug($request->name),
                'active' => true,
                'agenda' => $request->agenda ?? 'N/A',
                'banner_app' => 'default.jpg',
            ];
        
            // ----- Subir archivos a S3 -----
            if ($request->hasFile('banner')) {
                $bannerFile = $request->file('banner');
                $bannerBase64 = base64_encode(file_get_contents($bannerFile));
                $eventData['banner'] = $this->saveImages($bannerBase64, 'events/banners', $space->id . '_' . time());
            } else {
                $eventData['banner'] = 'https://via.placeholder.com/1200x400?text=Sin+Banner';
            }
        
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $imageBase64 = base64_encode(file_get_contents($imageFile));
                $eventData['image'] = $this->saveImages($imageBase64, 'events/images', $space->id . '_' . time());
            } else {
                $eventData['image'] = 'https://via.placeholder.com/800x600?text=Sin+Imagen';
            }
        
            if ($request->hasFile('icon')) {
                $iconFile = $request->file('icon');
                $iconBase64 = base64_encode(file_get_contents($iconFile));
                $eventData['icon'] = $this->saveImages($iconBase64, 'events/icons', $space->id . '_' . time());
            } else {
                $eventData['icon'] = 'https://via.placeholder.com/200x200?text=Sin+Icono';
            }
        
            // Guardar el evento
            $event = Event::create($eventData);
        
            // Crear tipos de boletos y asociarlos al evento
            foreach ($request->ticket_types as $ticketTypeData) {
                $ticketType = TicketType::firstOrCreate([
                    'name' => $ticketTypeData['name']
                ]);
        
                $event->ticketTypes()->attach($ticketType->id, [
                    'price' => $ticketTypeData['price'],
                    'quantity' => $ticketTypeData['quantity']
                ]);
            }
        
            return redirect()
                ->route('spaces.profile', $space->subdomain)
                ->with('success', 'Evento creado exitosamente y las imágenes fueron subidas a S3.');
        } catch (\Exception $e) {
            // Registrar el error en el log
            \Log::error('Error al crear el evento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        
            // Retornar con mensaje de error
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al crear el evento. Por favor, intenta nuevamente.');
        } 
    }
}