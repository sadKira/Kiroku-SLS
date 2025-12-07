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
        // Admin 1
        User::create([
            'username' => 'mkdlibrary25',
            'name' => 'Admin Account',
            'password' => Hash::make("mkd2025-admin"),
            'role' => UserRole::Admin,
        ]);

        // Admin 0
        User::create([
            'username' => 'kamisama',
            'name' => 'Super Admin',
            'password' => Hash::make("mkd2025-masterminad"),
            'role' => UserRole::SuperAdmin,
        ]);
    }
}
