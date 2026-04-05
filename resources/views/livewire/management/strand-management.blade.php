<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Strands & Levels</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Strands</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex justify-between items-center mb-5">
        <flux:heading size="xl">Strand Management</flux:heading>

        <flux:button wire:click="addStrand" icon="plus" variant="primary" size="sm">Add Strand</flux:button>
    </div>

    {{-- Table --}}
    <livewire:management.strand-list-table />

    {{-- Create Strand Modal --}}
    <flux:modal name="create-strand" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Strand</flux:heading>
                <flux:text class="mt-1">Enter the details for the new strand.</flux:text>
            </div>

            <flux:input wire:model="code" label="Code" placeholder="e.g. BSIS" />

            <flux:input wire:model="name" label="Name" placeholder="e.g. Bachelor of Science in Information Systems" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="cancelCreate">Cancel</flux:button>
                <flux:button wire:click="storeStrand" variant="primary" size="sm">
                    Add
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-strand" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Strand</flux:heading>
                <flux:text class="mt-1">Are you sure you want to delete this strand? This action cannot be undone.</flux:text>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="cancelDelete">Cancel</flux:button>
                <flux:button wire:click="deleteStrand" variant="danger" size="sm">
                    Delete
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
