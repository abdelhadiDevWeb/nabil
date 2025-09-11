<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'email' => 'admin@anpt.dz',
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'ANPT',
            'employee_id' => 'ADMIN001',
            'department' => 'IT',
            'position' => 'Administrateur Système',
            'login' => 'admin',
            'password' => Hash::make('admin123'),
            'is_active' => true,
            'hire_date' => now(),
        ]);

        // Create test employee
        User::create([
            'email' => 'employee@anpt.dz',
            'role' => 'employee',
            'first_name' => 'Employé',
            'last_name' => 'Test',
            'employee_id' => 'EMP001',
            'department' => 'Développement',
            'position' => 'Développeur',
            'login' => 'employee',
            'password' => Hash::make('employee123'),
            'is_active' => true,
            'hire_date' => now(),
        ]);
    }
}