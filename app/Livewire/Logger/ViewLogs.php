<?php

namespace App\Livewire\Logger;

use Livewire\Component;

use App\Models\LogSession;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.view-logs-app')]
class ViewLogs extends Component
{

    public $logSession;

    public function mount(LogSession $logSession)
    {
        $this->logSession = $logSession;
    }

    public function render()
    {
        return view('livewire.logger.view-logs');
    }
}
