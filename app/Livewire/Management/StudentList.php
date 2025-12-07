<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class StudentList extends Component
{
    public function render()
    {
        return view('livewire.management.student-list');
    }
}
