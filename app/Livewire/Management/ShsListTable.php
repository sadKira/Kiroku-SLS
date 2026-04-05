<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\WithPagination;

use App\Models\Student;
use App\Models\Strand;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Lazy]
class ShsListTable extends Component
{
    use WithPagination;

    public $search = '';

    // Filtering
    public $selectedStrand = 'All';
    public $selectedYearLevel = 'All';

    // Multi Select
    public $selected = [];

    // User Details for Edit/Delete
    public $student, $studentId, $last_name, $first_name, $id_student, $year_level, $strand;

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

    public function updatedSelectedStrand()
    {
        $this->resetPage();
    }

    public function updatedSelectedYearLevel()
    {
        $this->resetPage();
    }

    // Clear Filters
    public function clearYearLevel()
    {
        $this->selectedYearLevel = 'All';
        $this->search = '';
    }

    public function clearStrand()
    {
        $this->selectedStrand = 'All';
        $this->search = '';
    }

    // Clear Selected
    public function clearSelected()
    {
        $this->selected = [];
    }

    #[On('student-added')]
    public function refreshTable()
    {
        $this->resetPage();
        $this->search = '';
        $this->selectedStrand = 'All';
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
            $this->strand = $student->strand;

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

    public function updateProfileInformation(): void
    {
        try {
            $validated = $this->validate([
                'last_name' => ['string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'first_name' => ['string', 'max:255', 'regex:/^[A-Za-z ,.\-]+$/'],
                'year_level' => ['string'],
                'strand' => ['string'],
            ]);

            $student = Student::findOrFail($this->studentId);

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

            $student->update($validated);

            Flux::modals()->close();
            
            $this->dispatch('notify',
                type: 'success',
                content: 'Student details updated successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Student not found. Please refresh the page and try again.',
                duration: 5000
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Database error occurred. Please try again later.',
                duration: 5000
            );
        } catch (Exception $e) {
            Flux::modals()->close();
            
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
            $student = Student::findOrFail($this->studentId);
            $student->delete();

            Flux::modals()->close();
            $this->dispatch('student-deleted');

            $this->dispatch('notify',
                type: 'success',
                content: 'Student deleted successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Student not found. It may have already been deleted.',
                duration: 5000
            );
        } catch (Exception $e) {
            Flux::modals()->close();
            
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
            if (empty($this->selected) || !is_array($this->selected)) {
                Flux::modals()->close();
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No students selected for deletion.',
                    duration: 5000
                );
                return;
            }

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

            $deletedCount = Student::whereIn('id', $validatedIds)->delete();

            $this->clearSelected();
            Flux::modals()->close();
            $this->dispatch('student-deleted');

            $this->dispatch('notify',
                type: 'success',
                content: $deletedCount . ' student(s) deleted successfully.',
                duration: 5000
            );

        } catch (Exception $e) {
            Flux::modals()->close();
            
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
        return view('livewire.management.shs-list-table-placeholder');
    }

    public function render()
    {
        $query = Student::shs()
            ->when($this->selectedYearLevel !== 'All' && $this->selectedYearLevel !== null, function ($q) {
                $q->where('year_level', $this->selectedYearLevel);
            })
            ->when($this->selectedStrand !== 'All' && $this->selectedStrand !== null, function ($q) {
                $q->where('strand', $this->selectedStrand);
            })
            ->search($this->search)
            ->orderBy('last_name');

        $strands = Strand::orderBy('name')->get();

        return view('livewire.management.shs-list-table', [
            'students' => $query->paginate(10),
            'hasStudents' => Student::shs()->exists(),
            'strands' => $strands,
        ]);
    }
}
