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
                'name' => 'Concierto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Conferencia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Workshop',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Festival',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deportivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cultural',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Networking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gastronómico',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tecnología',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Familiar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Religioso',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Benéfico',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \App\Models\TypeEvent::insert($typeEvents);
        
        $this->command->info('Tipos de eventos creados exitosamente: ' . count($typeEvents));
    }
}
