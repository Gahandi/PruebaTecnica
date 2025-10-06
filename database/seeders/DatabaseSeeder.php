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
        /* $this->call([
            RolePermissionSeeder::class,
        ]); */

/*         $this->call([
            RoleSpaceSeeder::class,
        ]); */

/*         $this->call([
            TypeEventSeeder::class,
        ]);
 */
        $this->call([
            StateSeeder::class,
        ]);


        //Crear solo usuarios con roles
        //$this->call([
            //UserSeeder::class,
        //]);
    }
}
