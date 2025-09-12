<?php
// Test para verificar logs de Laravel
echo "Testing Laravel Logs...<br>";

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
    
    // Verificar archivos de log
    $logPath = __DIR__ . '/../storage/logs/laravel.log';
    echo "Log path: " . $logPath . "<br>";
    
    if (file_exists($logPath)) {
        echo "✅ Log file exists<br>";
        $logContent = file_get_contents($logPath);
        $logSize = strlen($logContent);
        echo "Log file size: " . $logSize . " bytes<br>";
        
        if ($logSize > 0) {
            echo "Last 1000 characters of log:<br>";
            echo "<pre>" . htmlspecialchars(substr($logContent, -1000)) . "</pre>";
        } else {
            echo "Log file is empty<br>";
        }
    } else {
        echo "❌ Log file does not exist<br>";
    }
    
    // Verificar permisos de storage
    $storagePath = __DIR__ . '/../storage';
    echo "Storage path: " . $storagePath . "<br>";
    echo "Storage exists: " . (is_dir($storagePath) ? 'Yes' : 'No') . "<br>";
    echo "Storage writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "<br>";
    
    // Verificar variables de entorno críticas
    echo "Environment variables:<br>";
    echo "APP_KEY: " . (env('APP_KEY') ? 'Set' : 'NOT SET') . "<br>";
    echo "DB_HOST: " . (env('DB_HOST') ?: 'NOT SET') . "<br>";
    echo "DB_DATABASE: " . (env('DB_DATABASE') ?: 'NOT SET') . "<br>";
    echo "APP_DEBUG: " . (env('APP_DEBUG') ?: 'NOT SET') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}
?>
