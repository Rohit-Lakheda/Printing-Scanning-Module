<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create 
                            {name : The name of the user}
                            {email : The email of the user}
                            {password : The password (will be automatically hashed)}
                            {--role=operator : The role (admin or operator)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with automatically hashed password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $role = $this->option('role');

        if (!in_array($role, ['admin', 'operator'])) {
            $this->error('Role must be either "admin" or "operator"');
            return 1;
        }

        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        // Create user - password will be automatically hashed by the model
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password, // Will be automatically hashed
            'role' => $role,
        ]);

        $this->info("User created successfully!");
        $this->info("Name: {$name}");
        $this->info("Email: {$email}");
        $this->info("Role: {$role}");
        $this->info("Password: {$password} (hashed in database)");

        return 0;
    }
}
