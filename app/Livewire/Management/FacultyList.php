<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

use App\Models\Faculty;
use App\Models\InstructionalLevel;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class FacultyList extends Component
{

    // Faculty Details for Add
    public $last_name, $first_name, $instructional_level;

    // Paper Size for Export
    public $paperSize = 'A4';

    #[On('faculty-deleted')]
    public function refreshFacultyList()
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

            $facultyCount = Faculty::count();
            if ($facultyCount === 0) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No faculty found to export. Please add faculty first.',
                    duration: 5000
                );
                return;
            }

            $this->dispatch('notify',
                type: 'info',
                content: 'Generating barcode PDF...',
                duration: 5000
            );

            return redirect()->route('export_barcode', ['paper_size' => $paperSize, 'user_type' => 'faculty']);

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

    // Add Faculty
    public function addFaculty()
    {
        $this->last_name = '';
        $this->first_name = '';
        $this->instructional_level = '';

        $this->resetErrorBag();
        
        Flux::modal('create-faculty')->show();
    }

    public function addFacultyInformation()
    {
        try {
            $validated = $this->validate([
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'first_name' => ['required', 'string','max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'instructional_level' => ['required', 'string'],
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

            // Create Faculty (id_faculty auto-generated)
            Faculty::create($validated);

            $this->last_name = '';
            $this->first_name = '';
            $this->instructional_level = '';

            $this->dispatch('faculty-added');

            Flux::modals()->close();

            $this->dispatch('notify',
                type: 'success',
                content: 'Faculty added successfully.',
                duration: 5000
            );

        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            Flux::modals()->close();
            
            if ($e->getCode() == 23000) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Unable to add faculty. A duplicate record may exist.',
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
            
            Log::error('Error adding faculty', [
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
        $this->instructional_level = '';

        Flux::modals()->close();
    }

    public function render()
    {
        $faculties = Faculty::all();
        $instructionalLevels = InstructionalLevel::orderBy('name')->get();

        return view('livewire.management.faculty-list', [
            'faculties' => $faculties,
            'instructionalLevels' => $instructionalLevels,
        ]);
    }
}
