<?php
// Test específico para capturar errores
echo "Testing Laravel Error Capture...<br>";

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
    
    // Obtener el kernel HTTP
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel created<br>";
    
    // Test de la ruta raíz con manejo de errores
    echo "Testing root route with error handling...<br>";
    try {
        $request = Illuminate\Http\Request::create('/', 'GET');
        $response = $kernel->handle($request);
        
        echo "Response status: " . $response->getStatusCode() . "<br>";
        echo "Response content length: " . strlen($response->getContent()) . "<br>";
        
        // Si es un error 500, mostrar el contenido
        if ($response->getStatusCode() == 500) {
            echo "Error 500 detected. Content preview:<br>";
            echo "<pre>" . htmlspecialchars(substr($response->getContent(), 0, 500)) . "</pre>";
        }
        
    } catch (Exception $e) {
        echo "❌ Exception during route processing: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
        echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
    }
    
    // Test directo del controlador
    echo "Testing controller directly...<br>";
    try {
        $controller = new \App\Http\Controllers\Public\EventController();
        $events = \App\Models\Event::with('ticketTypes')->get();
        $view = view('public.events.index', ['events' => $events]);
        $content = $view->render();
        echo "✅ Controller test successful<br>";
        echo "Content length: " . strlen($content) . "<br>";
    } catch (Exception $e) {
        echo "❌ Controller error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ General error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
