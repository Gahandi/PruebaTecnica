<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Space;
use App\Models\Tag;
use App\Models\TicketType;
use App\Models\TypeEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\S3ImageManager;

class SpaceEventController extends Controller
{
    use S3ImageManager;

    /**
     * Lista de eventos del espacio con estadísticas
     */
    public function index(Request $request, $subdomain)
    {
        $space = $request->get('space');

        $events = Event::where('spaces_id', $space->id)
            ->with(['type_event', 'state'])
            ->withCount(['orders', 'tickets'])
            ->orderBy('date', 'desc')
            ->paginate(12);

        // Agregar estadísticas a cada evento
        $events->getCollection()->transform(function ($event) {
            $completedOrderIds = $event->orders()->where('status', 'completed')->pluck('id');
            $event->completed_orders = $completedOrderIds->count();
            $event->total_revenue = \App\Models\Payment::whereIn('order_id', $completedOrderIds)->sum('total');
            $event->tickets_sold = $event->tickets_count;
            return $event;
        });

        return view('spaces.events.index', compact('space', 'events'));
    }

    public function show(Request $request, $subdomain, Event $event)
    {
        // Verificar que el evento pertenece al espacio correcto
        $space = Space::where('subdomain', $subdomain)->first();

        if (!$space || $event->spaces_id !== $space->id) {
            abort(404, 'Evento no encontrado');
        }

        $event->load(['type_event', 'ticketTypes', 'tags']);

        return view('events.show', compact('space', 'event'));
    }

    public function showEvents($subdomain, $id)
    {
        $typeEvent = TypeEvent::findOrFail($id);
        $space = Space::where('subdomain', $subdomain)->firstOrFail();

        $events = Event::where('spaces_id', $space->id)
            ->where('type_events_id', $id)
            ->where('active', true)
            ->where('date', '>=', now())
            ->orderBy('date')
            ->get();

        return view('spaces.events.by_category', compact('space', 'events', 'typeEvent'));
    }

    public function create(Request $request, $subdomain)
    {
        $space = $request->get('space');
        $ticketTypes = TicketType::all();
        $typeEvents = TypeEvent::all();
        $tags = Tag::all();

        return view('spaces.events.create', compact('space', 'ticketTypes', 'typeEvents', 'tags'));
    }

    // Store a new event category (TypeEvent) with optional S3 image upload
    public function storeCategory(Request $request, $subdomain)
    {
        // 1. Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string', // FontAwesome icon class
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional image upload
        ]);

        try {
            // 2. Handle Image Upload to S3 (if provided)
            $imageUrl = null;
            if ($request->hasFile('image_file')) {
                $file = $request->file('image_file');
                $fileContents = file_get_contents($file->getPathname());

                // Determine file extension
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

                // Use a generic ID or timestamp for the filename since we don't have the ID yet
                $fileId = 'category_' . time();
                $fileName = $fileId . '.' . $extension;
                $pathSegment = 'categories/images';

                // Upload using the trait
                $this->saveImages($fileContents, $pathSegment, $fileId);

                // Construct the S3 URL
                $imageUrl = env('S3_ENVIRONMENT') . '/' . $pathSegment . '/' . $fileName;
            }

            // 3. Create the Category
            $category = TypeEvent::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'icon' => $validated['icon'] ?? 'fas fa-calendar-alt', // Default icon
                'image' => $imageUrl, // Save the S3 URL or null
            ]);

            // 4. Return the new category data as JSON
            return response()->json([
                'success' => true,
                'category' => $category,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error creating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría. Por favor intente de nuevo.',
            ], 500);
        }
    }


    public function store(Request $request, $subdomain)
    {
        try {
            $space = $request->get('space');

            // 1. Validar los datos
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date|after:now',
                'address' => 'required|string|max:255',
                'coordinates' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'nullable|string|max:255',
                'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type_event_id' => 'required|exists:type_events,id',
                'ticket_types' => 'required|array|min:1',
                'ticket_types.*.name' => 'required|string|max:255',
                'ticket_types.*.price' => 'required|numeric|min:0',
                'ticket_types.*.quantity' => 'required|integer|min:1',
            ]);

            // 2. Preparar los datos básicos
            $eventData = [
                'name' => $request->name,
                'description' => $request->description,
                'date' => $request->date,
                'address' => $request->address,
                'coordinates' => $request->coordinates,
                'spaces_id' => $space->id,
                'type_events_id' => $request->type_event_id,
                'slug' => Str::slug($request->name),
                'active' => true,
                'agenda' => $request->agenda ?? 'N/A',
                'state_id' => 1,
            ];

            // 3. Generar un slug y un ID idealmente único para las imágenes
            // Usaremos el ID del espacio + timestamp para agrupar.
            $timestamp = time();
            $tempId = $space->id . '_' . $timestamp;

            // Función helper para subir imagen y retornar la URL FINAL
            $handleUpload = function ($fileKey, $pathSegment, $defaultName) use ($request, $tempId) {
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $fileContents = file_get_contents($file->getPathname());

                    // Nombre base para S3
                    $fileName = $defaultName . '_' . $tempId;

                    // Subir usando el trait
                    // El trait S3ImageManager::saveImages($content, $path, $name)
                    // NOTA: Si el trait agrega automáticamente '.jpg', la URL debe reflejar eso.
                    $this->saveImages($fileContents, $pathSegment, $fileName);

                    // Construir la URL
                    // Asumiendo estandarización a .jpg como es común en estos traits
                    return env('S3_ENVIRONMENT') . '/' . $pathSegment . '/' . $fileName . '.jpg';
                }
                return null;
            };

            // Subir imágenes antes de crear el evento
            $bannerUrl = $handleUpload('banner', 'events/banners', 'banner');
            $imageUrl = $handleUpload('image', 'events/images', 'image');
            $iconUrl = $handleUpload('icon', 'events/icons', 'icon');

            // Validar que se hayan obtenido las URLs (requeridas por DB)
            if (!$bannerUrl || !$imageUrl) {
                // Si falla la subida pero pasó la validación de Laravel, es un error de S3 o algo interno.
                throw new \Exception('Error al procesar las imágenes. No se pudieron subir a S3.');
            }

            // Agregar URLs al array de datos para la creación
            $eventData['banner'] = $bannerUrl;
            $eventData['image'] = $imageUrl;
            // Asignamos la misma imagen vertical para banner_app ya que es requerida y probablemente sea la misma visualización
            $eventData['banner_app'] = $imageUrl;
            $eventData['icon'] = $iconUrl;

            // 4. Crear el evento con todos los datos INCLUYENDO LAS IMÁGENES
            $event = Event::create($eventData);

            // Ya no necesitamos el paso posterior de actualización de imágenes


            // 5. Crear y asociar tipos de boletos
            foreach ($request->ticket_types as $ticketTypeData) {
                // A. Buscar si es un ID existente (selección de lista)
                // El frontend debería mandar el ID en 'name' si es selección, o el texto si es nuevo.
                // Ajuste: Vamos a asumir que 'name' trae el texto.

                // Lógica Híbrida:
                // Si el usuario seleccionó de la lista, el valor podría ser el ID.
                // Si escribió uno nuevo, es texto.
                // Verificamos si es numérico y existe.

                $ticketIdentifier = $ticketTypeData['name_other'] ?? $ticketTypeData['name']; // Soporte para input "otro"

                if (is_numeric($ticketIdentifier)) {
                    $ticketType = TicketType::find($ticketIdentifier);
                    if (!$ticketType) {
                        // Fallback por si acaso envió un número que no es ID
                        $ticketType = TicketType::firstOrCreate(['name' => $ticketIdentifier]);
                    }
                } else {
                    // Texto libre -> Buscar por nombre o crear
                    $ticketType = TicketType::firstOrCreate(['name' => $ticketIdentifier]);
                }

                // Asociar al evento con precio y cantidad (tabla pivote tickets_events probablemente, o methods custom)
                // Asumiendo relación ManyToMany con pivote:
                $event->ticketTypes()->attach($ticketType->id, [
                    'price' => $ticketTypeData['price'],
                    'quantity' => $ticketTypeData['quantity']
                ]);
            }

            // 6. Procesar Tags (similar logic)
            if ($request->has('tags') && is_array($request->tags)) {
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    if (!empty(trim($tagName))) {
                        $tag = Tag::firstOrCreate(
                            ['name' => trim($tagName)],
                            ['slug' => Str::slug(trim($tagName))] // Asumiendo que Tag tiene slug
                        );
                        $tagIds[] = $tag->id;
                    }
                }
                $event->tags()->sync($tagIds);
            }

            return redirect()
                ->route('spaces.profile', $space->subdomain)
                ->with('success', 'Evento creado exitosamente.');

        } catch (\Exception $e) {
            // Registrar el error en el log para debugging
            \Log::error('Error al crear evento: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Mensaje amigable pero con detalle técnico para debug
            $errorMessage = 'Error: ' . $e->getMessage();

            if (strpos($e->getMessage(), 'S3') !== false || strpos($e->getMessage(), 'AWS') !== false) {
                $errorMessage = 'Error de S3/AWS: ' . $e->getMessage();
            }

            return back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Muestra el formulario para editar un evento existente.
     */
    public function edit(Request $request, $subdomain, Event $event)
    {
        // El espacio ya está disponible en la request por el middleware
        $space = $request->get('space');

        // 1. Verificar que el evento pertenece al espacio (por seguridad)
        if ($event->spaces_id !== $space->id) {
            abort(403, 'Acceso no autorizado a este evento.');
        }

        // 2. Verificar restricción de 4 días
        // Si faltan menos de 4 días para que empiece el evento, no se puede editar
        $daysUntilEvent = now()->diffInDays($event->date, false);
        if ($daysUntilEvent < 4 && $event->date > now()) {
            return redirect()
                ->route('spaces.events.index', $space->subdomain)
                ->with('error', 'No se puede editar el evento porque faltan menos de 4 días para su realización.');
        }

        // 3. Cargar las relaciones necesarias
        $event->load([
            'ticketTypes' => function ($query) {
                $query->withPivot('quantity', 'price');
            }
        ]);

        $ticketTypes = TicketType::all();
        $typeEvents = TypeEvent::all();
        $tags = Tag::all();

        // Cargar tags del evento
        $event->load('tags');

        // 4. Retornar la vista de creación (unificada para crear/editar)
        return view('spaces.events.create', compact('space', 'event', 'ticketTypes', 'typeEvents', 'tags'));
    }

    /**
     * Actualiza la información de un evento existente.
     */
    public function update(Request $request, $subdomain, Event $event)
    {
        try {
            $space = $request->get('space');

            // 1. Verificar que el evento pertenece al espacio (por seguridad)
            if ($event->spaces_id !== $space->id) {
                abort(403, 'Acceso no autorizado para editar este evento.');
            }

            // 2. Verificar restricción de 4 días
            $daysUntilEvent = now()->diffInDays($event->date, false);
            if ($daysUntilEvent < 4 && $event->date > now()) {
                return redirect()
                    ->route('spaces.events.index', $space->subdomain)
                    ->with('error', 'No se puede editar el evento porque faltan menos de 4 días para su realización.');
            }

            // 3. Validar los datos
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date|after:now',
                'address' => 'required|string|max:255',
                'coordinates' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'nullable|string|max:255',
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

            // 4. Preparar los datos básicos
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

            // 5. Gestión de Archivos (Subir a S3 y actualizar la DB)
            $uploadAndUpdateImage = function ($fileKey, $pathSegment, $dbField, $event, $request) use (&$eventData) {
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $fileContents = file_get_contents($file->getPathname());

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

                    $productId = $event->spaces_id . '_' . time();
                    $fileName = $productId . '.' . $extension;

                    // Eliminar la imagen antigua si existe y no es una URL por defecto
                    if ($event->$dbField && !Str::startsWith($event->$dbField, 'http')) {
                        $fullPath = $event->$dbField;
                        $fileNameToDelete = basename($fullPath);
                        $envPrefix = env('S3_ENVIRONMENT') . '/';
                        $folderAndFile = Str::after($fullPath, $envPrefix);
                        $folderToDelete = Str::beforeLast($folderAndFile, '/');
                        $this->deleteS3Image($folderToDelete, $fileNameToDelete);
                    }

                    $this->saveImages($fileContents, $pathSegment, $productId);
                    $eventData[$dbField] = env('S3_ENVIRONMENT') . '/' . $pathSegment . '/' . $fileName;
                }
            };

            $uploadAndUpdateImage('banner', 'events/banners', 'banner', $event, $request);
            $uploadAndUpdateImage('image', 'events/images', 'image', $event, $request);
            $uploadAndUpdateImage('icon', 'events/icons', 'icon', $event, $request);

            // 6. Actualizar el evento
            $event->update($eventData);

            // 7. Sincronizar tipos de boletos
            $syncData = [];

            foreach ($request->ticket_types as $ticketTypeData) {
                $ticketIdentifier = $ticketTypeData['name_other'] ?? $ticketTypeData['name'];

                if (is_numeric($ticketIdentifier)) {
                    $ticketType = TicketType::find($ticketIdentifier);
                    if (!$ticketType) {
                        continue;
                    }
                } else {
                    $ticketType = TicketType::firstOrCreate(
                        ['name' => $ticketIdentifier]
                    );
                }
                $syncData[$ticketType->id] = [
                    'price' => $ticketTypeData['price'],
                    'quantity' => $ticketTypeData['quantity']
                ];
            }

            $event->ticketTypes()->sync($syncData);

            // Procesar tags
            if ($request->has('tags') && is_array($request->tags)) {
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    if (!empty(trim($tagName))) {
                        $tag = Tag::firstOrCreate(
                            ['name' => trim($tagName)],
                            ['slug' => Str::slug(trim($tagName))]
                        );
                        $tagIds[] = $tag->id;
                    }
                }
                $event->tags()->sync($tagIds);
            } else {
                $event->tags()->sync([]);
            }

            return redirect()
                ->route('spaces.profile', $space->subdomain)
                ->with('success', 'Evento actualizado exitosamente.');

        } catch (\Exception $e) {
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

    /**
     * Elimina un evento.
     */
    public function destroy(Request $request, $subdomain, Event $event)
    {
        $space = $request->get('space');

        if ($event->spaces_id !== $space->id) {
            abort(403, 'Acceso no autorizado a este evento.');
        }

        $event->delete();

        return redirect()
            ->route('spaces.events.index', $space->subdomain)
            ->with('success', 'Evento eliminado correctamente.');
    }

    /**
     * Duplica un evento existente.
     */
    public function duplicate(Request $request, $subdomain, Event $event)
    {
        $space = $request->get('space');

        // 1. Verificar permisos
        if ($event->spaces_id !== $space->id) {
            abort(403, 'Acceso no autorizado a este evento.');
        }

        try {
            // 2. Replicar evento
            $newEvent = $event->replicate();
            $newEvent->name = 'Copia de ' . $event->name;
            $newEvent->slug = Str::slug($newEvent->name) . '-' . time(); // Slug único
            $newEvent->active = false; // Inactivo por defecto hasta que se configure
            $newEvent->date = now()->addDays(5); // Fecha temporal (5 días para evitar restricción de 4 días)
            $newEvent->created_at = now();
            $newEvent->updated_at = now();
            $newEvent->push(); // Guardar y cargar relaciones

            // 3. Duplicar relaciones (ticket types y tags)
            $event->load(['ticketTypes', 'tags']);

            // Duplicar ticket types con pivote
            foreach ($event->ticketTypes as $ticketType) {
                $newEvent->ticketTypes()->attach($ticketType->id, [
                    'price' => $ticketType->pivot->price,
                    'quantity' => $ticketType->pivot->quantity
                ]);
            }

            // Duplicar tags
            $newEvent->tags()->sync($event->tags->pluck('id'));

            // 4. Redirigir al formulario de edición del NUEVO evento
            return redirect()
                ->route('spaces.events.edit', [$space->subdomain, $newEvent->slug])
                ->with('success', 'Evento duplicado correctamente. Por favor configura la nueva fecha y activa el evento.');

        } catch (\Exception $e) {
            \Log::error('Error al duplicar evento: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al duplicar el evento.');
        }
    }
}
