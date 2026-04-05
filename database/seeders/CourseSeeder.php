<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            ['code' => 'ABIS', 'name' => 'Bachelor of Arts in International Studies'],
            ['code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems'],
            ['code' => 'BHS', 'name' => 'Bachelor of Human Services'],
            ['code' => 'BSED', 'name' => 'Bachelor of Secondary Education'],
            ['code' => 'ECED', 'name' => 'Bachelor of Elementary Education'],
            ['code' => 'SNED', 'name' => 'Bachelor of Special Needs Education'],
        ];

        foreach ($courses as $course) {
            Course::firstOrCreate(
                ['code' => $course['code']],
                ['name' => $course['name']]
            );
        }
    }
}
