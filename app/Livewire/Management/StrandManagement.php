<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

use Illuminate\Support\Str;
use App\Models\Strand;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class StrandManagement extends Component
{
    // Form fields
    public string $code = '';
    public string $name = '';

    // Delete confirmation
    public ?int $deleteStrandId = null;

    /**
     * Open the create modal
     */
    public function addStrand(): void
    {
        $this->code = '';
        $this->name = '';
        $this->resetErrorBag();

        Flux::modal('create-strand')->show();
    }

    /**
     * Store a new strand
     */
    public function storeStrand(): void
    {
        try {
            $validated = $this->validate([
                'code' => ['required', 'string', 'max:20', Rule::unique('strands', 'code')],
                'name' => ['required', 'string', 'max:255', Rule::unique('strands', 'name')],
            ], [
                'code.required' => 'Strand code is required.',
                'code.unique' => 'This strand code already exists.',
                'name.required' => 'Strand name is required.',
                'name.unique' => 'This strand name already exists.',
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

            Strand::create($validated);

            $this->reset(['code', 'name']);
            Flux::modals()->close();

            $this->dispatch('strand-list-updated');

            $this->dispatch('notify',
                type: 'success',
                content: 'Strand added successfully.',
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
            Log::error('Error adding strand', [
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
     * Confirm deletion of a strand
     */
    #[On('open-delete-strand-modal')]
    public function confirmDelete(int $id): void
    {
        $this->deleteStrandId = $id;
        Flux::modal('delete-strand')->show();
    }

    /**
     * Delete the selected strand
     */
    public function deleteStrand(): void
    {
        try {
            $strand = Strand::findOrFail($this->deleteStrandId);
            $strand->delete();

            $this->deleteStrandId = null;
            Flux::modals()->close();

            $this->dispatch('strand-list-updated');

            $this->dispatch('notify',
                type: 'success',
                content: 'Strand deleted successfully.',
                duration: 5000
            );
        } catch (Exception $e) {
            Flux::modals()->close();
            Log::error('Error deleting strand', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'Failed to delete strand. It may be in use.',
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
        $this->deleteStrandId = null;
        Flux::modals()->close();
    }

    public function render()
    {
        return view('livewire.management.strand-management');
    }
}
