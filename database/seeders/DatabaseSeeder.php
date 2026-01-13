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
            // StudentSeeder::class,
            SchoolYearSettingSeeder::class,

            // LogSessionSeeder::class,
            // LogRecordSeeder::class,
        ]);
       
    }
}
