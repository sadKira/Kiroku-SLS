<div>
     {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('student_logs') }}">Student Logs</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        {{-- Profile Section --}}
        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex items-center justify-between mb-5">

        <flux:heading size="lg" class="font-bold">Student Logs</flux:heading>

    </div>

    {{-- Table --}}
    <div class="flex flex-col">

        <div class="flex items-center justify-between mb-5">

            {{-- Filter --}}
            <div class="flex items-center gap-3">

                {{-- Select Month --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Month:</flux:heading>
                    <flux:select size="sm" wire:model="" placeholder="Select Month">
                        <flux:select.option class="text-black dark:text-white">January</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">February</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">March</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">April</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">May</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">June</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">July</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">August</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">September</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">October</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">November</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">December</flux:select.option>
                    </flux:select>
                </div>
               
                {{-- Select Year --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Year:</flux:heading>
                    <flux:select size="sm" wire:model="" placeholder="Select Year">
                        <flux:select.option class="text-black dark:text-white">January</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">February</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">March</flux:select.option>
                    </flux:select>
                </div>

                {{-- Select Academic Year --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Academic Year:</flux:heading>
                    <flux:select size="sm" wire:model="" placeholder="Select Academic Year">
                        <flux:select.option class="text-black dark:text-white">January</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">February</flux:select.option>
                        <flux:select.option class="text-black dark:text-white">March</flux:select.option>
                    </flux:select>
                </div>

            </div>

            {{-- Add Log --}}
            <flux:button icon="plus" wire:click="" variant="primary" size="sm">Add Log</flux:button>

        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                        <table class="min-w-full border-separate border-spacing-y-[10px] -mt-2.5">
                            <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-3 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        Log</th>
                                    <th scope="col"
                                        class="px-3 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        Academic Year</th>
                                    <th scope="col"
                                            class="px-3 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                            </th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- @foreach($students as $student) --}}
                                
                                    <tr class="bg-white dark:bg-zinc-900 hover:bg-gray-100 dark:hover:bg-neutral-700">
                                        
                                        <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 border-t border-b border-black/10 dark:border-white/10 border-l rounded-l-lg">
                                            Log Date
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200 border-t border-b border-black/10 dark:border-white/10">
                                            Academic Year
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-end text-sm font-medium border-t border-b border-black/10 dark:border-white/10 border-r rounded-r-lg">
                                            <div class="flex items-center justify-end">
                                                <div class="flex items-center gap-2">
                                                    <flux:modal.trigger name="edit-profile">
                                                        <flux:button icon="eye" variant="filled" size="sm" class="cursor-pointer">View Record</flux:button>
                                                    </flux:modal.trigger>
                                                    <flux:button wire:click="" icon="arrow-down-tray" variant="primary" size="sm" class="cursor-pointer"></flux:button>
                                                </div>
                                                
                                            </div>
                                        </td>
                                        
                                    </tr>
                                
                                {{-- @encdforeach --}}

                            </tbody>
                        </table>
                        
                </div>
            </div>
        </div>
    </div>

    <flux:modal name="edit-profile" flyout variant="floating" position="right" class="md:w-xl w-4xl">
        <div class="space-y-6">
            <flux:heading size="lg">Update profile</flux:heading>
            <flux:subheading>Make changes to your personal details.</flux:subheading>
            <flux:input label="Name" placeholder="Your name" />
            <flux:input label="Date of birth" type="date" />
        </div>
        <x-slot name="footer" class="flex items-center justify-end gap-2">
            <flux:modal.close>
                <flux:button variant="filled">Cancel</flux:button>
            </flux:modal.close>
            <flux:button type="submit" variant="primary">Save changes</flux:button>
        </x-slot>
    </flux:modal>


</div>

