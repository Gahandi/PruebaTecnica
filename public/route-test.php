<?php
// Test específico del routing de Laravel
echo "Testing Laravel Routing...<br>";

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
    
    // Test de la ruta raíz
    echo "Testing root route (/)...<br>";
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    echo "✅ Root route processed<br>";
    echo "Response status: " . $response->getStatusCode() . "<br>";
    echo "Response content length: " . strlen($response->getContent()) . "<br>";
    
    // Test de la ruta de eventos
    echo "Testing events route (/events)...<br>";
    $request = Illuminate\Http\Request::create('/events', 'GET');
    $response = $kernel->handle($request);
    echo "✅ Events route processed<br>";
    echo "Response status: " . $response->getStatusCode() . "<br>";
    echo "Response content length: " . strlen($response->getContent()) . "<br>";
    
    // Test de redirección
    echo "Testing redirect from root to events...<br>";
    $request = Illuminate\Http\Request::create('/', 'GET');
    $response = $kernel->handle($request);
    
    if ($response instanceof Illuminate\Http\RedirectResponse) {
        echo "✅ Redirect response detected<br>";
        echo "Redirect URL: " . $response->getTargetUrl() . "<br>";
        echo "Redirect status: " . $response->getStatusCode() . "<br>";
    } else {
        echo "❌ Not a redirect response<br>";
        echo "Response type: " . get_class($response) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
