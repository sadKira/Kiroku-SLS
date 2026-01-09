<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LogSession>
 */
class LogSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $schoolYear = fake()->randomElement([
            '2024-2025',
            '2025-2026',
            '2026-2027',
        ]);

        $startYear = (int) substr($schoolYear, 0, 4);
        $startDate = sprintf('%d-06-01', $startYear);
        $endDate = sprintf('%d-03-31', $startYear + 1);

        return [
            'date' => fake()->dateTimeBetween($startDate, $endDate)->format('Y-m-d'),
            'school_year' => $schoolYear,
        ];
    }
}
