<?php
// Test específico de eventos
echo "Testing Events Query...<br>";

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Cargar el autoloader de Composer
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "✅ Composer autoloader loaded<br>";
    
    // Crear la aplicación Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Laravel app created<br>";
    
    // Configurar el entorno
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "✅ Laravel bootstrapped<br>";
    
    // Test de conexión a la base de datos
    try {
        $events = \App\Models\Event::with('ticketTypes')->get();
        echo "✅ Events query successful<br>";
        echo "Events count: " . $events->count() . "<br>";
        
        if ($events->count() > 0) {
            echo "First event: " . $events->first()->name . "<br>";
        } else {
            echo "No events found in database<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database query error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    }
    
    // Test de la vista
    try {
        $view = view('public.events.index', ['events' => $events]);
        echo "✅ View created successfully<br>";
        
        $content = $view->render();
        echo "✅ View rendered successfully<br>";
        echo "View content length: " . strlen($content) . "<br>";
        
    } catch (Exception $e) {
        echo "❌ View error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
