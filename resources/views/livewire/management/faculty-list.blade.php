<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Faculty</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    <div class="mt-10 mb-6 flex items-center justify-between">

        <flux:heading size="xl">Faculty List</flux:heading>

        {{-- Actions --}}
        <div class="flex items-center gap-1">
            <flux:dropdown>
                <flux:button icon="arrow-down-tray" variant="ghost" size="sm">Export</flux:button>
                <flux:menu>
                    <flux:menu.group heading="Paper Size">
                        <flux:menu.item wire:click="exportBarcodes('A4')">A4</flux:menu.item>
                        <flux:menu.item wire:click="exportBarcodes('Letter')">Letter</flux:menu.item>
                        <flux:menu.item wire:click="exportBarcodes('Legal')">Legal</flux:menu.item>
                    </flux:menu.group>
                </flux:menu>
            </flux:dropdown>

            <flux:button wire:click="addFaculty" icon="plus" variant="primary" size="sm">Add Faculty</flux:button>
        </div>
    </div>

    {{-- Table --}}
    <livewire:management.faculty-list-table />

    {{-- Create Faculty Modal --}}
    <flux:modal name="create-faculty" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Faculty</flux:heading>
            </div>

            <flux:input wire:model="last_name" label="Last Name" placeholder="Last Name" />

            <flux:input wire:model="first_name" label="First Name" placeholder="First Name" />

            <flux:select wire:model="instructional_level" label="Instructional Level" placeholder="Select Instructional Level">
                @foreach($instructionalLevels as $level)
                    <flux:select.option class="text-black dark:text-white" value="{{ $level->name }}">{{ $level->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="resetCreateForms">Cancel</flux:button>
                <flux:button wire:click="addFacultyInformation" variant="primary" size="sm">
                    Add
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
