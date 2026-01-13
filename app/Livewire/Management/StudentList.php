<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

use App\Models\Student;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class StudentList extends Component
{

    // User Details for Add Student
    public $last_name, $first_name, $id_student, $year_level, $course;

    // Paper Size for Export
    public $paperSize = 'A4';

    #[On('student-deleted')]
    public function refreshStudentList()
    {
        // This will trigger a re-render of the component
    }

    // Set Paper Size and Export
    public function exportBarcodes($paperSize)
    {
        try {
            $this->paperSize = $paperSize;
            
            // Validate paper size
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (!in_array($paperSize, $validPaperSizes)) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Invalid paper size selected. Please try again.',
                    duration: 5000
                );
                return;
            }

            // Check if there are any students to export
            $studentCount = Student::count();
            if ($studentCount === 0) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No students found to export. Please add students first.',
                    duration: 5000
                );
                return;
            }

            // Show success toast before redirecting
            $this->dispatch('notify',
                type: 'info',
                content: 'Generating barcode PDF...',
                duration: 5000
            );

            // Redirect to export route with paper size
            return redirect()->route('export_barcode', ['paper_size' => $paperSize]);

        } catch (Exception $e) {
            // Handle any unexpected errors
            Log::error('Error initiating barcode export', [
                'paper_size' => $paperSize ?? 'unknown',
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

    // Add Student
    public function addStudent()
    {
        // Reset form fields
        $this->last_name = '';
        $this->first_name = '';
        $this->id_student = '';
        $this->year_level = '';
        $this->course = '';

        // Clear Error
        $this->resetErrorBag();
        
        Flux::modal('create-students')->show();
    }

    public function addStudentInformation()
    {
        try {
            // Validate input
            $validated = $this->validate([
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'first_name' => ['required', 'string','max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'id_student' => ['required', 'string', 'min:7', Rule::unique('students', 'id_student')],
                'year_level' => ['required', 'string'],
                'course' => ['required', 'string'],
            ]);

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

            // Create Student
            Student::create($validated);

            // Reset form fields
            $this->last_name = '';
            $this->first_name = '';
            $this->id_student = '';
            $this->year_level = '';
            $this->course = '';

            // Dispatch event to refresh the table
            $this->dispatch('student-added');

            // Close Modal
            Flux::modals()->close();

            // Success Toast
            $this->dispatch('notify',
                type: 'success',
                content: 'Student added successfully.',
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
                    content: 'Unable to add student. The student ID or name may already exist.',
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
            Log::error('Error adding student', [
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

    public function resetCreateForms(){
        // Reset form fields
        $this->last_name = '';
        $this->first_name = '';
        $this->id_student = '';
        $this->year_level = '';
        $this->course = '';

        // Close Modal
        Flux::modals()->close();
    }

    public function render()
    {
        $students = Student::all();

        return view('livewire.management.student-list', [
            'students' => $students
        ]);
    }
}
