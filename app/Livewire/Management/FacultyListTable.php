<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\WithPagination;

use App\Models\Faculty;
use App\Models\InstructionalLevel;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Lazy]
class FacultyListTable extends Component
{
    use WithPagination;

    public $search = '';

    // Filtering
    public $selectedInstructionalLevel = 'All';

    // Multi Select
    public $selected = [];

    // Faculty Details for Edit/Delete
    public $faculty, $facultyId, $last_name, $first_name, $id_faculty, $instructional_level;

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

    public function updatedSelectedInstructionalLevel()
    {
        $this->resetPage();
    }

    // Clear Filters
    public function clearInstructionalLevel()
    {
        $this->selectedInstructionalLevel = 'All';
        $this->search = '';
    }

    // Clear Selected
    public function clearSelected()
    {
        $this->selected = [];
    }

    #[On('faculty-added')]
    public function refreshTable()
    {
        $this->resetPage();
        $this->search = '';
        $this->selectedInstructionalLevel = 'All';
    }

    // Edit Faculty Details
    public function editProfile($facultyId)
    {
        try {
            $faculty = Faculty::find($facultyId);

            if (!$faculty) {
                $this->dispatch('notify',
                    type: 'error',
                    content: 'Faculty not found. Please refresh the page and try again.',
                    duration: 5000
                );
                return;
            }

            $this->facultyId = $faculty->id;
            $this->last_name = $faculty->last_name;
            $this->first_name = $faculty->first_name;
            $this->id_faculty = $faculty->id_faculty;
            $this->instructional_level = $faculty->instructional_level;

            Flux::modal('update-faculty')->show();

        } catch (Exception $e) {
            Log::error('Error loading faculty for edit', [
                'faculty_id' => $facultyId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'Unable to load faculty details. Please try again.',
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
                'instructional_level' => ['string'],
            ]);

            $faculty = Faculty::findOrFail($this->facultyId);

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

            $faculty->update($validated);

            Flux::modals()->close();
            
            $this->dispatch('notify',
                type: 'success',
                content: 'Faculty details updated successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Faculty not found. Please refresh the page and try again.',
                duration: 5000
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Flux::modals()->close();
            
            Log::error('Error updating faculty profile', [
                'faculty_id' => $this->facultyId,
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

    // Remove Faculty
    public function removeProfile($facultyId)
    {
        $faculty = Faculty::find($facultyId);
        
        $this->facultyId = $faculty->id;
        $this->last_name = $faculty->last_name;
        $this->first_name = $faculty->first_name;

        Flux::modal('remove-faculty')->show();
    }

    public function deleteProfileInformation()
    {
        try {
            $faculty = Faculty::findOrFail($this->facultyId);
            $faculty->delete();

            Flux::modals()->close();
            $this->dispatch('faculty-deleted');

            $this->dispatch('notify',
                type: 'success',
                content: 'Faculty deleted successfully.',
                duration: 5000
            );

        } catch (ModelNotFoundException $e) {
            Flux::modals()->close();
            $this->dispatch('notify',
                type: 'error',
                content: 'Faculty not found. It may have already been deleted.',
                duration: 5000
            );
        } catch (Exception $e) {
            Flux::modals()->close();
            
            Log::error('Error deleting faculty', [
                'faculty_id' => $this->facultyId,
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

        Flux::modal('bulkremove-faculty')->show();
    }

    public function bulkDeleteProfileInformation()
    {
        try {
            if (empty($this->selected) || !is_array($this->selected)) {
                Flux::modals()->close();
                $this->dispatch('notify',
                    type: 'error',
                    content: 'No faculty selected for deletion.',
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
                    content: 'Invalid faculty selection. Please try again.',
                    duration: 5000
                );
                return;
            }

            $deletedCount = Faculty::whereIn('id', $validatedIds)->delete();

            $this->clearSelected();
            Flux::modals()->close();
            $this->dispatch('faculty-deleted');

            $this->dispatch('notify',
                type: 'success',
                content: $deletedCount . ' faculty member(s) deleted successfully.',
                duration: 5000
            );

        } catch (Exception $e) {
            Flux::modals()->close();
            
            Log::error('Error bulk deleting faculty', [
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
        return view('livewire.management.faculty-list-table-placeholder');
    }

    public function render()
    {
        $query = Faculty::query()
            ->when($this->selectedInstructionalLevel !== 'All' && $this->selectedInstructionalLevel !== null, function ($q) {
                $q->where('instructional_level', $this->selectedInstructionalLevel);
            })
            ->search($this->search)
            ->orderBy('last_name');

        $instructionalLevels = InstructionalLevel::orderBy('name')->get();

        return view('livewire.management.faculty-list-table', [
            'faculties' => $query->paginate(10),
            'hasFaculties' => Faculty::exists(),
            'instructionalLevels' => $instructionalLevels,
        ]);
    }
}
