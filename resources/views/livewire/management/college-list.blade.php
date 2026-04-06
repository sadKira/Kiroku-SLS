<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>College</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    <div class="mt-10 mb-6 flex items-center justify-between">

        <div class="flex items-center gap-2">
            <flux:heading size="xl">College Student List</flux:heading>
            <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 dark:bg-neutral-800 px-2 py-0.5 text-xs font-medium text-neutral-500 dark:text-neutral-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M9 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM17 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 0 0-1.5-4.33A5 5 0 0 1 19 16v1h-6.07ZM6 11a5 5 0 0 1 5 5v1H1v-1a5 5 0 0 1 5-5Z" />
                </svg>
                {{ number_format($totalCount) }} {{ $totalCount === 1 ? 'student' : 'students' }}
            </span>
        </div>

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
    <livewire:management.college-list-table />

    {{-- Create Student Modal --}}
    <flux:modal name="create-students" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add College Student</flux:heading>
            </div>

            <flux:input wire:model="last_name" label="Last Name" placeholder="Last Name" />

            <flux:input wire:model="first_name" label="First Name" placeholder="First Name" />

            <flux:select wire:model="year_level" label="Year Level" placeholder="Select Year Level">
                <flux:select.option class="text-black dark:text-white" value="1st Year">1st Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="2nd Year">2nd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="3rd Year">3rd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="4th Year">4th Year</flux:select.option>
            </flux:select>

            <flux:select wire:model="course" label="Course" placeholder="Select Course">
                @foreach($courses as $courseItem)
                    <flux:select.option class="text-black dark:text-white" value="{{ $courseItem->name }}">{{ $courseItem->name }}</flux:select.option>
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
