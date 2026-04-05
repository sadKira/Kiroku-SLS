<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

use Illuminate\Support\Str;
use App\Models\InstructionalLevel;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class LevelManagement extends Component
{
    // Form fields
    public string $code = '';
    public string $name = '';

    // Delete confirmation
    public ?int $deleteLevelId = null;

    /**
     * Open the create modal
     */
    public function addLevel(): void
    {
        $this->code = '';
        $this->name = '';
        $this->resetErrorBag();

        Flux::modal('create-level')->show();
    }

    /**
     * Store a new instructional level
     */
    public function storeLevel(): void
    {
        try {
            $validated = $this->validate([
                'code' => ['required', 'string', 'max:20', Rule::unique('instructional_levels', 'code')],
                'name' => ['required', 'string', 'max:255', Rule::unique('instructional_levels', 'name')],
            ], [
                'code.required' => 'Level code is required.',
                'code.unique' => 'This level code already exists.',
                'name.required' => 'Level name is required.',
                'name.unique' => 'This level name already exists.',
            ]);

            // Formatting
            $validated['code'] = Str::upper($validated['code']);
            $validated['name'] = (string) Str::of($validated['name'])
                ->trim()
                ->replaceMatches('/,{2,}/', ',')
                ->replaceMatches('/\.{2,}/', '.')
                ->replaceMatches('/,\s*/', ', ')
                ->lower()
                ->title();

            InstructionalLevel::create($validated);

            $this->reset(['code', 'name']);
            Flux::modals()->close();

            $this->dispatch('level-list-updated');

            $this->dispatch('notify',
                type: 'success',
                content: 'Instructional level added successfully.',
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
            Log::error('Error adding instructional level', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'An unexpected error occurred. Please try again.',
                duration: 5000
            );
        }
    }

    /**
     * Confirm deletion of a level
     */
    #[On('open-delete-level-modal')]
    public function confirmDelete(int $id): void
    {
        $this->deleteLevelId = $id;
        Flux::modal('delete-level')->show();
    }

    /**
     * Delete the selected instructional level
     */
    public function deleteLevel(): void
    {
        try {
            $level = InstructionalLevel::findOrFail($this->deleteLevelId);
            $level->delete();

            $this->deleteLevelId = null;
            Flux::modals()->close();

            $this->dispatch('level-list-updated');

            $this->dispatch('notify',
                type: 'success',
                content: 'Instructional level deleted successfully.',
                duration: 5000
            );
        } catch (Exception $e) {
            Flux::modals()->close();
            Log::error('Error deleting instructional level', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'Failed to delete level. It may be in use.',
                duration: 5000
            );
        }
    }

    /**
     * Cancel create form
     */
    public function cancelCreate(): void
    {
        $this->reset(['code', 'name']);
        $this->resetErrorBag();
        Flux::modals()->close();
    }

    /**
     * Cancel delete
     */
    public function cancelDelete(): void
    {
        $this->deleteLevelId = null;
        Flux::modals()->close();
    }

    public function render()
    {
        return view('livewire.management.level-management');
    }
}
