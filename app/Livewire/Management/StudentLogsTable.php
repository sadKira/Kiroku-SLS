<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;

use App\Models\LogSession;
use App\Models\LogRecord;
use Flux\Flux;

#[Lazy]
class StudentLogsTable extends Component
{
    use WithPagination;

    public ?LogSession $selectedLogSession = null;
    public $selectedLogRecords = [];

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
        $query = LogSession::query()
            ->latest();

        return view('livewire.management.student-logs-table', [
            'logSessions' => $query->paginate(10)
        ]);
    }
}
