<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $state = [
            [
                'name' => 'Activo',
            ],
            [
                'name' => 'Pendiente',
            ],
            [
                'name' => 'Eliminado',
            ],
            [
                'name' => 'Pagado',
            ]

        ];

        \App\Models\State::insert($state);

        $this->command->info('Tipos de states creados exitosamente: ' . count($state));
    }
}
