<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Crear permisos
        $permissions = [
            // Eventos
            'view events',
            'create events',
            'edit events',
            'delete events',
            
            // Tipos de boletos
            'view ticket types',
            'create ticket types',
            'edit ticket types',
            'delete ticket types',
            
            // Cupones
            'view coupons',
            'create coupons',
            'edit coupons',
            'delete coupons',
            
            // Órdenes
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            
            // Check-in
            'view checkins',
            'create checkins',
            'edit checkins',
            'delete checkins',
            
            // Dashboard
            'view dashboard',
            'view admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $staffRole = Role::create(['name' => 'staff']);
        $viewerRole = Role::create(['name' => 'viewer']);

        // Asignar permisos a admin (todos)
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos a staff
        $staffRole->givePermissionTo([
            'view events',
            'view ticket types',
            'view coupons',
            'view orders',
            'view checkins',
            'create checkins',
            'edit checkins',
            'view dashboard',
            'view admin panel',
        ]);

        // Asignar permisos a viewer
        $viewerRole->givePermissionTo([
            'view events',
            'view ticket types',
            'view coupons',
            'view orders',
            'view dashboard',
        ]);

        // Crear usuarios de ejemplo
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $staff = User::create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
        ]);
        $staff->assignRole('staff');

        $viewer = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@example.com',
            'password' => bcrypt('password'),
        ]);
        $viewer->assignRole('viewer');
    }
}