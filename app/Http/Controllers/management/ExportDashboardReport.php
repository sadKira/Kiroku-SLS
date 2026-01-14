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
            $stats = $this->calculateStatistics($logRecords, $logSessions, $dateRange, $reportType, $schoolYear);

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
        if ($reportType === 'monthly') {
            // Get month number
            $monthNumber = $this->getMonthNumber($month);
            if (!$monthNumber) {
                throw new Exception('Invalid month provided.');
            }

            // Find the actual date range for this month in the school year
            // Query log sessions to find which year this month belongs to
            $logSession = LogSession::where('school_year', $schoolYear)
                ->whereMonth('date', $monthNumber)
                ->orderBy('date')
                ->first();

            if (!$logSession) {
                // If no log session found, make an educated guess based on school year
                $yearParts = explode('-', $schoolYear);
                $startYear = (int) $yearParts[0];
                $endYear = (int) $yearParts[1] ?? ($startYear + 1);
                
                // June-December typically use start year, January-May use end year
                $year = ($monthNumber >= 6) ? $startYear : $endYear;
            } else {
                // Use the year from the actual log session
                $year = Carbon::parse($logSession->date)->year;
            }

            $start = Carbon::create($year, $monthNumber, 1, 0, 0, 0, 'Asia/Manila')->startOfMonth();
            $end = $start->copy()->endOfMonth();
        } else {
            // Semestral: Get ALL log sessions for this school year
            // Simply find the earliest and latest dates in the log sessions
            $dates = LogSession::where('school_year', $schoolYear)
                ->selectRaw('MIN(date) as first_date, MAX(date) as last_date')
                ->first();

            if (!$dates || !$dates->first_date || !$dates->last_date) {
                throw new Exception('No log sessions found for the selected school year.');
            }

            $start = Carbon::parse($dates->first_date, 'Asia/Manila')->startOfDay();
            $end = Carbon::parse($dates->last_date, 'Asia/Manila')->endOfDay();
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

    private function calculateStatistics($logRecords, $logSessions, $dateRange, $reportType, $schoolYear)
    {
        // Total log records
        $totalLogs = $logRecords->count();
        
        // Total students (all time)
        $totalStudents = Student::count();
        
        // Log sessions count
        $logSessionsCount = $logSessions->count();
        
        // Daily activity data (for both monthly and semestral reports)
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
                'label' => $current->format('F j, Y'),
                'day' => $current->format('l'),
                'value' => $count,
            ];
            
            $current->addDay();
        }
        
        // Monthly activity data (for semestral reports)
        $monthlyActivity = [];
        if ($reportType === 'semestral') {
            // Group log sessions by year-month
            $sessionsByMonth = $logSessions->groupBy(function ($session) {
                return Carbon::parse($session->date)->format('Y-m');
            });

            // Sort by year-month
            $sessionsByMonth = $sessionsByMonth->sortKeys();

            foreach ($sessionsByMonth as $yearMonth => $sessions) {
                $sessionIds = $sessions->pluck('id');
                $count = $logRecords->filter(function ($record) use ($sessionIds) {
                    return $sessionIds->contains($record->log_session_id);
                })->count();
                
                $date = Carbon::createFromFormat('Y-m', $yearMonth);
                
                $monthlyActivity[] = [
                    'label' => $date->format('F Y'),
                    'value' => $count,
                ];
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