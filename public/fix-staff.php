<?php
// Script para asegurar que el usuario staff tenga el rol correcto
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Buscar o crear el usuario staff
$staff = \App\Models\User::firstOrCreate(
    ['email' => 'staff@example.com'],
    [
        'name' => 'Staff Member',
        'password' => \Hash::make('password'),
        'email_verified_at' => now(),
    ]
);

// Asegurar que el rol staff existe
$staffRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff']);

// Asignar el rol al usuario
$staff->assignRole('staff');

// Asegurar que el rol tenga los permisos correctos
$permissions = [
    'view events',
    'view ticket types', 
    'view coupons',
    'view orders',
    'view checkins',
    'create checkins',
    'edit checkins',
    'view dashboard',
    'view admin panel',
];

foreach ($permissions as $permissionName) {
    $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permissionName]);
    $staffRole->givePermissionTo($permission);
}

echo "Usuario staff configurado correctamente:<br>";
echo "Email: " . $staff->email . "<br>";
echo "Roles: " . $staff->roles->pluck('name')->implode(', ') . "<br>";
echo "Permisos: " . $staff->getAllPermissions()->pluck('name')->implode(', ') . "<br>";
echo "Tiene rol staff: " . ($staff->hasRole('staff') ? 'SÍ' : 'NO') . "<br>";
echo "Puede ver checkins: " . ($staff->can('view checkins') ? 'SÍ' : 'NO') . "<br>";
?>
