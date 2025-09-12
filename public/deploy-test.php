<?php
echo "Deploy test - " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Laravel Version: " . app()->version() . "<br>";

// Test routes
echo "<h3>Testing Routes:</h3>";
try {
    $routes = app('router')->getRoutes();
    $checkinRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'checkins') !== false) {
            $checkinRoutes[] = $route->uri() . ' (' . implode('|', $route->methods()) . ')';
        }
    }
    
    if (empty($checkinRoutes)) {
        echo "❌ No checkin routes found<br>";
    } else {
        echo "✅ Checkin routes found:<br>";
        foreach ($checkinRoutes as $route) {
            echo "- " . $route . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error loading routes: " . $e->getMessage() . "<br>";
}

// Test middleware
echo "<h3>Testing Middleware:</h3>";
try {
    $middleware = app('router')->getMiddleware();
    if (isset($middleware['redirect.unauthorized'])) {
        echo "✅ redirect.unauthorized middleware registered<br>";
    } else {
        echo "❌ redirect.unauthorized middleware NOT registered<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading middleware: " . $e->getMessage() . "<br>";
}
?>
