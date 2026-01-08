<?php

namespace App\Livewire\Management;

use Livewire\Component;
use App\Models\LogSession;

class StudentLogs extends Component
{
    public function render()
    {
        // $logs = LogSession::all()
        return view('livewire.management.student-logs');
    }
}
