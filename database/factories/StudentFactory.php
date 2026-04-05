<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userType = fake()->randomElement(['college', 'shs']);

        return [
            'user_type' => $userType,
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'year_level' => $userType === 'college'
                ? fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year'])
                : fake()->randomElement(['Grade 11', 'Grade 12']),
            'course' => $userType === 'college'
                ? fake()->randomElement([
                    'Bachelor of Arts in International Studies',
                    'Bachelor of Science in Information Systems',
                    'Bachelor of Human Services',
                    'Bachelor of Secondary Education',
                    'Bachelor of Elementary Education',
                    'Bachelor of Special Needs Education',
                ])
                : null,
            'strand' => $userType === 'shs'
                ? fake()->randomElement([
                    'Science, Technology, Engineering, and Mathematics',
                    'Accountancy, Business and Management',
                    'Humanities and Social Sciences',
                    'General Academic Strand',
                    'Technical-Vocational-Livelihood',
                    'Sports Track',
                    'Arts and Design Track',
                ])
                : null,
        ];
    }

    /**
     * State for college students.
     */
    public function college(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'college',
            'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
            'course' => fake()->randomElement([
                'Bachelor of Arts in International Studies',
                'Bachelor of Science in Information Systems',
                'Bachelor of Human Services',
                'Bachelor of Secondary Education',
                'Bachelor of Elementary Education',
                'Bachelor of Special Needs Education',
            ]),
            'strand' => null,
        ]);
    }

    /**
     * State for SHS students.
     */
    public function shs(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'shs',
            'year_level' => fake()->randomElement(['Grade 11', 'Grade 12']),
            'course' => null,
            'strand' => fake()->randomElement([
                'Science, Technology, Engineering, and Mathematics',
                'Accountancy, Business and Management',
                'Humanities and Social Sciences',
                'General Academic Strand',
                'Technical-Vocational-Livelihood',
                'Sports Track',
                'Arts and Design Track',
            ]),
        ]);
    }
}
