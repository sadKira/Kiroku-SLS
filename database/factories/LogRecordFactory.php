<?php

namespace Database\Factories;

use App\Models\LogSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LogRecord>
 */
class LogRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate 'time_in' within a specific range (e.g., last 30 days)
        $timeIn = $this->faker->dateTimeBetween('-30 days', 'now');

        // Generate 'time_out' starting from 'time_in' up to a maximum duration (e.g., +12 hours)
        // You can use a relative string like '+8 hours' as the second parameter
        $timeOut = $this->faker->dateTimeBetween($timeIn, $timeIn->format('Y-m-d H:i:s').' +12 hours');

        return [
            'student_id' => Student::factory(),
            'log_session_id' => fake()->randomElement([
            '1',
            '2',
            ]),

            'time_in'  => $timeIn,
            'time_out' => $timeOut,
        ];
    }
}
