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
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'admin123', // Will be automatically hashed by the model
                'role' => 'admin',
            ]
        );

        // Create Operator User
        User::updateOrCreate(
            ['email' => 'operator@example.com'],
            [
                'name' => 'Operator User',
                'password' => 'operator123', // Will be automatically hashed by the model
                'role' => 'operator',
            ]
        );

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@example.com / admin123');
        $this->command->info('Operator: operator@example.com / operator123');
    }
}
