<div>

    {{-- Table --}}
    <div class="flex flex-col">

        <div class="flex items-center justify-between mb-5">

            {{-- Filter --}}
            <div class="flex items-center gap-3">

                {{-- Select Month --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Month:</flux:heading>
                    <flux:select size="sm" wire:model.live="filterMonth" placeholder="Select Month" clearable>
                        <flux:select.option class="text-black dark:text-white" value="January">January</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="February">February</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="March">March</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="April">April</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="May">May</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="June">June</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="July">July</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="August">August</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="September">September</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="October">October</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="November">November</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="December">December</flux:select.option>
                    </flux:select>
                </div>

                {{-- Select Year --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Year:</flux:heading>
                    <flux:select size="sm" wire:model.live="filterYear" placeholder="Select Year" clearable>
                        @foreach ($availableYears as $year)
                            <flux:select.option class="text-black dark:text-white" value="{{ $year }}">{{ $year }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                {{-- Select Academic Year --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Academic Year:</flux:heading>
                    <flux:select size="sm" wire:model.live="filterAcademicYear" placeholder="Select Academic Year" clearable>
                        @foreach ($availableAcademicYears as $academicYear)
                            <flux:select.option class="text-black dark:text-white" value="{{ $academicYear }}">{{ $academicYear }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

            </div>

            {{-- Add Log --}}
            <flux:button icon="plus" wire:click="addLogSession" variant="primary" size="sm">Add Log</flux:button>

        </div>

        {{-- Filter Indicators --}}
        <div class="flex items-center gap-3 mb-5">
            @if ($filterMonth != '' || $filterYear != '' || $filterAcademicYear != '')
                <flux:heading>Filters:</flux:heading>
            @endif
            @if ($filterMonth != '')
                <flux:badge variant="solid" color="zinc">
                    {{ $filterMonth }} <flux:badge.close wire:click="clearFilterMonth" />
                </flux:badge>
            @endif
            @if ($filterYear != '')
                <flux:badge variant="solid" color="zinc">
                    {{ $filterYear }} <flux:badge.close wire:click="clearFilterYear" />
                </flux:badge>
            @endif
            @if ($filterAcademicYear != '')
                <flux:badge variant="solid" color="zinc">
                    {{ $filterAcademicYear }} <flux:badge.close wire:click="clearFilterAcademicYear" />
                </flux:badge>
            @endif
        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full border-separate border-spacing-y-[10px] -mt-2.5">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-4 py-1 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Log</th>
                                <th scope="col"
                                    class="px-4 py-1 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Academic Year</th>
                                <th scope="col"
                                    class="px-4 py-1 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @if ($logSessions->isEmpty())
                                
                                <tr>
                                    <td colspan="3" class="px-6 py-10 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        <div class="flex justify-center items-center gap-2 w-full">
                                            <flux:icon.magnifying-glass variant="solid" class="" />
                                            <flux:heading size="lg">No Log Sessions</flux:heading>
                                        </div>
                                    </td>
                                </tr>

                            @else
                                @foreach ($logSessions as $logSession)

                                <tr class="bg-white dark:bg-zinc-900 hover:bg-gray-100 dark:hover:bg-neutral-700">

                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 border-t border-b border-black/10 dark:border-white/10 border-l rounded-l-lg">
                                        {{ \Carbon\Carbon::parse($logSession->date)->format('l, F j, Y') }}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200 border-t border-b border-black/10 dark:border-white/10">
                                        {{ $logSession->school_year }}
                                    </td>
                                    <td
                                        class="px-4 py-4 whitespace-nowrap text-end text-sm font-medium border-t border-b border-black/10 dark:border-white/10 border-r rounded-r-lg">
                                        <div class="flex items-center justify-end">
                                            <div class="flex items-center gap-1">
                                                
                                                @if ($logSession->log_records_count > 0)
                                                    <div wire:key="view-button-{{ $logSession->id }}">
                                                        <flux:button
                                                            icon="eye"
                                                            variant="ghost"
                                                            size="sm"
                                                            class="cursor-pointer"
                                                            wire:click="viewLogs({{ $logSession->id }})"
                                                            wire:target="viewLogs({{ $logSession->id }})"
                                                        >
                                                            View
                                                        </flux:button>
                                                    </div>

                                                    <div wire:key="export-dropdown-{{ $logSession->id }}">
                                                        <flux:dropdown>
                                                            <flux:button 
                                                                icon="arrow-down-tray" 
                                                                variant="ghost"
                                                                size="sm" 
                                                                class="cursor-pointer"
                                                            />

                                                            <flux:menu>
                                                                <flux:menu.group heading="Paper Size">
                                                                    <flux:menu.item 
                                                                        wire:click="exportStudentLogs({{ $logSession->id }}, 'A4')"
                                                                        wire:target="exportStudentLogs({{ $logSession->id }}, 'A4')"
                                                                    >
                                                                        A4
                                                                    </flux:menu.item>
                                                                    <flux:menu.item 
                                                                        wire:click="exportStudentLogs({{ $logSession->id }}, 'Letter')"
                                                                        wire:target="exportStudentLogs({{ $logSession->id }}, 'Letter')"
                                                                    >
                                                                        Letter
                                                                    </flux:menu.item>
                                                                    <flux:menu.item 
                                                                        wire:click="exportStudentLogs({{ $logSession->id }}, 'Legal')"
                                                                        wire:target="exportStudentLogs({{ $logSession->id }}, 'Legal')"
                                                                    >
                                                                        Legal
                                                                    </flux:menu.item>
                                                                </flux:menu.group>
                                                            </flux:menu>
                                                        </flux:dropdown>
                                                    </div>
                                                @endif

                                                <div 
                                                    x-data="{ isHovered: false }" 
                                                    @mouseenter="isHovered = true" 
                                                    @mouseleave="isHovered = false"
                                                    >
                                                    <!-- Outline variant shown by default -->
                                                    <flux:tooltip content="Delete Log" position="top">
                                                        <flux:icon.trash 
                                                            x-show="!isHovered" 
                                                            variant="outline" 
                                                            class="text-red-500 cursor-pointer ml-3" 
                                                        />
                                                    </flux:tooltip>

                                                    <!-- Solid variant shown only on hover -->
                                                    <flux:tooltip content="Delete Student" position="top">
                                                        <flux:icon.trash 
                                                            x-show="isHovered" 
                                                            variant="solid" 
                                                            class="text-red-500 cursor-pointer ml-3" 
                                                            x-cloak 
                                                            wire:click="removeLogSession({{ $logSession->id }})"
                                                        />
                                                    </flux:tooltip>

                                                </div>
                                            </div>

                                        </div>
                                    </td>

                                </tr>

                                @endforeach
                            @endif
                            
                        </tbody>
                    </table>

                    @if ($logSessions->hasPages())
                        <div class="mt-4">
                            {{ $logSessions->links('pagination::tailwind') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Create Log Sessions Modal --}}
    <flux:modal name="create-log-session" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Log Session</flux:heading>
            </div>

            {{-- Log Session (Date) --}}
            <div>
                <input type="hidden" wire:model="date"/>
                <flux:input 
                    readonly 
                    type="text" 
                    label="Log Session" 
                    placeholder="Log Session"
                    value="{{ $this->formatted_date }}" 
                    variant="filled"
                    icon:trailing="lock-closed"
                />
                
            </div>

            {{-- Academic Year --}}
            <div class="space-y-4">
                <div>
                    <flux:heading size="sm" class="mb-2">Academic Year</flux:heading>
                    <flux:field>
                        <div class="flex items-center gap-2">
                            {{-- Start Year --}}
                            <flux:input 
                                wire:model.live="start_year" 
                                mask="9999" 
                                type="text" 
                                label="Start Year" 
                                placeholder="YYYY"
                                class="flex-1" 
                                name="startYear"
                            />
                            
                            <span class="mt-6 text-gray-500 dark:text-neutral-400">-</span>
                            
                            {{-- End Year (Readonly) --}}
                            <flux:input 
                                wire:model="end_year" 
                                readonly 
                                mask="9999" 
                                type="text" 
                                label="End Year" 
                                placeholder="YYYY"
                                class="flex-1"
                                variant="filled"
                                icon:trailing="lock-closed"
                            />
                        </div>
                        <flux:error name="school_year" />
                    </flux:field>
                </div>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="resetCreateForms">Cancel</flux:button>
                <flux:button wire:click="addLogSessionInformation" variant="primary" size="sm">
                    Add
                </flux:button>
            </div>
        </div>
    </flux:modal>

        {{-- Delete Log Session Modal --}}
    <flux:modal name="remove-log-session" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Log Session?</flux:heading>
                <flux:text class="mt-2">
                    You are about to delete the log session for 
                </flux:text>
                <flux:text class="">
                    <span class="font-bold">{{ $logSessionDate ?? 'error' }}</span> ({{ $logSessionSchoolYear ?? 'error' }}).
                </flux:text>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteLogSessionInformation" variant="danger" size="sm">
                    Delete
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- View Logs Modal --}}
    <flux:modal name="view-logs" flyout variant="floating" position="right" class="md:w-xl w-4xl">
        <div class="space-y-6">
            
            {{-- View Logs --}}
            @if ($selectedLogSession)
                <div class="space-y-3">
                    <div class="space-y-1">
                        <flux:heading size="lg" class="font-semibold">
                            {{ \Carbon\Carbon::parse($selectedLogSession->date)->format('l, F j, Y') }}
                        </flux:heading>
                        <p class="text-sm text-gray-500 dark:text-neutral-400">
                            Academic Year: {{ $selectedLogSession->school_year }}
                        </p>
                    </div>

                    @if ($this->selectedLogRecords->isNotEmpty())
                        <div class="mt-2 space-y-2
                            overflow-y-auto
                            {{-- max-h-100 --}}
                            max-h-[75vh]
                            [&::-webkit-scrollbar]:w-2
                            [&::-webkit-scrollbar-thumb]:rounded-full
                            [&::-webkit-scrollbar-track]:bg-gray-100
                            [&::-webkit-scrollbar-thumb]:bg-gray-300
                            dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                            dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500
                        ">
                            @foreach ($this->selectedLogRecords as $record)
                                <div
                                    class="mr-2 flex items-center justify-between gap-4 rounded-lg border border-black/10 dark:border-white/10 px-3 py-2 text-sm bg-white/60 dark:bg-zinc-900/70">
                                    <div class="space-y-0.5">

                                        {{-- Student Name --}}
                                        <p class="font-semibold text-gray-900 dark:text-neutral-50">
                                            @php
                                                $student = $record->student;
                                            @endphp
                                            @if ($student)
                                                {{ $student->last_name }}, {{ $student->first_name }}
                                            @else
                                                <span class="text-gray-400">Unknown student</span>
                                            @endif
                                        </p>

                                        {{-- Student Year Leve & Course --}}
                                        @if ($student)
                                            <p class="text-xs text-gray-500 dark:text-neutral-400">
                                                {{ $student->year_level }}-{{ $student->course }}
                                            </p>
                                        @endif
                                    </div>
                                    <div>
                                        {{-- Student Time In --}}
                                        @if ($record->time_in)
                                            <div class="flex items-center gap-1">
                                                <flux:icon.log-in class="text-green-500" variant="micro" />
                                                <p class="text-right text-green-500">
                                                    {{ \Carbon\Carbon::parse($record->time_in)->timezone('Asia/Manila')->format('g:i a') }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">—</span>
                                        @endif

                                        {{-- Student Time out --}}
                                        @if ($record->time_out)
                                            <div class="flex items-center gap-1">
                                                <flux:icon.log-out class="text-red-500" variant="micro" />
                                                <p class="text-right text-red-500">
                                                    {{ \Carbon\Carbon::parse($record->time_out)->timezone('Asia/Manila')->format('g:i a') }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">—</span>
                                        @endif

                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-neutral-400">
                            No logs have been recorded for this session yet.
                        </p>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-neutral-400">
                    Select a log session to view its records.
                </p>
            @endif
        </div>
    </flux:modal>

</div>
