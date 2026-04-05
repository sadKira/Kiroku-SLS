<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use App\Models\Course;

#[Lazy]
class CourseListTable extends Component
{
    public string $search = '';

    public function placeholder()
    {
        return view('livewire.management.course-list-table-placeholder');
    }

    #[On('course-list-updated')]
    public function render()
    {
        $courses = Course::orderBy('name')
            ->when($this->search !== '', function ($query) {
                $query->where('code', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
            })
            ->get();

        return view('livewire.management.course-list-table', [
            'courses' => $courses,
        ]);
    }
}
