<?php
// Test básico de Laravel
echo "Testing Laravel...<br>";

try {
    // Cargar el autoloader de Composer
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "✅ Composer autoloader loaded<br>";
    
    // Crear la aplicación Laravel
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Laravel app created<br>";
    
    // Obtener el kernel HTTP
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel created<br>";
    
    // Crear una request simple
    $request = Illuminate\Http\Request::create('/', 'GET');
    echo "✅ Request created<br>";
    
    // Procesar la request
    $response = $kernel->handle($request);
    echo "✅ Request processed<br>";
    
    echo "Response status: " . $response->getStatusCode() . "<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}
?>
