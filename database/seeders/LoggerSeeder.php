<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoggerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Logger
        User::create([
            'username' => 'mkdlogger25',
             'name' => 'Logger Account',
            'password' => Hash::make("mkd2025-logger"),
            'role' => UserRole::Logger,
        ]);
    }
}
