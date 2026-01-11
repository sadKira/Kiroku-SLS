<?php

namespace App\Livewire\Logger;

use Livewire\Component;
use Livewire\Attributes\Layout;
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

#[Layout('components.layouts.logger-app')]
class LoggerDashboard extends Component
{
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

        return view('livewire.logger.logger-dashboard', [
            // 'logSessions' => $logSessions,
            // 'availableYears' => $this->availableYears,
            // 'availableAcademicYears' => $this->availableAcademicYears,
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
