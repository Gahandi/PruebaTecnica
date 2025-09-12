<?php
// Test básico de PHP
echo "PHP está funcionando correctamente<br>";

// Test de conexión a la base de datos
try {
    $host = 'apps-web-extra-gsch-69b2.g.aivencloud.com';
    $port = 20606;
    $dbname = 'defaultdb';
    $username = 'avnadmin';
    $password = 'AVNS_5ers-mFGmvYZyuNz9zT';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    echo "Conexión a la base de datos exitosa<br>";
    
    // Test de consulta simple
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "Consulta de prueba exitosa: " . $result['test'] . "<br>";
    
} catch (Exception $e) {
    echo "Error de base de datos: " . $e->getMessage() . "<br>";
}

// Test de variables de entorno
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'No definido') . "<br>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'No definido') . "<br>";
echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'No definido') . "<br>";
?>
