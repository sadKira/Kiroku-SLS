<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Courses & Levels</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Courses</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex justify-between items-center mb-5">
        <flux:heading size="xl">Course Management</flux:heading>

        <flux:button wire:click="addCourse" icon="plus" variant="primary" size="sm">Add Course</flux:button>
    </div>

    {{-- Table --}}
    <livewire:management.course-list-table />

    {{-- Create Course Modal --}}
    <flux:modal name="create-course" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Course</flux:heading>
                <flux:text class="mt-1">Enter the details for the new course.</flux:text>
            </div>

            <flux:input wire:model="code" label="Code" placeholder="e.g. BSIS" />

            <flux:input wire:model="name" label="Name" placeholder="e.g. Bachelor of Science in Information Systems" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="cancelCreate">Cancel</flux:button>
                <flux:button wire:click="storeCourse" variant="primary" size="sm">
                    Add
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-course" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Course</flux:heading>
                <flux:text class="mt-1">Are you sure you want to delete this course? This action cannot be undone.</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button wire:click="deleteCourse" variant="danger" size="sm">
                    Delete
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
