<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@nailsawards.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Crear usuario staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@nailsawards.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $staff->assignRole('staff');

        // Crear usuario viewer
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@nailsawards.com'],
            [
                'name' => 'Viewer',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        $viewer->assignRole('viewer');

        $this->command->info('Usuarios creados exitosamente:');
        $this->command->info('- Admin: admin@nailsawards.com / password123');
        $this->command->info('- Staff: staff@nailsawards.com / password123');
        $this->command->info('- Viewer: viewer@nailsawards.com / password123');
    }
}