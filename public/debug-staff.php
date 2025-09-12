<?php
// Debug para verificar el usuario staff
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Buscar el usuario staff
$staff = \App\Models\User::where('email', 'staff@example.com')->first();

if ($staff) {
    echo "Usuario encontrado: " . $staff->name . " (" . $staff->email . ")<br>";
    echo "Roles: " . $staff->roles->pluck('name')->implode(', ') . "<br>";
    echo "Permisos: " . $staff->getAllPermissions()->pluck('name')->implode(', ') . "<br>";
    echo "Tiene rol staff: " . ($staff->hasRole('staff') ? 'SÍ' : 'NO') . "<br>";
    echo "Puede ver checkins: " . ($staff->can('view checkins') ? 'SÍ' : 'NO') . "<br>";
} else {
    echo "Usuario staff@example.com no encontrado<br>";
}

// Verificar todos los usuarios
echo "<br>--- Todos los usuarios ---<br>";
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo $user->email . " - Roles: " . $user->roles->pluck('name')->implode(', ') . "<br>";
}
?>
