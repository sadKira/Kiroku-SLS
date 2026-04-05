<?php

namespace App\Livewire\Management;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

use Illuminate\Support\Str;
use App\Models\Course;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.app')]
class CourseManagement extends Component
{
    // Form fields
    public string $code = '';
    public string $name = '';

    // Delete confirmation
    public ?int $deleteCourseId = null;

    /**
     * Open the create modal
     */
    public function addCourse(): void
    {
        $this->code = '';
        $this->name = '';
        $this->resetErrorBag();

        Flux::modal('create-course')->show();
    }

    /**
     * Store a new course
     */
    public function storeCourse(): void
    {
        try {
            $validated = $this->validate([
                'code' => ['required', 'string', 'max:20', Rule::unique('courses', 'code')],
                'name' => ['required', 'string', 'max:255', Rule::unique('courses', 'name')],
            ], [
                'code.required' => 'Course code is required.',
                'code.unique' => 'This course code already exists.',
                'name.required' => 'Course name is required.',
                'name.unique' => 'This course name already exists.',
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

            Course::create($validated);

            $this->reset(['code', 'name']);
            Flux::modals()->close();

            $this->dispatch('course-list-updated');

            $this->dispatch('notify',
                type: 'success',
                content: 'Course added successfully.',
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
            Log::error('Error adding course', [
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
     * Confirm deletion of a course
     */
    #[On('open-delete-course-modal')]
    public function confirmDelete(int $id): void
    {
        $this->deleteCourseId = $id;
        Flux::modal('delete-course')->show();
    }

    /**
     * Delete the selected course
     */
    public function deleteCourse(): void
    {
        try {
            $course = Course::findOrFail($this->deleteCourseId);
            $course->delete();

            $this->deleteCourseId = null;
            Flux::modals()->close();

            $this->dispatch('course-list-updated');

            $this->dispatch('notify',
                type: 'success',
                content: 'Course deleted successfully.',
                duration: 5000
            );
        } catch (Exception $e) {
            Flux::modals()->close();
            Log::error('Error deleting course', [
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify',
                type: 'error',
                content: 'Failed to delete course. It may be in use.',
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
        $this->deleteCourseId = null;
        Flux::modals()->close();
    }

    public function render()
    {
        return view('livewire.management.course-management');
    }
}
