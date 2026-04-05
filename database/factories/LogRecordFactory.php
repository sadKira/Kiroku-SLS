<?php

namespace Database\Factories;

use App\Models\LogSession;
use App\Models\Student;
use App\Models\Faculty;
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
        // Generate date within last 30 days
        $date = $this->faker->dateTimeBetween('-30 days', 'now');

        // Force time_in between 8 AM and 5 PM
        $hourIn = $this->faker->numberBetween(8, 17);
        $minuteIn = $this->faker->numberBetween(0, 59);
        $timeIn = (clone $date)->setTime($hourIn, $minuteIn, 0);

        // 70% chance of having time_out, within working hours window
        $timeOut = null;
        if ($this->faker->boolean(70)) {
            $timeOutCandidate = (clone $timeIn)->modify('+' . $this->faker->numberBetween(1, 4) . ' hours')
                ->modify('+' . $this->faker->numberBetween(0, 59) . ' minutes');
            $endOfDay = (clone $timeIn)->setTime(17, 59, 59);
            $timeOut = $timeOutCandidate > $endOfDay ? $endOfDay : $timeOutCandidate;
        }

        return [
            'loggable_type' => 'student',
            'student_id' => Student::factory(),
            'faculty_id' => null,
            'log_session_id' => LogSession::factory(),
            'time_in'  => $timeIn,
            'time_out' => $timeOut,
        ];
    }

    /**
     * State for faculty log records.
     */
    public function forFaculty(): static
    {
        return $this->state(fn (array $attributes) => [
            'loggable_type' => 'faculty',
            'student_id' => null,
            'faculty_id' => Faculty::factory(),
        ]);
    }
}
