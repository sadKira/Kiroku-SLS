<?php

namespace App\Livewire\Management;

use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\SchoolYearSetting;
use App\Models\Student;
use App\Models\Faculty;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AdminDashboard extends Component
{
    // Time filter tabs
    public $timeFilter = 'all'; // all, today, last_7_days, this_month
    
    public function updatedTimeFilter()
    {
        // This will trigger a re-render and chart update
    }
    
    // School year setting modal
    public $start_year = '';
    public $end_year = '';
    public $school_year = '';

    // Export report modal
    public $exportReportType = 'monthly'; // 'monthly' or 'semestral'
    public $exportMonth = '';
    public $exportSchoolYear = '';
    public $exportPaperSize = 'A4';

    public function mount()
    {
        // Ensure there's an active school year
        if (!SchoolYearSetting::getActive()) {
            SchoolYearSetting::setActive('2025-2026');
        }
    }

    // Get active school year
    public function getActiveSchoolYearProperty()
    {
        $setting = SchoolYearSetting::getActive();
        return $setting ? $setting->school_year : '2025-2026';
    }

    // Open set school year modal
    public function openSetSchoolYearModal()
    {
        $this->start_year = '';
        $this->end_year = '';
        $this->school_year = '';
        $this->resetErrorBag();
        Flux::modal('set-academic-year')->show();
    }

    // Auto-calculate end_year when start_year changes
    public function updatedStartYear($value)
    {
        if (!empty($value) && is_numeric($value) && strlen($value) === 4) {
            $this->end_year = (string) ((int) $value + 1);
            $this->school_year = $value . '-' . $this->end_year;
        } else {
            $this->end_year = '';
            $this->school_year = '';
        }
    }

    // Set school year
    public function setSchoolYear()
    {
        $this->validate([
            'start_year' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
            'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
        ], [
            'start_year.required' => 'Start year is required.',
            'start_year.size' => 'Start year must be 4 digits.',
            'start_year.regex' => 'Start year must be a valid 4-digit year.',
            'school_year.required' => 'School year is required.',
            'school_year.regex' => 'School year must be in the format YYYY-YYYY.',
        ]);

        SchoolYearSetting::setActive($this->school_year);

        Flux::modals()->close();
        $this->resetSetSchoolYearForm();

        // Dispatch event to reload charts
        $this->dispatch('school-year-changed');

        $this->dispatch('notify',
            type: 'success',
            content: 'Academic year set successfully.',
            duration: 5000
        );
    }

    // Reset set school year form
    public function resetSetSchoolYearForm()
    {
        $this->start_year = '';
        $this->end_year = '';
        $this->school_year = '';
        Flux::modals()->close();
    }

    // Get available academic years from log sessions
    public function getAvailableAcademicYearsProperty()
    {
        return LogSession::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year')
            ->toArray();
    }

    // Open export report modal
    public function openExportReportModal($reportType = 'monthly')
    {
        $this->exportReportType = $reportType;
        $this->exportMonth = '';
        $this->exportSchoolYear = '';
        $this->exportPaperSize = 'A4';
        $this->resetErrorBag();
        Flux::modal('export-dashboard-report')->show();
    }

    // Reset fields when report type changes
    public function updatedExportReportType()
    {
        $this->exportMonth = '';
        $this->resetErrorBag();
    }

    // Reset export form
    public function resetExportForm()
    {
        $this->exportReportType = 'monthly';
        $this->exportMonth = '';
        $this->exportSchoolYear = '';
        $this->exportPaperSize = 'A4';
        Flux::modals()->close();
    }

    // Export dashboard report
    public function exportDashboardReport()
    {
        $this->validate([
            'exportReportType' => ['required', 'in:monthly,semestral'],
            'exportSchoolYear' => ['required', 'string', 'min:1'],
            'exportMonth' => ['required_if:exportReportType,monthly', 'nullable', 'string'],
            'exportPaperSize' => ['required', 'in:A4,Letter,Legal'],
        ], [
            'exportReportType.required' => 'Report type is required.',
            'exportReportType.in' => 'Invalid report type selected.',
            'exportSchoolYear.required' => 'Academic year is required. Please select an academic year.',
            'exportSchoolYear.min' => 'Academic year is required. Please select an academic year.',
            'exportMonth.required_if' => 'Month is required for monthly reports. Please select a month.',
            'exportPaperSize.required' => 'Paper size is required.',
            'exportPaperSize.in' => 'Invalid paper size selected.',
        ]);

        // Store values before resetting
        $reportType = $this->exportReportType;
        $schoolYear = $this->exportSchoolYear;
        $month = $this->exportMonth;
        $paperSize = $this->exportPaperSize;

        // Reset form fields
        $this->resetExportForm();

        // Show info toast before redirecting
        $this->dispatch('notify',
            type: 'info',
            content: 'Generating dashboard report PDF...',
            duration: 5000
        );

        // Redirect to export route
        return redirect()->route('export_dashboard_report', [
            'report_type' => $reportType,
            'school_year' => $schoolYear,
            'month' => $month,
            'paper_size' => $paperSize,
        ]);
    }

    // Get date range for the whole year
    protected function getDateRange()
    {
        $now = Carbon::now('Asia/Manila');
        
        return [
            'start' => $now->copy()->startOfYear(),
            'end' => $now->copy()->endOfYear(),
        ];
    }

    // Total UNIQUE Students who logged (filtered by school year and time)
    // This counts unique students across all log sessions in the date range
    public function getTotalLogsProperty()
    {
        $range = $this->getDateRange();
        $schoolYear = $this->activeSchoolYear;
        
        // Count unique students and faculty across all sessions
        $stats = DB::table('log_records')
            ->join('log_sessions', 'log_records.log_session_id', '=', 'log_sessions.id')
            ->selectRaw('COUNT(DISTINCT log_records.student_id) as student_count, COUNT(DISTINCT log_records.faculty_id) as faculty_count')
            ->where('log_sessions.school_year', $schoolYear)
            ->whereBetween('log_sessions.date', [$range['start']->format('Y-m-d'), $range['end']->format('Y-m-d')])
            ->first();

        return ($stats->student_count ?? 0) + ($stats->faculty_count ?? 0);
    }

    // Unique Students (filtered) - Same as totalLogs since we're counting unique students
    public function getUniqueStudentsProperty()
    {
        return $this->totalLogs;
    }

    // Total Users (all time - college students + shs students + faculty)
    public function getTotalStudentsProperty()
    {
        $collegeCount = Student::college()->count();
        $shsCount = Student::shs()->count();
        $facultyCount = Faculty::count();

        return $collegeCount + $shsCount + $facultyCount;
    }

    // Breakdown of total users by type (for tooltip / future use)
    public function getTotalUserBreakdownProperty()
    {
        return [
            'college' => Student::college()->count(),
            'shs'     => Student::shs()->count(),
            'faculty' => Faculty::count(),
        ];
    }

    // Active Users Today (unique students + faculty for the active school year)
    public function getActiveStudentsTodayProperty()
    {
        $today = Carbon::now('Asia/Manila')->startOfDay();
        $schoolYear = $this->activeSchoolYear;
        
        $stats = DB::table('log_records')
            ->join('log_sessions', 'log_records.log_session_id', '=', 'log_sessions.id')
            ->selectRaw('COUNT(DISTINCT log_records.student_id) as student_count, COUNT(DISTINCT log_records.faculty_id) as faculty_count')
            ->where('log_sessions.school_year', $schoolYear)
            ->whereDate('log_sessions.date', $today->format('Y-m-d'))
            ->whereNotNull('log_records.time_in')
            ->first();

        return ($stats->student_count ?? 0) + ($stats->faculty_count ?? 0);
    }

    // Attendance percentage (active users / total users)
    public function getAttendancePercentageProperty()
    {
        $total = $this->totalStudents;
        if ($total == 0) return 0;
        
        $active = $this->activeStudentsToday;
        return round(($active / $total) * 100, 1);
    }

    // Log Sessions Count (filtered)
    public function getLogSessionsCountProperty()
    {
        $range = $this->getDateRange();
        $schoolYear = $this->activeSchoolYear;
        
        return LogSession::where('school_year', $schoolYear)
            ->whereBetween('date', [$range['start']->format('Y-m-d'), $range['end']->format('Y-m-d')])
            ->count();
    }

    // Monthly activity data for chart (12 months) - counts UNIQUE users (students + faculty) per month
    public function getActivityChartDataProperty()
    {
        $range = $this->getDateRange();
        $schoolYear = $this->activeSchoolYear;
        
        // Single optimized query to get monthly unique counts
        $monthlyStats = DB::table('log_records')
            ->join('log_sessions', 'log_records.log_session_id', '=', 'log_sessions.id')
            ->selectRaw('
                MONTH(log_sessions.date) as int_month,
                COUNT(DISTINCT log_records.student_id) as student_count,
                COUNT(DISTINCT log_records.faculty_id) as faculty_count
            ')
            ->where('log_sessions.school_year', $schoolYear)
            ->whereBetween('log_sessions.date', [$range['start']->format('Y-m-d'), $range['end']->format('Y-m-d')])
            ->groupBy('int_month')
            ->get()
            ->keyBy('int_month');
            
        $data = [];
        $current = $range['start']->copy();
        while ($current <= $range['end']) {
            $month = $current->month;
            $stat = $monthlyStats->get($month);
            
            $data[] = [
                'label' => $current->format('M'),
                'value' => $stat ? ($stat->student_count + $stat->faculty_count) : 0,
            ];
            $current->addMonth();
        }
        return $data;
    }

    // Get chart title value (total unique students for the period)
    public function getChartTitleValueProperty()
    {
        return number_format($this->totalLogs);
    }

    // Monthly daily activity data for time-series chart (current month) - counts UNIQUE users (students + faculty) per day
    public function getMonthlyDailyActivityProperty()
    {
        $now = Carbon::now('Asia/Manila');
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $schoolYear = $this->activeSchoolYear;
        
        // Single optimized query to get daily unique counts
        $dailyStats = DB::table('log_records')
            ->join('log_sessions', 'log_records.log_session_id', '=', 'log_sessions.id')
            ->selectRaw('
                DATE(log_sessions.date) as full_date,
                COUNT(DISTINCT log_records.student_id) as student_count,
                COUNT(DISTINCT log_records.faculty_id) as faculty_count
            ')
            ->where('log_sessions.school_year', $schoolYear)
            ->whereBetween('log_sessions.date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
            ->groupBy('full_date')
            ->get()
            ->keyBy('full_date');

        $data = [];
        $current = $monthStart->copy();
        
        while ($current <= $monthEnd) {
            $dateStr = $current->format('Y-m-d');
            $stat = $dailyStats->get($dateStr);
            
            $data[] = [
                'x' => $current->copy()->startOfDay()->timestamp * 1000,
                'y' => $stat ? ($stat->student_count + $stat->faculty_count) : 0,
            ];
            $current->addDay();
        }
        
        return $data;
    }

    // Today's hourly activity data (8am-5pm) for sparkline chart - counts UNIQUE users (students + faculty) per hour
    public function getTodayHourlyActivityProperty()
    {
        $today = Carbon::now('Asia/Manila')->startOfDay();
        $schoolYear = $this->activeSchoolYear;
        
        $logSession = LogSession::where('school_year', $schoolYear)
            ->whereDate('date', $today->format('Y-m-d'))
            ->first();
        
        if (!$logSession) {
            return [];
        }
        
        // Single optimized query for hourly data
        $hourlyStats = DB::table('log_records')
            ->selectRaw('
                HOUR(time_in) as log_hour,
                COUNT(DISTINCT student_id) as student_count,
                COUNT(DISTINCT faculty_id) as faculty_count
            ')
            ->where('log_session_id', $logSession->id)
            ->whereNotNull('time_in')
            ->whereRaw('HOUR(time_in) BETWEEN 8 AND 17')
            ->groupBy('log_hour')
            ->get()
            ->keyBy('log_hour');
        
        $data = [];
        
        // Generate data for each hour from 8am to 5pm
        for ($hour = 8; $hour <= 17; $hour++) {
            $hourStart = $today->copy()->setHour($hour)->setMinute(0)->setSecond(0);
            
            $stat = $hourlyStats->get($hour);
            
            $data[] = [
                'x' => $hourStart->timestamp * 1000,
                'y' => $stat ? ($stat->student_count + $stat->faculty_count) : 0,
            ];
        }
        
        return $data;
    }

    // Today's log records filtered by hour (8am-5pm) for timeline
    // This shows ALL records (students + faculty) for the timeline display
    public function getTodayLogRecordsProperty()
    {
        $today = Carbon::now('Asia/Manila')->startOfDay();
        $schoolYear = $this->activeSchoolYear;
        
        $logSession = LogSession::where('school_year', $schoolYear)
            ->whereDate('date', $today->format('Y-m-d'))
            ->first();
        
        if (!$logSession) {
            return collect();
        }
        
        $records = LogRecord::where('log_session_id', $logSession->id)
            ->with(['student', 'faculty'])
            ->whereNotNull('time_in')
            ->get()
            ->filter(function ($record) {
                $hour = Carbon::parse($record->time_in)->hour;
                return $hour >= 8 && $hour <= 17; // 8am to 5pm
            })
            ->sortBy('time_in') // earliest first for timeline
            ->values();
        
        return $records;
    }

    public function render()
    {
        return view('livewire.management.admin-dashboard', [
            'activeSchoolYear' => $this->activeSchoolYear,
            'totalLogs' => $this->totalLogs,
            'uniqueStudents' => $this->uniqueStudents,
            'totalStudents' => $this->totalStudents,
            'activeStudentsToday' => $this->activeStudentsToday,
            'attendancePercentage' => $this->attendancePercentage,
            'logSessionsCount' => $this->logSessionsCount,
            'activityChartData' => $this->activityChartData,
            'monthlyDailyActivity' => $this->monthlyDailyActivity,
            'todayHourlyActivity' => $this->todayHourlyActivity,
            'chartTitleValue' => $this->chartTitleValue,
            'todayLogRecords' => $this->todayLogRecords,
            'availableAcademicYears' => $this->availableAcademicYears,
        ]);
    }
}