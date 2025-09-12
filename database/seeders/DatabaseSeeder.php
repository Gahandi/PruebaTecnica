<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar el seeder de roles y permisos primero
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Crear eventos
        $events = \App\Models\Event::factory(5)->create();

        // Crear tipos de boletos para cada evento
        foreach ($events as $event) {
            \App\Models\TicketType::factory(3)->create([
                'event_id' => $event->id,
            ]);
        }

        // Crear cupones
        \App\Models\Coupon::factory(10)->create();

        // Crear algunas órdenes de ejemplo
        \App\Models\Order::factory(20)->create();

        // Crear algunos check-ins
        \App\Models\Checkin::factory(15)->create();
    }
}
