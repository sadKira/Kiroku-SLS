<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;
use Carbon\Carbon;

class LogRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolYear = '2025-2026';
        $students = Student::all();
        
        if ($students->isEmpty()) {
            return;
        }
        
        // Get all log sessions for the school year
        $logSessions = LogSession::where('school_year', $schoolYear)->get();
        
        if ($logSessions->isEmpty()) {
            return;
        }
        
        // Create log records for each session
        foreach ($logSessions as $session) {
            $sessionDate = Carbon::parse($session->date);
            $isToday = $sessionDate->isToday();
            
            // For today, create more records (10-20)
            // For other days, create fewer (5-15)
            $recordCount = $isToday ? rand(10, 20) : rand(5, 15);
            
            // Select random students for this session
            $selectedStudents = $students->random(min($recordCount, $students->count()));
            
            foreach ($selectedStudents as $student) {
                // Generate time_in based on the session date (8 AM - 5 PM)
                $timeIn = $sessionDate->copy()
                    ->setTime(rand(8, 17), rand(0, 59), 0);
                
                // 70% chance of having time_out (some students might still be logged in)
                $timeOut = null;
                if (rand(1, 100) <= 70) {
                    $timeOutCandidate = $timeIn->copy()
                        ->addHours(rand(1, 4))
                        ->addMinutes(rand(0, 59));
                    $endOfDay = $timeIn->copy()->setTime(17, 59, 59);
                    $timeOut = $timeOutCandidate > $endOfDay ? $endOfDay : $timeOutCandidate;
                }
                
                LogRecord::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'log_session_id' => $session->id,
                    ],
                    [
                        'student_id' => $student->id,
                        'log_session_id' => $session->id,
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                    ]
                );
            }
        }
    }
}
