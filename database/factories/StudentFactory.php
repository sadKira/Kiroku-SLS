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
        return [
            'id_student' => str_pad(mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT),
            'last_name' => fake()->lastName(),
            'first_name' => fake()->firstName(),
            'year_level' => fake()->randomElement([
                '1st Year',
                '2nd Year',
                '3rd Year',
                '4th Year',
            ]),
            'course' => fake()->randomElement([
                'Bachelor of Arts in International Studies',
                'Bachelor of Science in Information Systems',
                'Bachelor of Human Services',
                'Bachelor of Secondary Education',
                'Bachelor of Elementary Education',
                'Bachelor of Special Needs Education',
            ]),
        ];
    }
}
