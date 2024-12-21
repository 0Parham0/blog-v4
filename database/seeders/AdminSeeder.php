<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $name = $this->command->ask("Enter admin name", 'Admin');
        $email = $this->command->ask('Enter admin email', 'admin@admin.admin');
        $password = $this->command->ask('Enter admin password', 'adminpassword');

        $admin = User::firstOrCreate([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole('admin');

        $this->command->info("Admin created with email: {$email} and password: {$password}");
    }
}
