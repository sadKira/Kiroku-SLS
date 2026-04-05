<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\LogSessionFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            LoggerSeeder::class,

            // Reference Data
            CourseSeeder::class,
            StrandSeeder::class,
            InstructionalLevelSeeder::class,

            // Swap
            StudentSeeder::class,
            FacultySeeder::class,
            SchoolYearSettingSeeder::class,

            // Swap all
            SettingSeeder::class,

            LogSessionSeeder::class,
            LogRecordSeeder::class,
        ]);
       
    }
}
