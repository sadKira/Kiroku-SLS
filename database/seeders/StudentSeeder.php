<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;
use RuntimeException;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Student::factory(30)->create();

        $sql = file_get_contents(database_path('kiroku.sql'));

        if ($sql === false) {
            throw new RuntimeException('Unable to read database/kiroku.sql.');
        }

        preg_match_all(
            "/\(\d+,\s*'[^']*',\s*'([^']*)',\s*'([^']*)',\s*'([^']*)',\s*'([^']*)',\s*'[^']*',\s*'[^']*'\)[,;]/",
            $sql,
            $studentMatches,
            PREG_SET_ORDER
        );

        $students = array_map(
            static fn (array $match): array => [
                'last_name' => $match[1],
                'first_name' => $match[2],
                'year_level' => $match[3],
                'course' => $match[4],
            ],
            $studentMatches
        );

        if ($students === []) {
            throw new RuntimeException('No student rows found in database/kiroku.sql.');
        }

        $courseMap = Course::query()
            ->pluck('name')
            ->mapWithKeys(static fn (string $name): array => [$name => $name])
            ->all();

        foreach ($students as $student) {
            if (! isset($courseMap[$student['course']])) {
                throw new RuntimeException("Course [{$student['course']}] is not seeded in CourseSeeder.");
            }

            Student::updateOrCreate(
                [
                    'user_type'  => 'college',
                    'last_name'  => $student['last_name'],
                    'first_name' => $student['first_name'],
                    'course'     => $courseMap[$student['course']],
                ],
                [
                    'year_level' => $student['year_level'],
                    'strand'     => null,
                ]
            );
        }
    }
}
