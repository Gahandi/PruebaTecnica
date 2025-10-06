<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rolespace;

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
            Rolespace::create($role);
        }
    }
}
