<?php

namespace Database\Seeders;

use App\Models\RoleSpace;
use Illuminate\Database\Seeder;

class RoleSpaceSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador del espacio - acceso completo'
            ],
            [
                'name' => 'staff',
                'description' => 'Staff del espacio - gestión de eventos'
            ],
            [
                'name' => 'viewer',
                'description' => 'Visualizador del espacio - solo lectura'
            ]
        ];

        foreach ($roles as $role) {
            RoleSpace::create($role);
        }
    }
}
