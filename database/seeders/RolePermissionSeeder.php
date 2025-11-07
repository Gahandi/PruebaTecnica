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
            'view events' => 'Ver eventos',
            'create events' => 'Crear eventos',
            'edit events' => 'Editar eventos',
            'delete events' => 'Eliminar eventos',
            
            // Tipos de boletos
            'view ticket types' => 'Ver tipos de boletos',
            'create ticket types' => 'Crear tipos de boletos',
            'edit ticket types' => 'Editar tipos de boletos',
            'delete ticket types' => 'Eliminar tipos de boletos',
            
            // Cupones
            'view coupons' => 'Ver cupones',
            'create coupons' => 'Crear cupones',
            'edit coupons' => 'Editar cupones',
            'delete coupons' => 'Eliminar cupones',
            
            // Órdenes
            'view orders' => 'Ver órdenes',
            'create orders' => 'Crear órdenes',
            'edit orders' => 'Editar órdenes',
            'delete orders' => 'Eliminar órdenes',
            
            // Check-in
            'view checkins' => 'Ver check-ins',
            'create checkins' => 'Crear check-ins'  ,
            'edit checkins' => 'Editar check-ins',
            'delete checkins' => 'Eliminar check-ins',
            
            // Dashboard
            'view dashboard' => 'Ver dashboard',
            'view admin panel' => 'Ver panel de administración',
        ];

        foreach ($permissions as $permission => $description) {
            Permission::create(['name' => $permission, 'description' => $description]);
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
            'view admin panel'
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
            'last_name' => 'Del sistema',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $staff = User::create([
            'name' => 'Staff',
            'last_name' => 'Del sistema',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
        ]);
        $staff->assignRole('staff');

        $viewer = User::create([
            'name' => 'Viewer',
            'last_name' => 'Del sistema',
            'email' => 'viewer@example.com',
            'password' => bcrypt('password'),
        ]);
        $viewer->assignRole('viewer');
    }
}