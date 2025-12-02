<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeEvents = [
            [
                'name' => 'Makeup',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Barbershop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Estilismo y Modas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Curso de Uñas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Redes Sociales y Generación de Contenido',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \App\Models\TypeEvent::insert($typeEvents);
        
        $this->command->info('Tipos de eventos creados exitosamente: ' . count($typeEvents));
    }
}
