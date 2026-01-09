<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Illuminate\Support\Facades\DB;

use App\Models\LogSession;
use App\Models\LogRecord;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

#[Lazy]
class StudentLogsTable extends Component
{
    use WithPagination;

    public $date, $start_year, $end_year, $school_year;

    public ?LogSession $selectedLogSession = null;
    public $selectedLogRecords = [];

    // Filter properties
    public $filterMonth = '';
    public $filterYear = '';
    public $filterAcademicYear = '';

    protected $listeners = ['log-session-created' => '$refresh'];

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
        if (!empty($value) && is_numeric($value) && strlen($value) === 4) {
            $this->end_year = (string)((int)$value + 1);
            $this->school_year = $value . '-' . $this->end_year;
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
                'trace' => $e->getTraceAsString()
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

    // View Logs 
    public function viewLogs($logSessionId)
    {
        $this->selectedLogSession = LogSession::findOrFail($logSessionId);
        $this->selectedLogRecords = LogRecord::with('student')
            ->where('log_session_id', $logSessionId)
            ->orderBy('time_in')
            ->get();

        Flux::modal('view-logs')->show();
    }

    public function placeholder()
    {
        return view('livewire.management.student-logs-table-placeholder');
    }

    public function render()
    {
        $query = LogSession::query();

        // Apply month filter
        if (!empty($this->filterMonth)) {
            $monthNumber = $this->getMonthNumber($this->filterMonth);
            if ($monthNumber) {
                $query->whereMonth('date', $monthNumber);
            }
        }

        // Apply year filter
        if (!empty($this->filterYear)) {
            $query->whereYear('date', $this->filterYear);
        }

        // Apply academic year filter
        if (!empty($this->filterAcademicYear)) {
            $query->where('school_year', $this->filterAcademicYear);
        }

        $logSessions = $query->latest()->paginate(10);

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
