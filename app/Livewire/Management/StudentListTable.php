<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\WithPagination;

use App\Models\Student;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Lazy]
class StudentListTable extends Component
{
    use WithPagination;

    public $search = '';

    // Filtering
    public $selectedCourse = 'All';
    public $selectedYearLevel = 'All';

    // Multi Select
    public $selected = [];

    // User Details for Edit/Delete
    public $student, $studentId, $last_name, $first_name, $id_student, $year_level, $course;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->validate([
            'search' => ['nullable', 'string', 'max:255', 'regex:/^[A-Za-z0-9 ,.\-]+$/'],
        ]);
    }

    public function updatedSelectedCourse()
    {
        $this->resetPage();
    }

    public function updatedSelectedYearLevel()
    {
        $this->resetPage();
    }

    // Clear Student Filters
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

    // Clear Selected
    public function clearSelected()
    {
        $this->selected = [];
    }

     // Listen for the student-added event
    #[On('student-added')]
    public function refreshTable()
    {
        // Reset to first page to show the newly added student
        $this->resetPage();
        
        // Clear any active search/filters 
        $this->search = '';
        $this->selectedCourse = 'All';
        $this->selectedYearLevel = 'All';
    }

    // Edit Student Details
    public function editProfile($studentId)
    {
        try {
            $student = Student::find($studentId);

            if (!$student) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Student not found. Please refresh the page and try again.',
                    duration: 5000
                );
                return;
            }

            $this->studentId = $student->id;
            $this->last_name = $student->last_name;
            $this->first_name = $student->first_name;
            $this->id_student = $student->id_student;
            $this->year_level = $student->year_level;
            $this->course = $student->course;

            Flux::modal('update-student')->show();

        } catch (Exception $e) {
            Log::error('Error loading student for edit', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'Unable to load student details. Please try again.',
                duration: 5000
            );
        }
    }

    // Details Update Validation
    public function updateProfileInformation(): void
    {
        try {
            // Validate input
            $validated = $this->validate([
                'last_name' => ['string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'first_name' => ['string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'id_student' => ['string', 'min:7', Rule::unique('students', 'id_student')->ignore($this->studentId)],
                'year_level' => ['string'],
                'course' => ['string'],
            ]);

            // Check if student exists
            $student = Student::findOrFail($this->studentId);

            // Validation
            $formatName = static function (string $value): string {
                return (string) Str::of($value)
                    ->trim()
                    ->replaceMatches('/,{2,}/', ',') // Remove Consecutive Commas and Periods
                    ->replaceMatches('/\.{2,}/', '.')
                    ->replaceMatches('/,\s*/', ', ') // Normalize Comma Spacing
                    ->lower()
                    ->title();
            };

            foreach (['last_name', 'first_name'] as $field) {
                if (isset($validated[$field]) && is_string($validated[$field])) {
                    $validated[$field] = $formatName($validated[$field]);
                }
            }

            // Update student
            $student->update($validated);

            // Close Modal
            Flux::modals()->close();
            
            // Success Toast
            $this->dispatch('notify',
                type: 'success',
                content: 'Student details updated successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            // Handle model not found
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Student not found. Please refresh the page and try again.',
                duration: 5000
            );
        } catch (ValidationException $e) {
            // Validation errors are automatically handled by Livewire
            throw $e;
        } catch (QueryException $e) {
            // Handle database errors
            Flux::modals()->close();
            
            // Check for specific database errors
            if ($e->getCode() == 23000) { // Integrity constraint violation
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to update student. The student ID may already be in use.',
                    duration: 5000
                );
            } else {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Database error occurred. Please try again later.',
                    duration: 5000
                );
            }
        } catch (Exception $e) {
            // Handle any other unexpected errors
            Flux::modals()->close();
            
            // Log the error for debugging
            Log::error('Error updating student profile', [
                'student_id' => $this->studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again or contact support if the problem persists.',
                duration: 5000
            );
        }
    }

    // Remove Student
    public function removeProfile($studentId)
    {
        $student = Student::find($studentId);
        
        $this->studentId = $student->id;
        $this->last_name = $student->last_name;
        $this->first_name = $student->first_name;

        Flux::modal('remove-student')->show();
    }

    public function deleteProfileInformation()
    {
        try {
            // Check if student exists
            $student = Student::findOrFail($this->studentId);

            // Delete student
            $student->delete();

            // Close Modal
            Flux::modals()->close();

            // Dispatch event to refresh parent component
            $this->dispatch('student-deleted');

            // Success Toast
            $this->dispatch('notify',
                type: 'success',
                content: 'Student deleted successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            // Handle model not found
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Student not found. It may have already been deleted.',
                duration: 5000
            );
        } catch (QueryException $e) {
            // Handle database errors
            Flux::modals()->close();
            
            // Check for foreign key constraint violations
            if ($e->getCode() == 23000) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to delete student. The student may have associated records.',
                    duration: 5000
                );
            } else {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Database error occurred. Please try again later.',
                    duration: 5000
                );
            }
        } catch (Exception $e) {
            // Handle any other unexpected errors
            Flux::modals()->close();
            
            // Log the error for debugging
            Log::error('Error deleting student', [
                'student_id' => $this->studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again or contact support if the problem persists.',
                duration: 5000
            );
        }
    }

    // Bulk Delete
    public function bulkRemoveProfile()
    {
        if (empty($this->selected)) {
            return;
        }

        Flux::modal('bulkremove-student')->show();
    }

    public function bulkDeleteProfileInformation()
    {
        try {
            // Validate that selected is not empty and is an array
            if (empty($this->selected) || !is_array($this->selected)) {
                Flux::modals()->close();
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No students selected for deletion.',
                    duration: 5000
                );
                return;
            }

            // Validate and sanitize IDs - ensure they are integers
            $validatedIds = array_filter(
                array_map('intval', $this->selected),
                fn($id) => $id > 0
            );

            if (empty($validatedIds)) {
                Flux::modals()->close();
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Invalid student selection. Please try again.',
                    duration: 5000
                );
                return;
            }

            // Check if any students exist
            $studentsCount = Student::whereIn('id', $validatedIds)->count();
            if ($studentsCount === 0) {
                Flux::modals()->close();
                $this->clearSelected();
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Selected students not found. They may have already been deleted.',
                    duration: 5000
                );
                return;
            }

            // Delete Students
            $deletedCount = Student::whereIn('id', $validatedIds)->delete();

            $this->clearSelected();

            // Close Modal
            Flux::modals()->close();

            // Dispatch event to refresh parent component
            $this->dispatch('student-deleted');

            // Success Toast
            $this->dispatch('notify',
                type: 'success',
                content: $deletedCount . ' student(s) deleted successfully.',
                duration: 5000
            );

        } catch (QueryException $e) {
            // Handle database errors
            Flux::modals()->close();
            
            // Check for foreign key constraint violations
            if ($e->getCode() == 23000) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to delete some students. They may have associated records.',
                    duration: 5000
                );
            } else {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Database error occurred. Please try again later.',
                    duration: 5000
                );
            }
        } catch (Exception $e) {
            // Handle any other unexpected errors
            Flux::modals()->close();
            
            // Log the error for debugging
            Log::error('Error bulk deleting students', [
                'selected_ids' => $this->selected,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again or contact support if the problem persists.',
                duration: 5000
            );
        }
    }

    public function placeholder()
    {
        return view('livewire.management.student-list-table-placeholder');
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
            ->orderBy('last_name');

        return view('livewire.management.student-list-table', [
            'students' => $query->paginate(10),
            'hasStudents' => Student::exists()
        ]);
    }
}
