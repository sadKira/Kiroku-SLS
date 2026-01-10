<?php

namespace App\Livewire\Management;

use App\Models\LogRecord;
use App\Models\LogSession;
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

#[Lazy]
class StudentLogsTable extends Component
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

    // Export Student Logs
    public function exportStudentLogs($logSessionId, $paperSize = 'A4')
    {
        try {
            $this->exportingSessionId = $logSessionId;
            $this->paperSize = $paperSize;

            // Validate paper size
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

            // Check if log session exists
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

            // Check if there are any log records to export
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

            // Show success toast before redirecting
            $this->dispatch('notify',
                type: 'info',
                content: 'Generating student logs PDF...',
                duration: 5000
            );

            // Reset exporting state before redirect
            $this->exportingSessionId = null;

            // Redirect to export route with log session ID and paper size
            return redirect()->route('export_student_logs', [
                'log_session_id' => $logSessionId,
                'paper_size' => $paperSize,
            ]);

        } catch (Exception $e) {
            // Handle any unexpected errors
            $this->exportingSessionId = null;
            Log::error('Error initiating student logs export', [
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
        // Set today's date automatically
        $this->date = Carbon::now()->format('Y-m-d');

        // Reset form fields
        $this->start_year = '';
        $this->end_year = '';
        $this->school_year = '';

        // Clear Error
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
            // Validate input
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

            // Check if a log session with the same date and school year already exists
            $existingSession = LogSession::where('date', $validated['date'])
                ->where('school_year', $validated['school_year'])
                ->first();

            if ($existingSession) {
                $this->addError('school_year', 'A log session with this date and school year already exists.');

                return;
            }

            // Create Log Session
            LogSession::create([
                'date' => $validated['date'],
                'school_year' => $validated['school_year'],
            ]);

            // Reset form fields
            $this->date = '';
            $this->start_year = '';
            $this->end_year = '';
            $this->school_year = '';

            // Close Modal
            Flux::modals()->close();

            // Dispatch event to refresh the table component
            $this->dispatch('log-session-created');

            // Success Toast
            $this->dispatch('notify',
                type: 'success',
                content: 'Log session added successfully.',
                duration: 5000
            );

        } catch (ValidationException $e) {
            // Validation errors are automatically handled by Livewire
            throw $e;
        } catch (QueryException $e) {
            // Handle database errors
            Flux::modals()->close();

            // Check for specific database errors
            if ($e->getCode() == 23000) { // Integrity constraint violation
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
            // Handle any other unexpected errors
            Flux::modals()->close();

            // Log the error for debugging
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
        // Reset form fields
        $this->date = '';
        $this->start_year = '';
        $this->end_year = '';
        $this->school_year = '';

        // Close Modal
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

        return LogRecord::with('student')
            ->where('log_session_id', $this->selectedLogSessionId)
            ->orderBy('time_in')
            ->get();
    }

    // View Logs
    public function viewLogs($logSessionId)
    {
        $this->viewingSessionId = $logSessionId;

        // Reset previous selection
        $this->selectedLogSession = null;
        $this->selectedLogSessionId = null;

        $this->selectedLogSession = LogSession::findOrFail($logSessionId);
        $this->selectedLogSessionId = $logSessionId;

        Flux::modal('view-logs')->show();

        // Reset viewing state after modal opens
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
            // Check if log session exists
            $logSession = LogSession::findOrFail($this->logSessionId);

            // Delete log session (this will cascade delete log records if foreign key constraints are set up)
            $logSession->delete();

            // Reset properties
            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            // Close Modal
            Flux::modals()->close();

            // Success Toast
            $this->dispatch('notify',
                type: 'success',
                content: 'Log session deleted successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            // Handle model not found
            Flux::modals()->close();

            // Reset properties
            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            $this->dispatch('notify',
                type: 'error',
                content: 'Log session not found. It may have already been deleted.',
                duration: 5000
            );
        } catch (QueryException $e) {
            // Handle database errors
            Flux::modals()->close();

            // Reset properties
            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            // Check for foreign key constraint violations
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
            // Handle any other unexpected errors
            Flux::modals()->close();

            // Reset properties
            $this->logSessionId = null;
            $this->logSessionDate = null;
            $this->logSessionSchoolYear = null;

            // Log the error for debugging
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
        return view('livewire.management.student-logs-table-placeholder');
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

        // Eager load log records count to check if sessions have records
        $logSessions = $query->withCount('logRecords')->latest()->paginate(10);

        return view('livewire.management.student-logs-table', [
            'logSessions' => $logSessions,
            'availableYears' => $this->availableYears,
            'availableAcademicYears' => $this->availableAcademicYears,
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
