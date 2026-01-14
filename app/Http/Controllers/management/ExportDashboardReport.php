<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class ExportDashboardReport extends Controller
{
    public function generatePdf(Request $request)
    {
        try {
            // Get report type from request
            $reportType = $request->input('report_type'); // 'monthly' or 'semestral'
            $paperSize = $request->input('paper_size', 'A4');
            $month = $request->input('month');
            $schoolYear = $request->input('school_year');

            // Validate paper size
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (!in_array($paperSize, $validPaperSizes)) {
                $paperSize = 'A4';
            }

            // Validate report type
            if (!in_array($reportType, ['monthly', 'semestral'])) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'Invalid report type selected.',
                    'duration' => 5000
                ]);
            }

            // Validate school year
            if (empty($schoolYear)) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'School year is required.',
                    'duration' => 5000
                ]);
            }

            // Validate month for monthly reports
            if ($reportType === 'monthly' && empty($month)) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'Month is required for monthly reports.',
                    'duration' => 5000
                ]);
            }

            // Get date range based on report type
            $dateRange = $this->getDateRange($reportType, $month, $schoolYear);
            
            // Get log sessions for the date range and school year
            $logSessions = LogSession::where('school_year', $schoolYear)
                ->whereBetween('date', [$dateRange['start'], $dateRange['end']])
                ->withCount('logRecords')
                ->orderBy('date')
                ->get();

            if ($logSessions->isEmpty()) {
                return redirect()->back()->with('notify', [
                    'type' => 'error',
                    'content' => 'No log sessions found for the selected period.',
                    'duration' => 5000
                ]);
            }

            // Get log records for the period
            $logSessionIds = $logSessions->pluck('id');
            $logRecords = LogRecord::whereIn('log_session_id', $logSessionIds)
                ->with(['student', 'logSessions'])
                ->get();

            // Calculate statistics
            $stats = $this->calculateStatistics($logRecords, $logSessions, $dateRange, $reportType);

            // Render the export view to HTML
            $html = view('Reports/export-dashboard-report', [
                'reportType' => $reportType,
                'schoolYear' => $schoolYear,
                'month' => $month,
                'dateRange' => $dateRange,
                'logSessions' => $logSessions,
                'logRecords' => $logRecords,
                'stats' => $stats,
            ])->render();

            // Generate the PDF
            $pdf = Browsershot::html($html)
                ->format($paperSize)
                ->showBackground()
                ->margins(10, 10, 10, 10)
                ->scale(0.9)
                ->pdf();

            // Generate filename
            $filename = $this->generateFilename($reportType, $month, $schoolYear, $paperSize);

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot $e) {
            Log::error('Browsershot error generating dashboard report PDF', [
                'error' => $e->getMessage(),
                'report_type' => $request->input('report_type'),
                'paper_size' => $paperSize ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'Unable to generate PDF. Please ensure the PDF generation service is properly configured.',
                'duration' => 5000
            ]);

        } catch (Exception $e) {
            Log::error('Error generating dashboard report PDF', [
                'error' => $e->getMessage(),
                'report_type' => $request->input('report_type'),
                'paper_size' => $paperSize ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('notify', [
                'type' => 'error',
                'content' => 'An unexpected error occurred while generating the PDF. Please try again or contact support if the problem persists.',
                'duration' => 5000
            ]);
        }
    }

    private function getDateRange($reportType, $month, $schoolYear)
    {
        $now = Carbon::now('Asia/Manila');
        
        if ($reportType === 'monthly') {
            // Parse school year (e.g., "2025-2026")
            $yearParts = explode('-', $schoolYear);
            $startYear = (int) $yearParts[0];
            $endYear = (int) $yearParts[1] ?? ($startYear + 1);
            
            // Get month number
            $monthNumber = $this->getMonthNumber($month);
            if (!$monthNumber) {
                throw new Exception('Invalid month provided.');
            }

            // For school year 2025-2026:
            // - June-December (6-12) use startYear (2025)
            // - January-May (1-5) use endYear (2026)
            $year = ($monthNumber >= 6) ? $startYear : $endYear;

            $start = Carbon::create($year, $monthNumber, 1, 0, 0, 0, 'Asia/Manila')->startOfMonth();
            $end = $start->copy()->endOfMonth();
        } else {
            // Semestral: Cover entire school year (June of start year to May of end year)
            $yearParts = explode('-', $schoolYear);
            $startYear = (int) $yearParts[0];
            $endYear = (int) $yearParts[1] ?? ($startYear + 1);
            
            // School year typically runs from June to May of the following year
            $start = Carbon::create($startYear, 6, 1, 0, 0, 0, 'Asia/Manila')->startOfMonth();
            $end = Carbon::create($endYear, 5, 31, 23, 59, 59, 'Asia/Manila')->endOfMonth();
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
        ];
    }

    private function getMonthNumber($monthName)
    {
        $months = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12,
        ];

        return $months[$monthName] ?? null;
    }

    private function calculateStatistics($logRecords, $logSessions, $dateRange, $reportType)
    {
        // Total log records
        $totalLogs = $logRecords->count();
        
        // Unique students
        $uniqueStudents = $logRecords->pluck('student_id')->unique()->count();
        
        // Total students (all time)
        $totalStudents = Student::count();
        
        // Log sessions count
        $logSessionsCount = $logSessions->count();
        
        // Daily activity data for chart
        $dailyActivity = [];
        $current = Carbon::parse($dateRange['start'], 'Asia/Manila');
        $end = Carbon::parse($dateRange['end'], 'Asia/Manila');
        
        // Create a map of log session IDs by date for faster lookup
        $sessionsByDate = [];
        foreach ($logSessions as $session) {
            $dateStr = Carbon::parse($session->date)->format('Y-m-d');
            if (!isset($sessionsByDate[$dateStr])) {
                $sessionsByDate[$dateStr] = [];
            }
            $sessionsByDate[$dateStr][] = $session->id;
        }
        
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $sessionIds = $sessionsByDate[$dateStr] ?? [];
            $count = $logRecords->filter(function ($record) use ($sessionIds) {
                return in_array($record->log_session_id, $sessionIds);
            })->count();
            
            $dailyActivity[] = [
                'date' => $dateStr,
                'label' => $current->format('M j'),
                'value' => $count,
            ];
            
            $current->addDay();
        }
        
        // Monthly activity data (for semestral reports)
        $monthlyActivity = [];
        if ($reportType === 'semestral') {
            $current = Carbon::parse($dateRange['start'], 'Asia/Manila');
            $end = Carbon::parse($dateRange['end'], 'Asia/Manila');
            
            while ($current <= $end) {
                $monthStart = $current->copy()->startOfMonth();
                $monthEnd = $current->copy()->endOfMonth();
                
                // Ensure we don't go beyond the date range
                if ($monthStart->lt(Carbon::parse($dateRange['start'], 'Asia/Manila'))) {
                    $monthStart = Carbon::parse($dateRange['start'], 'Asia/Manila');
                }
                if ($monthEnd->gt(Carbon::parse($dateRange['end'], 'Asia/Manila'))) {
                    $monthEnd = Carbon::parse($dateRange['end'], 'Asia/Manila');
                }
                
                $sessionIds = $logSessions->filter(function ($session) use ($monthStart, $monthEnd) {
                    $sessionDate = Carbon::parse($session->date);
                    return $sessionDate->between($monthStart, $monthEnd);
                })->pluck('id');
                
                $count = LogRecord::whereIn('log_session_id', $sessionIds)->count();
                
                $monthlyActivity[] = [
                    'label' => $current->format('M'),
                    'value' => $count,
                ];
                
                $current->addMonth();
            }
        }
        
        // Course distribution
        $courseDistribution = $logRecords->filter(function ($record) {
            return $record->student && $record->student->course;
        })->groupBy(function ($record) {
            return $record->student->course;
        })->map(function ($group) {
            return $group->count();
        })->toArray();
        
        return [
            'totalLogs' => $totalLogs,
            'uniqueStudents' => $uniqueStudents,
            'totalStudents' => $totalStudents,
            'logSessionsCount' => $logSessionsCount,
            'dailyActivity' => $dailyActivity,
            'monthlyActivity' => $monthlyActivity,
            'courseDistribution' => $courseDistribution,
        ];
    }

    private function generateFilename($reportType, $month, $schoolYear, $paperSize)
    {
        $type = $reportType === 'monthly' ? 'monthly' : 'semestral';
        $monthPart = $month ? strtolower($month) : '';
        $yearPart = str_replace('-', '-', $schoolYear);
        
        if ($reportType === 'monthly') {
            return "dashboard-report-{$type}-{$monthPart}-{$yearPart}-{$paperSize}.pdf";
        } else {
            return "dashboard-report-{$type}-{$yearPart}-{$paperSize}.pdf";
        }
    }
}
