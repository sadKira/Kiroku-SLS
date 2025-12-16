<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

use App\Models\Student;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

#[Layout('components.layouts.app')]
class StudentList extends Component
{

    use WithPagination;

    // User Details
    public $student, $studentId, $full_name, $id_student, $year_level, $course;

    public $search = '';

    // Filtering
    public $selectedCourse = 'All';
    public $selectedYearLevel = 'All';

    // Multi Select
    public $selected = [];
    // public $selectAll = false;
    // public $selectPage = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCourse()
    {
        $this->resetPage();
    }

    public function updatedSelectedYearLevel()
    {
        $this->resetPage();
    }

    public function clearSelection()
    {
        $this->selectedCourse = 'All';
        $this->selectedYearLevel = 'All';
        $this->search = '';
        $this->resetPage();
    }

    public function clearYearLevel()
    {
        $this->selectedYearLevel = 'All';
        $this->search = '';
    }

    public function clearCourse()
    {
        $this->selectedCourse = 'All';
        $this->search = '';
    }

    // Edit Student Details
    public function editProfile($studentId)
    {
        $student = Student::find($studentId);

        $this->studentId = $student->id;
        $this->full_name = $student->full_name;
        $this->id_student = $student->id_student;
        $this->year_level = $student->year_level;
        $this->course = $student->course;

        Flux::modal('update-student')->show();
    }

    // Details Update Validation
    public function updateProfileInformation(): void
    {
    
        $validated = $this->validate([

            'full_name' => ['string', 'min:5','max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
            'id_student' => ['string', 'min:7', Rule::unique('students', 'id_student')->ignore($this->studentId)],
            'year_level' => ['string'],
            'course' => ['string'],

        ]);

        $validated['full_name'] = Str::title(
            Str::lower(
                preg_replace(
                    '/,\s*/', ', ', // Normalize Comma Spacing
                    preg_replace(
                        ['/,{2,}/', '/\.{2,}/'], // Remove Consecutive Commas and Periods
                        [',', '.'],
                        trim($validated['full_name'])
                    )
                )
            )
        );

        Student::where('id', $this->studentId)->update($validated);

        // Close Modal
        Flux::modals()->close();   

    }

    // Remove Student
    public function removeProfile($studentId)
    {
        $student = Student::find($studentId);
        
        $this->full_name = $student->full_name;

        Flux::modal('remove-student')->show();
    }

    public function deleteProfileInformation()
    {
        Student::where('id', $this->studentId)->delete();

        // Close Modal
        Flux::modals()->close();
    }

    public function render()
    {
        $query = Student::query()
            ->when($this->selectedYearLevel !== 'All' && $this->selectedYearLevel !== null, function ($q) {
                $q->where('year_level', $this->selectedYearLevel);
            })
            ->when($this->selectedCourse !== 'All' && $this->selectedCourse !== null, function ($q) {
                $q->where('course', $this->selectedCourse);
            })
            ->search($this->search)
            ->orderBy('full_name');

        return view('livewire.management.student-list', [
            'students' => $query->paginate(10)
        ]);
    }
}
