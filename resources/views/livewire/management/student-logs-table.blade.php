<div>

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

            {{-- Search Students --}}
            <flux:input size="sm" icon="magnifying-glass" placeholder="Search log" class="max-w-50" wire:model.live.debounce.300ms="" autocomplete="off" clearable />

        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full border-separate border-spacing-y-[10px] -mt-2.5">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-3 py-1 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Log</th>
                                <th scope="col"
                                    class="px-3 py-1 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Academic Year</th>
                                <th scope="col"
                                    class="px-3 py-1 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($logSessions as $logSession)

                                <tr class="bg-white dark:bg-zinc-900 hover:bg-gray-100 dark:hover:bg-neutral-700">

                                    <td
                                        class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 border-t border-b border-black/10 dark:border-white/10 border-l rounded-l-lg">
                                        {{ \Carbon\Carbon::parse($logSession->date)->format('l, F j, Y') }}
                                    </td>
                                    <td
                                        class="px-3 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200 border-t border-b border-black/10 dark:border-white/10">
                                        {{ $logSession->school_year }}
                                    </td>
                                    <td
                                        class="px-3 py-4 whitespace-nowrap text-end text-sm font-medium border-t border-b border-black/10 dark:border-white/10 border-r rounded-r-lg">
                                        <div class="flex items-center justify-end">
                                            <div class="flex items-center gap-1">
                                                
                                                <flux:button
                                                    icon="eye"
                                                    variant="ghost"
                                                    size="sm"
                                                    class="cursor-pointer"
                                                    wire:click="viewLogs({{ $logSession->id }})"
                                                >
                                                    View
                                                </flux:button>

                                                <flux:tooltip content="Download Log" position="top">
                                                    <flux:button wire:click="" icon="arrow-down-tray" variant="ghost"
                                                        size="sm" class="cursor-pointer" />
                                                </flux:tooltip>
                                            </div>

                                        </div>
                                    </td>

                                </tr>

                            @endforeach

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

    <flux:modal name="view-logs" flyout variant="floating" position="right" class="md:w-xl w-4xl">
        <div class="space-y-6>
            
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

                    @if (collect($selectedLogRecords)->isNotEmpty())
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
                            @foreach ($selectedLogRecords as $record)
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
