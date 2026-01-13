<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">
        
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Student List</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>    

    {{-- Upper --}}
    <div class="flex items-center justify-between mb-5">

        <flux:heading size="lg">Student List</flux:heading>

        <div class="flex items-center gap-1">
            <flux:button icon="plus" wire:click="addStudent" variant="ghost" size="sm">Add Student</flux:button>

            
            @if ($students->count() > 0)
                
                <flux:dropdown>
                    <flux:button icon="arrow-down-tray" variant="primary" size="sm">
                        Download Barcodes
                    </flux:button>

                    <flux:menu>
                        <flux:menu.group heading="Paper Size">
                            <flux:menu.item wire:click="exportBarcodes('A4')">A4</flux:menu.item>
                            <flux:menu.item wire:click="exportBarcodes('Letter')">Letter</flux:menu.item>
                            <flux:menu.item wire:click="exportBarcodes('Legal')">Legal</flux:menu.item>
                        </flux:menu.group>
                    </flux:menu>
                </flux:dropdown>
            
            @endif
        </div>

    </div>

    {{-- Create Students Modal --}}
    <flux:modal name="create-students" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Student</flux:heading>
            </div>

            {{-- Name --}}
            <flux:input wire:model.defer="last_name" type="text" label="Last Name" placeholder="Last Name" />

            <flux:input wire:model.defer="first_name" type="text" label="First Name" placeholder="First Name" />

            {{-- Student ID --}}
            <flux:input wire:model.defer="id_student" type="text" label="Student ID" mask="9999999" placeholder="7-Digit ID" />

            {{-- Year Level --}}
            <flux:select wire:model.defer="year_level" label="Year level" placeholder="Year Level">
                <flux:select.option class="text-black dark:text-white" value="1st Year">1st Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="2nd Year">2nd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="3rd Year">3rd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="4th Year">4th Year</flux:select.option>
            </flux:select>

            {{-- Course --}}
            <flux:select wire:model.defer="course" label="Course" placeholder="Course">
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Arts in International Studies">Bachelor of Arts in International Studies</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Science in Information Systems">Bachelor of Science in Information Systems</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Human Services">Bachelor of Human Services</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Secondary Education">Bachelor of Secondary Education</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Elementary Education">Bachelor of Elementary Education</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Special Needs Education">Bachelor of Special Needs Education</flux:select.option>
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

    {{-- Table --}}
    <livewire:management.student-list-table />



    

</div>
