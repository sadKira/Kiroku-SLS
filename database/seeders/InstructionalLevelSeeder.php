<?php

namespace Database\Seeders;

use App\Models\InstructionalLevel;
use Illuminate\Database\Seeder;

class InstructionalLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['code' => 'COL', 'name' => 'College'],
            ['code' => 'SHS', 'name' => 'Senior High School'],
            ['code' => 'JHS', 'name' => 'Junior High School'],
        ];

        foreach ($levels as $level) {
            InstructionalLevel::firstOrCreate(
                ['code' => $level['code']],
                ['name' => $level['name']]
            );
        }
    }
}
