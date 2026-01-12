<?php

namespace Database\Seeders;

use App\Models\SchoolYearSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolYearSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default school year setting
        SchoolYearSetting::firstOrCreate(
            ['school_year' => '2025-2026'],
            ['is_active' => true]
        );
    }
}
