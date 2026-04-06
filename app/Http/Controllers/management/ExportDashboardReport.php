<?php

namespace App\Http\Controllers\management;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\Student;
use App\Models\Strand;
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
            $paperSize  = $request->input('paper_size', 'A4');
            $month      = $request->input('month');
            $schoolYear = $request->input('school_year');

            // Validate paper size
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (!in_array($paperSize, $validPaperSizes)) {
                $paperSize = 'A4';
            }

            // Validate report type
            if (!in_array($reportType, ['monthly', 'semestral'])) {
                return redirect()->back()->with('notify', [
                    'type'     => 'error',
                    'content'  => 'Invalid report type selected.',
                    'duration' => 5000,
                ]);
            }

            // Validate school year
            if (empty($schoolYear)) {
                return redirect()->back()->with('notify', [
                    'type'     => 'error',
                    'content'  => 'School year is required.',
                    'duration' => 5000,
                ]);
            }

            // Validate month for monthly reports
            if ($reportType === 'monthly' && empty($month)) {
                return redirect()->back()->with('notify', [
                    'type'     => 'error',
                    'content'  => 'Month is required for monthly reports.',
                    'duration' => 5000,
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
                    'type'     => 'error',
                    'content'  => 'No log sessions found for the selected period.',
                    'duration' => 5000,
                ]);
            }

            // Get ALL log records for the period (with both student and faculty)
            $logSessionIds = $logSessions->pluck('id');
            $logRecords = LogRecord::whereIn('log_session_id', $logSessionIds)
                ->with(['student', 'faculty', 'logSessions'])
                ->get();

            // Calculate statistics
            $stats = $this->calculateStatistics($logRecords, $logSessions, $dateRange, $reportType, $schoolYear);

            // Render the export view to HTML
            $html = view('Reports/export-dashboard-report', [
                'reportType'  => $reportType,
                'schoolYear'  => $schoolYear,
                'month'       => $month,
                'dateRange'   => $dateRange,
                'logSessions' => $logSessions,
                'logRecords'  => $logRecords,
                'stats'       => $stats,
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
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot $e) {
            Log::error('Browsershot error generating dashboard report PDF', [
                'error'       => $e->getMessage(),
                'report_type' => $request->input('report_type'),
                'paper_size'  => $paperSize ?? 'unknown',
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('notify', [
                'type'     => 'error',
                'content'  => 'Unable to generate PDF. Please ensure the PDF generation service is properly configured.',
                'duration' => 5000,
            ]);

        } catch (Exception $e) {
            Log::error('Error generating dashboard report PDF', [
                'error'       => $e->getMessage(),
                'report_type' => $request->input('report_type'),
                'paper_size'  => $paperSize ?? 'unknown',
                'trace'       => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('notify', [
                'type'     => 'error',
                'content'  => 'An unexpected error occurred while generating the PDF. Please try again or contact support if the problem persists.',
                'duration' => 5000,
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
            $logSession = LogSession::where('school_year', $schoolYear)
                ->whereMonth('date', $monthNumber)
                ->orderBy('date')
                ->first();

            if (!$logSession) {
                // Educated guess based on school year
                $yearParts = explode('-', $schoolYear);
                $startYear = (int) $yearParts[0];
                $endYear   = (int) ($yearParts[1] ?? ($startYear + 1));
                $year      = ($monthNumber >= 6) ? $startYear : $endYear;
            } else {
                $year = Carbon::parse($logSession->date)->year;
            }

            $start = Carbon::create($year, $monthNumber, 1, 0, 0, 0, 'Asia/Manila')->startOfMonth();
            $end   = $start->copy()->endOfMonth();
        } else {
            // Semestral: span of actual log session dates for this school year
            $dates = LogSession::where('school_year', $schoolYear)
                ->selectRaw('MIN(date) as first_date, MAX(date) as last_date')
                ->first();

            if (!$dates || !$dates->first_date || !$dates->last_date) {
                throw new Exception('No log sessions found for the selected school year.');
            }

            $start = Carbon::parse($dates->first_date, 'Asia/Manila')->startOfDay();
            $end   = Carbon::parse($dates->last_date, 'Asia/Manila')->endOfDay();
        }

        return [
            'start' => $start->format('Y-m-d'),
            'end'   => $end->format('Y-m-d'),
        ];
    }

    private function getMonthNumber($monthName)
    {
        $months = [
            'January'   => 1,  'February'  => 2,  'March'     => 3,
            'April'     => 4,  'May'        => 5,  'June'      => 6,
            'July'      => 7,  'August'     => 8,  'September' => 9,
            'October'   => 10, 'November'  => 11, 'December'  => 12,
        ];

        return $months[$monthName] ?? null;
    }

    /**
     * Calculate all report statistics.
     *
     * Key design decisions:
     * - "User Logs" = total log record rows (including duplicates / multiple visits)
     *   counts both student_id-based and faculty_id-based records.
     * - "Total Users" = all registered college + shs students + faculty (all-time).
     * - "Library Active Rate" = unique users who logged in the period ÷ total users × 100.
     * - Distributions group by actual log rows (not unique users) to reflect usage volume.
     */
    private function calculateStatistics($logRecords, $logSessions, $dateRange, $reportType, $schoolYear)
    {
        // ── User Logs (actual count, duplicates included) ──────────────────
        $totalUserLogs = $logRecords->count();

        // ── Total registered users (all-time) ──────────────────────────────
        $totalCollege = Student::college()->count();
        $totalShs     = Student::shs()->count();
        $totalFaculty = Faculty::count();
        $totalUsers   = $totalCollege + $totalShs + $totalFaculty;

        // ── Unique users who logged in the period ──────────────────────────
        $uniqueStudents = $logRecords->whereNotNull('student_id')->pluck('student_id')->unique()->count();
        $uniqueFaculty  = $logRecords->whereNotNull('faculty_id')->pluck('faculty_id')->unique()->count();
        $uniqueUsers    = $uniqueStudents + $uniqueFaculty;

        // ── Library / Monthly Active Rate (period unique users / total registered × 100) ──
        // Same formula for both monthly and semestral — $logRecords is already scoped to the period.
        $libraryActiveRate = $totalUsers > 0
            ? round(($uniqueUsers / $totalUsers) * 100, 1)
            : 0;

        // Alias used in monthly overview card
        $monthlyActiveRate = $libraryActiveRate;

        // ── Log sessions count ──────────────────────────────────────────────
        $logSessionsCount = $logSessions->count();

        // ── Build a date → session-id map for fast daily lookups ───────────
        $sessionsByDate = [];
        foreach ($logSessions as $session) {
            $dateStr = Carbon::parse($session->date)->format('Y-m-d');
            $sessionsByDate[$dateStr][] = $session->id;
        }

        // ── Daily activity (actual log counts, not unique) ─────────────────
        $dailyActivity = [];
        $current = Carbon::parse($dateRange['start'], 'Asia/Manila');
        $end     = Carbon::parse($dateRange['end'], 'Asia/Manila');

        while ($current <= $end) {
            $dateStr    = $current->format('Y-m-d');
            $sessionIds = $sessionsByDate[$dateStr] ?? [];

            $count = $logRecords->filter(fn($r) => in_array($r->log_session_id, $sessionIds))->count();

            $dailyActivity[] = [
                'date'  => $dateStr,
                'label' => $current->format('F j, Y'),
                'day'   => $current->format('l'),
                'value' => $count,
            ];

            $current->addDay();
        }

        // ── Monthly activity (semestral only, actual log counts) ───────────
        $monthlyActivity = [];
        if ($reportType === 'semestral') {
            $sessionsByMonth = $logSessions
                ->groupBy(fn($s) => Carbon::parse($s->date)->format('Y-m'))
                ->sortKeys();

            foreach ($sessionsByMonth as $yearMonth => $sessions) {
                $sessionIds = $sessions->pluck('id');
                $count = $logRecords
                    ->filter(fn($r) => $sessionIds->contains($r->log_session_id))
                    ->count();

                $date = Carbon::createFromFormat('Y-m', $yearMonth);
                $monthlyActivity[] = [
                    'label' => $date->format('F Y'),
                    'value' => $count,
                ];
            }
        }

        // ── Course distribution (log count per course, students only) ───────
        $courseDistribution = $logRecords
            ->filter(fn($r) => $r->student && $r->student->course && $r->student->user_type === 'college')
            ->groupBy(fn($r) => $r->student->course)
            ->map(fn($g) => $g->count())
            ->toArray();

        // ── Strand distribution (log count per strand, SHS students only) ──
        $strandDistribution = $logRecords
            ->filter(fn($r) => $r->student && $r->student->strand && $r->student->user_type === 'shs')
            ->groupBy(fn($r) => $r->student->strand)
            ->map(fn($g) => $g->count())
            ->toArray();

        // ── Faculty distribution (log count per instructional level) ────────
        $facultyDistribution = $logRecords
            ->filter(fn($r) => $r->faculty && $r->faculty->instructional_level)
            ->groupBy(fn($r) => $r->faculty->instructional_level)
            ->map(fn($g) => $g->count())
            ->toArray();

        // ── Extrema Calculations (Most/Least active) ────────────────────────
        $mostActiveDay = collect($dailyActivity)->sortByDesc('value')->first();
        $mostActiveDayPercent = ($mostActiveDay && $totalUserLogs > 0) 
            ? round(($mostActiveDay['value'] / $totalUserLogs) * 100, 1) : 0;
            
        $leastActiveDay = collect($dailyActivity)->sortBy('value')->first();
        $leastActiveDayPercent = ($leastActiveDay && $totalUserLogs > 0) 
            ? round(($leastActiveDay['value'] / $totalUserLogs) * 100, 1) : 0;
            
        $mostActiveMonth = collect($monthlyActivity)->sortByDesc('value')->first();
        $mostActiveMonthPercent = ($mostActiveMonth && $totalUserLogs > 0) 
            ? round(($mostActiveMonth['value'] / $totalUserLogs) * 100, 1) : 0;

        $leastActiveMonth = collect($monthlyActivity)->sortBy('value')->first();
        $leastActiveMonthPercent = ($leastActiveMonth && $totalUserLogs > 0) 
            ? round(($leastActiveMonth['value'] / $totalUserLogs) * 100, 1) : 0;

        $getMinMax = function($dist, $allKeys, $codeMap = null) {
            if (empty($allKeys)) return ['max' => 'N/A', 'min' => 'N/A'];
            // Ensure all possible keys exist with at least 0
            $fullDist = array_fill_keys($allKeys, 0);
            foreach ($dist as $k => $v) $fullDist[$k] = $v;
            
            $maxVal = max($fullDist);
            $minVal = min($fullDist);
            
            $maxKeys = array_keys($fullDist, $maxVal);
            $minKeys = array_keys($fullDist, $minVal);
            
            if ($codeMap) {
                $maxKeys = array_map(fn($k) => $codeMap[$k] ?? $k, $maxKeys);
                $minKeys = array_map(fn($k) => $codeMap[$k] ?? $k, $minKeys);
            }
            
            return [
                'max' => $maxVal > 0 ? implode(', ', $maxKeys) : 'None',
                'min' => implode(', ', $minKeys)
            ];
        };

        $allCourses = \App\Models\Course::pluck('name')->toArray();
        $allStrands = \App\Models\Strand::pluck('name')->toArray();
        $allLevels  = \App\Models\Faculty::whereNotNull('instructional_level')->pluck('instructional_level')->unique()->toArray();
        
        $courseCodes = \App\Models\Course::pluck('code', 'name')->toArray();
        $strandCodes = \App\Models\Strand::pluck('code', 'name')->toArray();

        $courseMinMax = $getMinMax($courseDistribution, $allCourses, $courseCodes);
        $strandMinMax = $getMinMax($strandDistribution, $allStrands, $strandCodes);
        $facultyMinMax = $getMinMax($facultyDistribution, $allLevels);

        return [
            'totalUserLogs'        => $totalUserLogs,
            'totalUsers'           => $totalUsers,
            'totalCollege'         => $totalCollege,
            'totalShs'             => $totalShs,
            'totalFaculty'         => $totalFaculty,
            'uniqueUsers'          => $uniqueUsers,
            'libraryActiveRate'    => $libraryActiveRate,
            'monthlyActiveRate'    => $monthlyActiveRate,
            'logSessionsCount'     => $logSessionsCount,
            'dailyActivity'        => $dailyActivity,
            'monthlyActivity'      => $monthlyActivity,
            'courseDistribution'   => $courseDistribution,
            'strandDistribution'   => $strandDistribution,
            'facultyDistribution'  => $facultyDistribution,
            'mostActiveDay'        => $mostActiveDay,
            'mostActiveDayPercent' => $mostActiveDayPercent,
            'leastActiveDay'       => $leastActiveDay,
            'leastActiveDayPercent'=> $leastActiveDayPercent,
            'mostActiveMonth'      => $mostActiveMonth,
            'mostActiveMonthPercent' => $mostActiveMonthPercent,
            'leastActiveMonth'     => $leastActiveMonth,
            'leastActiveMonthPercent' => $leastActiveMonthPercent,
            'courseMinMax'         => $courseMinMax,
            'strandMinMax'         => $strandMinMax,
            'facultyMinMax'        => $facultyMinMax,
        ];
    }

    private function generateFilename($reportType, $month, $schoolYear, $paperSize)
    {
        $type     = $reportType === 'monthly' ? 'monthly' : 'semestral';
        $yearPart = str_replace('-', '-', $schoolYear);

        if ($reportType === 'monthly') {
            $monthPart = strtolower($month ?? '');
            return "dashboard-report-{$type}-{$monthPart}-{$yearPart}-{$paperSize}.pdf";
        }

        return "dashboard-report-{$type}-{$yearPart}-{$paperSize}.pdf";
    }
}