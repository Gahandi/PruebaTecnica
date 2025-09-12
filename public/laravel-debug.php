<?php
// Debug detallado de Laravel
echo "Testing Laravel with detailed error reporting...<br>";

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
    
    // Crear una request simple
    $request = Illuminate\Http\Request::create('/', 'GET');
    echo "✅ Request created<br>";
    
    // Procesar la request con manejo de errores
    try {
        $response = $kernel->handle($request);
        echo "✅ Request processed successfully<br>";
        echo "Response status: " . $response->getStatusCode() . "<br>";
        echo "Response content length: " . strlen($response->getContent()) . "<br>";
    } catch (Exception $e) {
        echo "❌ Error processing request: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
        echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
