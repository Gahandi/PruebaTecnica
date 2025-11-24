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
                $fileContents = file_get_contents($bannerFile->getRealPath());
                
                // Detectar extensión desde MIME type (igual que saveImages)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $fileContents);
                finfo_close($finfo);
                $extensions = [
                    'image/jpeg' => 'jpg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                $extension = $extensions[$mimeType] ?? 'jpg';
                
                $productId = $space->id . '_' . time();
                $fileName = $productId . '.' . $extension;
                $bannerPath = env('S3_ENVIRONMENT') . '/events/banners/' . $fileName;
                
                // Primero guardar la imagen
                $this->saveImages($fileContents, 'events/banners', $productId);
                // Guardar la ruta relativa en la DB
                $eventData['banner'] = $bannerPath;
            } else {
                $eventData['banner'] = 'https://via.placeholder.com/1200x400?text=Sin+Banner';
            }
            
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $fileContents = file_get_contents($imageFile->getRealPath());
                
                // Detectar extensión desde MIME type (igual que saveImages)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $fileContents);
                finfo_close($finfo);
                $extensions = [
                    'image/jpeg' => 'jpg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                $extension = $extensions[$mimeType] ?? 'jpg';
                
                $productId = $space->id . '_' . time();
                $fileName = $productId . '.' . $extension;
                $imagePath = env('S3_ENVIRONMENT') . '/events/images/' . $fileName;
                
                // Primero guardar la imagen
                $this->saveImages($fileContents, 'events/images', $productId);
                // Guardar la ruta relativa en la DB
                $eventData['image'] = $imagePath;
            } else {
                $eventData['image'] = 'https://via.placeholder.com/800x600?text=Sin+Imagen';
            }
            if ($request->hasFile('icon')) {
                $iconFile = $request->file('icon');
                $fileContents = file_get_contents($iconFile->getRealPath());
                
                // Detectar extensión desde MIME type (igual que saveImages)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $fileContents);
                finfo_close($finfo);
                $extensions = [
                    'image/jpeg' => 'jpg',
                    'image/jpg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                $extension = $extensions[$mimeType] ?? 'jpg';
                
                $productId = $space->id . '_' . time();
                $fileName = $productId . '.' . $extension;
                $iconPath = env('S3_ENVIRONMENT') . '/events/icons/' . $fileName;
                
                // Primero guardar la imagen
                $this->saveImages($fileContents, 'events/icons', $productId);
                // Guardar la ruta relativa en la DB
                $eventData['icon'] = $iconPath;
            } else {
                $eventData['icon'] = 'https://via.placeholder.com/200x200?text=Sin+Icono';
            }
        
            // Guardar el evento
            $event = Event::create($eventData);
        
            // Crear tipos de boletos y asociarlos al evento
            foreach ($request->ticket_types as $ticketTypeData) {
                // Determinar si es un tipo existente (ID) o uno nuevo (Name)
                $ticketName = $ticketTypeData['name'];

                if(is_numeric($ticketName) && $ticketName > 0){
                    $ticketType = TicketType::find($ticketName);

                    //Si el id no existe loguear o saltar. 
                    if (!$ticketType) {
                        \Log::warning("Tipo de Boleto con ID $ticketName no encontrado, saltando.");
                        continue;
                    }
                }else{
                    $ticketType = TicketType::firstOrCreate(
                        ['name' => $ticketName]
                    );
                }

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
                'trace' => $e->getTraceAsString(),
                'exception' => get_class($e)
            ]);
        
            // Mensaje de error más específico si es un error de S3
            $errorMessage = 'Ocurrió un error al crear el evento. ';
            if (strpos($e->getMessage(), 'S3') !== false || strpos($e->getMessage(), 'AWS') !== false) {
                $errorMessage .= 'Error al subir las imágenes a S3. Verifica la configuración de AWS y los logs para más detalles.';
            } else {
                $errorMessage .= 'Por favor, intenta nuevamente.';
            }
        
            // Retornar con mensaje de error
            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } 
    }

        /**
     * Muestra el formulario para editar un evento existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $subdomain
     * @param  \App\Models\Event  $event
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $subdomain, Event $event)
    {
        // El espacio ya está disponible en la request por el middleware
        $space = $request->get('space');

        // 1. Verificar que el evento pertenece al espacio (por seguridad)
        if ($event->spaces_id !== $space->id) {
            abort(403, 'Acceso no autorizado a este evento.');
        }

        // 2. Cargar las relaciones necesarias
        // Cargar la relación ticketTypes con el pivot (quantity y price)
        $event->load(['ticketTypes' => function($query) {
            $query->withPivot('quantity', 'price');
        }]);

        $ticketTypes = TicketType::all();
        $typeEvents = TypeEvent::all();

        // 3. Retornar la vista de edición con los datos
        return view('spaces.events.edit', compact('space', 'event', 'ticketTypes', 'typeEvents'));
    }

    /**
     * Actualiza la información de un evento existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $subdomain
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $subdomain, Event $event)
    {
        try {
            $space = $request->get('space');

            // 1. Verificar que el evento pertenece al espacio (por seguridad)
            if ($event->spaces_id !== $space->id) {
                abort(403, 'Acceso no autorizado para editar este evento.');
            }
        
            // 2. Validar los datos
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date|after:now',
                'address' => 'required|string|max:255',
                'coordinates' => 'nullable|string|max:255',
                // Los archivos son nullable para que no sean obligatorios si ya existen
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type_event_id' => 'required|exists:type_events,id',
                'ticket_types' => 'required|array|min:1',
                'ticket_types.*.name' => 'required|string|max:255',
                'ticket_types.*.price' => 'required|numeric|min:0',
                'ticket_types.*.quantity' => 'required|integer|min:1',
            ]);
        
            // 3. Preparar los datos básicos
            $eventData = [
                'name' => $request->name,
                'description' => $request->description,
                'date' => $request->date,
                'address' => $request->address,
                'coordinates' => $request->coordinates,
                'type_events_id' => $request->type_event_id,
                // Generar nuevo slug si el nombre cambió
                'slug' => Str::slug($request->name), 
                'agenda' => $request->agenda ?? 'N/A',
            ];

            // 4. Gestión de Archivos (Subir a S3 y actualizar la DB)
            
            // Función auxiliar para subir y eliminar archivos (basado en tu lógica de store)
            $uploadAndUpdateImage = function ($fileKey, $pathSegment, $dbField, $event, $request) use (&$eventData) {
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $fileContents = file_get_contents($file->getRealPath());

                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_buffer($finfo, $fileContents);
                    finfo_close($finfo);
                    $extensions = [
                        'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png',
                        'image/gif' => 'gif', 'image/webp' => 'webp',
                    ];
                    $extension = $extensions[$mimeType] ?? 'jpg';
                    
                    $productId = $event->spaces_id . '_' . time();
                    $fileName = $productId . '.' . $extension; // <-- Crear el nombre del archivo completo
                    
                    // 1. Eliminar la imagen antigua si existe y no es una URL por defecto
                    // La ruta a eliminar debe coincidir con la ruta guardada en la DB
                    if ($event->$dbField && !Str::startsWith($event->$dbField, 'http')) {
                        $fullPath = $event->$dbField;

                        $fileNameToDelete = basename($fullPath);

                        $envPrefix = env('S3_ENVIRONMENT') . '/';

                        $folderAndFile = Str::after($fullPath, $envPrefix);

                        $folderToDelete = Str::beforeLast($folderAndFile, '/');
            
                        \Log::info("Intentando eliminar la imagen antigua de $dbField. Carpeta: $folderToDelete, Archivo: $fileNameToDelete");
                        
                        // CORRECCIÓN CLAVE: Llamar al método correcto del Trait y pasar los parámetros esperados
                        $this->deleteS3Image($folderToDelete, $fileNameToDelete);
                    }

                    // 2. Guardar la nueva imagen. Asumimos que saveImages usa $productId como nombre base.
                    $this->saveImages($fileContents, $pathSegment, $productId);
                    
                    // 3. Guardar la nueva ruta completa en la DB, ¡igual que en store!
                    $eventData[$dbField] = env('S3_ENVIRONMENT') . '/' . $pathSegment . '/' . $fileName; // RUTA COMPLETA
                }
            };

            $uploadAndUpdateImage('banner', 'events/banners', 'banner', $event, $request);
            $uploadAndUpdateImage('image', 'events/images', 'image', $event, $request);
            $uploadAndUpdateImage('icon', 'events/icons', 'icon', $event, $request);
        
            // 5. Actualizar el evento
            $event->update($eventData);
        
            // 6. Sincronizar tipos de boletos (más complejo ya que hay que manejar nuevos y existentes)
            $newTicketTypes = [];
            $syncData = [];

            foreach ($request->ticket_types as $ticketTypeData) {
                $ticketIdentifier = $ticketTypeData['name_other'] ?? $ticketTypeData['name'];

                if (is_numeric($ticketIdentifier)) {
                    $ticketType = TicketType::find($ticketIdentifier);
                    if (!$ticketType) {
                        // Si el ID no existe (ej: fue eliminado externamente), lo ignoramos.
                        \Log::warning("Tipo de Boleto ID $ticketIdentifier no encontrado, saltando en update.");
                        continue;
                    }
                } else {
                    // B. Se escribió un nombre nuevo (Ej: 'Platino'). Buscamos o creamos por nombre.
                    // Usamos firstOrCreate() con el nombre escrito
                    $ticketType = TicketType::firstOrCreate(
                        ['name' => $ticketIdentifier]
                    );
                }
                $syncData[$ticketType->id] = [
                    'price' => $ticketTypeData['price'],
                    'quantity' => $ticketTypeData['quantity']
                ];
            }

            // Sincronizar: Adjunta/Actualiza solo los IDs pasados y elimina los que faltan
            $event->ticketTypes()->sync($syncData);

            return redirect()
                ->route('spaces.profile', $space->subdomain)
                ->with('success', 'Evento actualizado exitosamente y las imágenes fueron gestionadas en S3.');

        } catch (\Exception $e) {
            // Registrar el error en el log
            \Log::error('Error al actualizar el evento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'exception' => get_class($e)
            ]);
        
            $errorMessage = 'Ocurrió un error al actualizar el evento. Por favor, intenta nuevamente.';
            if (strpos($e->getMessage(), 'S3') !== false || strpos($e->getMessage(), 'AWS') !== false) {
                $errorMessage = 'Error al subir/gestionar las imágenes en S3. Verifica la configuración de AWS y los logs.';
            }
        
            return back()
                ->withInput()
                ->with('error', $errorMessage);
        } 
    }
}