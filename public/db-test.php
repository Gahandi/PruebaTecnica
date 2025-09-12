<?php
// Test de conexión a la base de datos
echo "Testing database connection...<br>";

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
    echo "✅ Database connection successful!<br>";
    
    // Test simple query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "✅ Query test successful: " . $result['test'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<br>Environment variables:<br>";
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'Not set') . "<br>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "<br>";
echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'Not set') . "<br>";
?>
