<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'username' => env('ADMIN_USERNAME'),
            'name' => env('ADMIN_NAME'),
            'password' => Hash::make(env('ADMIN_PASSWORD')),
            'role' => UserRole::Admin,
        ]);

        // Super Admin
        User::create([
            'username' => env('SUPER_ADMIN_USERNAME'),
            'name' => env('SUPER_ADMIN_NAME'),
            'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
            'role' => UserRole::SuperAdmin,
        ]);
    }
}
