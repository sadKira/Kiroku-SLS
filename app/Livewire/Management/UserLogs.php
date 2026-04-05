<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserLogs extends Component
{
    public function render()
    {
        return view('livewire.management.user-logs');
    }
}
