<?php

namespace App\Livewire\Logger;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use App\Models\LogSession;

#[Lazy]
class ViewLogsTable extends Component
{
    public LogSession $logSession;

    protected $listeners = ['refresh-logs-table' => '$refresh'];

    public function mount(LogSession $logSession)
    {
        $this->logSession = $logSession;
    }

    public function placeholder()
    {
        return view('livewire.logger.view-logs-table-placeholder');
    }


    public function render()
    {
        // Reload the relationship to get fresh data
        $this->logSession->load('logRecords.student');
        
        return view('livewire.logger.view-logs-table', [
            'logSession' => $this->logSession,
        ]);
    }
}
