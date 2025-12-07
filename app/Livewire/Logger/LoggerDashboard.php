<?php

namespace App\Livewire\Logger;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.logger-app')]
class LoggerDashboard extends Component
{
    public function render()
    {
        return view('livewire.logger.logger-dashboard');
    }
}
