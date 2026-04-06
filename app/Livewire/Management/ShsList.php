<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

use App\Models\Student;
use App\Models\Strand;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class ShsList extends Component
{

    // User Details for Add Student
    public $last_name, $first_name, $year_level, $strand;

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
            
            $validPaperSizes = ['A4', 'Letter', 'Legal'];
            if (!in_array($paperSize, $validPaperSizes)) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Invalid paper size selected. Please try again.',
                    duration: 5000
                );
                return;
            }

            $studentCount = Student::shs()->count();
            if ($studentCount === 0) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No SHS students found to export. Please add students first.',
                    duration: 5000
                );
                return;
            }

            $this->dispatch('notify',
                type: 'info',
                content: 'Generating barcode PDF...',
                duration: 5000
            );

            return redirect()->route('export_barcode', ['paper_size' => $paperSize, 'user_type' => 'shs']);

        } catch (Exception $e) {
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
        $this->last_name = '';
        $this->first_name = '';
        $this->year_level = '';
        $this->strand = '';

        $this->resetErrorBag();
        
        Flux::modal('create-students')->show();
    }

    public function addStudentInformation()
    {
        try {
            $validated = $this->validate([
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'first_name' => ['required', 'string','max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'year_level' => ['required', 'string'],
                'strand' => ['required', 'string'],
            ]);

            $formatName = static function (string $value): string {
                return (string) Str::of($value)
                    ->trim()
                    ->replaceMatches('/,{2,}/', ',')
                    ->replaceMatches('/\.{2,}/', '.')
                    ->replaceMatches('/,\s*/', ', ')
                    ->lower()
                    ->title();
            };

            foreach (['last_name', 'first_name'] as $field) {
                if (isset($validated[$field]) && is_string($validated[$field])) {
                    $validated[$field] = $formatName($validated[$field]);
                }
            }

            // Set SHS type, no course
            $validated['user_type'] = 'shs';
            $validated['course'] = null;

            Student::create($validated);

            $this->last_name = '';
            $this->first_name = '';
            $this->year_level = '';
            $this->strand = '';

            $this->dispatch('student-added');

            Flux::modals()->close();

            $this->dispatch('notify',
                type: 'success',
                content: 'SHS student added successfully.',
                duration: 5000
            );

        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            Flux::modals()->close();
            
            if ($e->getCode() == 23000) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to add student. A duplicate record may exist.',
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
            Flux::modals()->close();
            
            Log::error('Error adding SHS student', [
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
        $this->last_name = '';
        $this->first_name = '';
        $this->year_level = '';
        $this->strand = '';

        Flux::modals()->close();
    }

    public function render()
    {
        $students = Student::shs()->get();
        $strands = Strand::orderBy('name')->get();

        return view('livewire.management.shs-list', [
            'students'   => $students,
            'strands'    => $strands,
            'totalCount' => $students->count(),
        ]);
    }
}
