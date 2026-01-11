<?php

namespace App\Livewire\Logger;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;

use App\Models\LogRecord;
use App\Models\LogSession;
use Carbon\Carbon;
use Exception;
use Flux\Flux;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

#[Lazy]
class LoggerTable extends Component
{
    use WithPagination;

    // Filter properties
    public $filterMonth = '';
    public $filterYear = '';
    public $filterAcademicYear = '';
    
    // Search property
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
        // Clear all filters when search is being updated
        $this->filterMonth = '';
        $this->filterYear = '';
        $this->filterAcademicYear = '';
    }

    public function updatedSearch()
    {
        $this->validate([
            'search' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s\-]+$/'],
        ]);
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
        // Clear search when filter is being updated
        $this->search = '';
    }

    public function updatingFilterYear()
    {
        $this->resetPage();
        // Clear search when filter is being updated
        $this->search = '';
    }

    public function updatingFilterAcademicYear()
    {
        $this->resetPage();
        // Clear search when filter is being updated
        $this->search = '';
    }

    // Clear filter methods
    public function clearFilterMonth()
    {
        $this->filterMonth = '';
    }

    public function clearFilterYear()
    {
        $this->filterYear = '';
    }

    public function clearFilterAcademicYear()
    {
        $this->filterAcademicYear = '';
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

    public function placeholder()
    {
        return view('livewire.logger.logger-table-placeholder');
    }

    public function render()
    {
        $query = LogSession::query();

        // Apply search filter using model scope
        if (!empty($this->search)) {
            $query->search($this->search);
        }

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

        // Eager load log records and students count
        // Sort by most recent: date DESC, then school_year DESC
        $logSessions = $query->withCount(['logRecords', 'students'])
            ->orderBy('date', 'desc')
            ->orderBy('school_year', 'desc')
            ->paginate(12);

        return view('livewire.logger.logger-table', [
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
