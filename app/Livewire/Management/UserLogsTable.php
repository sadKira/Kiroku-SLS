<?php

namespace App\Livewire\Management;

use App\Models\LogRecord;
use App\Models\LogSession;
use App\Models\SchoolYearSetting;
use Carbon\Carbon;
use Exception;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Lazy]
class UserLogsTable extends Component
{
    use WithPagination;

    public $date;

    public $start_year;

    public $end_year;

    public $school_year;

    public $logSessionId;

    public $logSessionDate;

    public $logSessionSchoolYear;

    public ?LogSession $selectedLogSession = null;

    public $selectedLogSessionId = null;

    // Track which session is being viewed/exported for loading states
    public $viewingSessionId = null;

    public $exportingSessionId = null;

    // Paper Size for Export
    public $paperSize = 'A4';

    // Filter properties
    public $filterMonth = '';

    public $filterYear = '';

    public $filterAcademicYear = '';

    protected $listeners = ['log-session-created' => '$refresh'];

    // Export User Logs
    public function exportUserLogs($logSessionId, $paperSize = 'A4')
    {
        try {
            $this->exportingSessionId = $logSessionId;
            $this->paperSize = $paperSize;

            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (! in_array($paperSize, $validPaperSizes)) {
                $this->exportingSessionId = null;
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Invalid paper size selected. Please try again.',
                    duration: 5000
                );

                return;
            }

            $logSession = LogSession::find($logSessionId);
            if (! $logSession) {
                $this->exportingSessionId = null;
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Log session not found. Please refresh the page and try again.',
                    duration: 5000
                );

                return;
            }

            $logRecordCount = LogRecord::where('log_session_id', $logSessionId)->count();
            if ($logRecordCount === 0) {
                $this->exportingSessionId = null;
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No log records found for this session. Please add log records first.',
                    duration: 5000
                );

                return;
            }

            $this->dispatch('notify',
                type: 'info',
                content: 'Generating user logs PDF...',
                duration: 5000
            );

            $this->exportingSessionId = null;

            return redirect()->route('export_user_logs', [
                'log_session_id' => $logSessionId,
                'paper_size' => $paperSize,
            ]);

        } catch (Exception $e) {
            $this->exportingSessionId = null;
            Log::error('Error initiating user logs export', [
                'log_session_id' => $logSessionId ?? 'unknown',
                'paper_size' => $paperSize ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again or contact support if the problem persists.',
                duration: 5000
            );
        }
    }

    // Reset filters
    public function resetFilters()
    {
        $this->filterMonth = '';
        $this->filterYear = '';
        $this->filterAcademicYear = '';
        $this->resetPage();
    }

    // Clear Filters
    public function clearFilterMonth()
    {
        $this->filterMonth = '';
        $this->resetPage();
    }

    public function clearFilterYear()
    {
        $this->filterYear = '';
        $this->resetPage();
    }

    public function clearFilterAcademicYear()
    {
        $this->filterAcademicYear = '';
        $this->resetPage();
    }

    // Add Log Session
    public function addLogSession()
    {
        $this->date = '';

        $activeSetting = SchoolYearSetting::getActive();
        if ($activeSetting && !empty($activeSetting->school_year)) {
            $this->school_year = $activeSetting->school_year;
            $parts = explode('-', $this->school_year);
            if (count($parts) === 2) {
                $this->start_year = $parts[0];
                $this->end_year = $parts[1];
            }
        } else {
            $this->start_year = '';
            $this->end_year = '';
            $this->school_year = '';
        }

        $this->resetErrorBag();

        Flux::modal('create-log-session')->show();
    }

    // Auto-calculate end_year when start_year changes
    public function updatedStartYear($value)
    {
        if (! empty($value) && is_numeric($value) && strlen($value) === 4) {
            $this->end_year = (string) ((int) $value + 1);
            $this->school_year = $value.'-'.$this->end_year;
        } else {
            $this->end_year = '';
            $this->school_year = '';
        }
    }

    // Add Log Session Validation
    public function addLogSessionInformation()
    {
        try {
            $validated = $this->validate([
                'date' => ['required', 'date'],
                'start_year' => ['required', 'string', 'size:4', 'regex:/^\d{4}$/'],
                'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            ], [
                'date.required' => 'Date is required.',
                'date.date' => 'Date must be a valid date.',
                'start_year.required' => 'Start year is required.',
                'start_year.size' => 'Start year must be 4 digits.',
                'start_year.regex' => 'Start year must be a valid 4-digit year.',
                'school_year.required' => 'School year is required.',
                'school_year.regex' => 'School year must be in the format YYYY-YYYY.',
            ]);

            $existingSession = LogSession::where('date', $validated['date'])
                ->where('school_year', $validated['school_year'])
                ->first();

            if ($existingSession) {
                $this->addError('school_year', 'A log session with this date and school year already exists.');

                return;
            }

            LogSession::create([
                'date' => $validated['date'],
                'school_year' => $validated['school_year'],
            ]);

            $this->date = '';
            $this->start_year = '';
            $this->end_year = '';
            $this->school_year = '';

            Flux::modals()->close();

            $this->dispatch('log-session-created');

            $this->dispatch('notify',
                type: 'success',
                content: 'Log session added successfully.',
                duration: 5000
            );

        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            Flux::modals()->close();

            if ($e->getCode() == 23000) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to add log session. A session with this date and school year may already exist.',
                    duration: 5000
                );
            } else {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Database error occurred. Please try again later.',
                    duration: 5000
                );
            }
        } catch (Exception $e) {
            Flux::modals()->close();

            Log::error('Error adding log session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again or contact support if the problem persists.',
                duration: 5000
            );
        }
    }

    public function resetCreateForms()
    {
        $this->date = '';
        $this->start_year = '';
        $this->end_year = '';
        $this->school_year = '';

        Flux::modals()->close();
    }

    // Computed property for formatted date display
    public function getFormattedDateProperty()
    {
        if (empty($this->date)) {
            return '';
        }

        try {
            return Carbon::parse($this->date)->format('l, F j, Y');
        } catch (\Exception $e) {
            return $this->date;
        }
    }

    // Get available years from database
    public function getAvailableYearsProperty()
    {
        return LogSession::select('date')
            ->distinct()
            ->get()
            ->map(function ($session) {
                return Carbon::parse($session->date)->format('Y');
            })
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();
    }

    // Get available academic years from database
    public function getAvailableAcademicYearsProperty()
    {
        return LogSession::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year')
            ->toArray();
    }

    // Computed property for selected log records with eager loading
    public function getSelectedLogRecordsProperty()
    {
        if (! $this->selectedLogSessionId) {
            return collect();
        }

        return LogRecord::with(['student', 'faculty'])
            ->where('log_session_id', $this->selectedLogSessionId)
            ->orderBy('time_in')
            ->get();
    }

    // View Logs
    public function viewLogs($logSessionId)
    {
        $this->viewingSessionId = $logSessionId;

        $this->selectedLogSession = null;
        $this->selectedLogSessionId = null;

        $this->selectedLogSession = LogSession::findOrFail($logSessionId);
        $this->selectedLogSessionId = $logSessionId;

        Flux::modal('view-logs')->show();

        $this->viewingSessionId = null;
    }

    // Remove Log Session
    public function removeLogSession($logSessionId)
    {
        try {
            $logSession = LogSession::find($logSessionId);

            if (! $logSession) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Log session not found. Please refresh the page and try again.',
                    duration: 5000
                );

                return;
            }

            $this->logSessionId = $logSession->id;
            $this->logSessionDate = Carbon::parse($logSession->date)->format('l, F j, Y');
            $this->logSessionSchoolYear = $logSession->school_year;

            Flux::modal('remove-log-session')->show();

        } catch (Exception $e) {
            Log::error('Error loading log session for delete', [
                'log_session_id' => $logSessionId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'Unable to load log session details. Please try again.',
                duration: 5000
            );
        }
    }

    // Delete Log Session
    public function deleteLogSessionInformation()
    {
        try {
            $logSession = LogSession::findOrFail($this->logSessionId);

            $logSession->delete();

            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            Flux::modals()->close();

            $this->dispatch('notify',
                type: 'success',
                content: 'Log session deleted successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            Flux::modals()->close();

            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            $this->dispatch('notify',
                type: 'error',
                content: 'Log session not found. It may have already been deleted.',
                duration: 5000
            );
        } catch (QueryException $e) {
            Flux::modals()->close();

            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            if ($e->getCode() == 23000) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to delete log session. The session may have associated log records that need to be removed first.',
                    duration: 5000
                );
            } else {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Database error occurred. Please try again later.',
                    duration: 5000
                );
            }
        } catch (Exception $e) {
            Flux::modals()->close();

            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            Log::error('Error deleting log session', [
                'log_session_id' => $this->logSessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again or contact support if the problem persists.',
                duration: 5000
            );
        }
    }

    public function placeholder()
    {
        return view('livewire.management.user-logs-table-placeholder');
    }

    public function render()
    {
        $query = LogSession::query();

        // Apply month filter
        if (! empty($this->filterMonth)) {
            $monthNumber = $this->getMonthNumber($this->filterMonth);
            if ($monthNumber) {
                $query->whereMonth('date', $monthNumber);
            }
        }

        // Apply year filter
        if (! empty($this->filterYear)) {
            $query->whereYear('date', $this->filterYear);
        }

        // Apply academic year filter
        if (! empty($this->filterAcademicYear)) {
            $query->where('school_year', $this->filterAcademicYear);
        }

        // Count unique users (students + faculty)
        $logSessions = $query->withCount([
                'logRecords as unique_users_count' => function ($query) {
                    $query->select(DB::raw(
                        'COUNT(DISTINCT CASE WHEN loggable_type = \'student\' THEN student_id END) + ' .
                        'COUNT(DISTINCT CASE WHEN loggable_type = \'faculty\' THEN faculty_id END)'
                    ));
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.management.user-logs-table', [
            'logSessions' => $logSessions,
            'availableYears' => $this->availableYears,
            'availableAcademicYears' => $this->availableAcademicYears,
            'hasLogSessions' => LogSession::exists()
        ]);
    }

    // Helper method to convert month name to number
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
}
