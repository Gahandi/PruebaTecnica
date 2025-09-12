<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar usuario administrador
        User::updateOrCreate(
            ['email' => 'admin@nailsawards.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario staff
        User::updateOrCreate(
            ['email' => 'staff@nailsawards.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Crear o actualizar usuario viewer
        User::updateOrCreate(
            ['email' => 'viewer@nailsawards.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
