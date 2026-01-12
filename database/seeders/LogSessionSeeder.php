<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LogSession;
use Carbon\Carbon;

class LogSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolYear = '2025-2026';
        $currentYear = Carbon::now()->year;
        
        // Create one log session per month from January to December for current year
        for ($month = 1; $month <= 12; $month++) {
            // Use the 15th of each month as a representative date
            $date = Carbon::create($currentYear, $month, 15);
            
            LogSession::firstOrCreate(
                [
                    'date' => $date->format('Y-m-d'),
                    'school_year' => $schoolYear,
                ],
                [
                    'date' => $date->format('Y-m-d'),
                    'school_year' => $schoolYear,
                ]
            );
        }
        
        // Create a log session for today
        $today = Carbon::now('Asia/Manila');
        LogSession::firstOrCreate(
            [
                'date' => $today->format('Y-m-d'),
                'school_year' => $schoolYear,
            ],
            [
                'date' => $today->format('Y-m-d'),
                'school_year' => $schoolYear,
            ]
        );
        
        // Create log sessions for the last 7 days
        for ($i = 1; $i <= 6; $i++) {
            $date = $today->copy()->subDays($i);
            LogSession::firstOrCreate(
                [
                    'date' => $date->format('Y-m-d'),
                    'school_year' => $schoolYear,
                ],
                [
                    'date' => $date->format('Y-m-d'),
                    'school_year' => $schoolYear,
                ]
            );
        }
    }
}
