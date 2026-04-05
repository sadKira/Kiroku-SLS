<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Senior High School</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    <div class="mt-10 mb-6 flex items-center justify-between">

        <flux:heading size="xl">SHS Student List</flux:heading>

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

            <flux:button wire:click="addStudent" icon="plus" variant="primary" size="sm">Add Student</flux:button>
        </div>
    </div>

    {{-- Table --}}
    <livewire:management.shs-list-table />

    {{-- Create Student Modal --}}
    <flux:modal name="create-students" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add SHS Student</flux:heading>
            </div>

            <flux:input wire:model="last_name" label="Last Name" placeholder="Last Name" />

            <flux:input wire:model="first_name" label="First Name" placeholder="First Name" />

            <flux:select wire:model="year_level" label="Year Level" placeholder="Select Year Level">
                <flux:select.option class="text-black dark:text-white" value="Grade 11">Grade 11</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Grade 12">Grade 12</flux:select.option>
            </flux:select>

            <flux:select wire:model="strand" label="Strand" placeholder="Select Strand">
                @foreach($strands as $strandItem)
                    <flux:select.option class="text-black dark:text-white" value="{{ $strandItem->name }}">{{ $strandItem->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="resetCreateForms">Cancel</flux:button>
                <flux:button wire:click="addStudentInformation" variant="primary" size="sm">
                    Add
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
