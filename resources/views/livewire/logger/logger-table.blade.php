<div>
    {{-- Two Column Layout --}}
    <div class="flex flex-col lg:flex-row gap-6 mt-5">

        {{-- Left Column: Filters --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-4 sticky top-4">
                
                {{-- Search Bar --}}
                <div class="mb-6">
                    <flux:heading size="sm" class="mb-2">Search</flux:heading>
                    <flux:input 
                        size="sm" 
                        icon="magnifying-glass" 
                        placeholder="Search by day, month, or academic year (e.g., Monday, January, 2023-2024)" 
                        class="w-full"
                        wire:model.live.debounce.300ms="search" 
                        autocomplete="off" 
                        clearable 
                    />
                </div>

                {{-- Filters Section --}}
                <div class="space-y-4">
                    <flux:heading size="sm" class="mb-3">Filters:</flux:heading>

                    {{-- Select Month --}}
                    <div class="flex flex-col gap-2">
                        <flux:heading size="xs">Month</flux:heading>
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
                    <div class="flex flex-col gap-2">
                        <flux:heading size="xs">Year</flux:heading>
                        <flux:select size="sm" wire:model.live="filterYear" placeholder="Select Year" clearable>
                            @foreach ($availableYears as $year)
                                <flux:select.option class="text-black dark:text-white" value="{{ $year }}">
                                    {{ $year }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Select Academic Year --}}
                    <div class="flex flex-col gap-2">
                        <flux:heading size="xs">Academic Year</flux:heading>
                        <flux:select size="sm" wire:model.live="filterAcademicYear" placeholder="Select Academic Year" clearable>
                            @foreach ($availableAcademicYears as $academicYear)
                                <flux:select.option class="text-black dark:text-white" value="{{ $academicYear }}">
                                    {{ $academicYear }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>

                {{-- Active Filters --}}
                @if ($filterMonth != '' || $filterYear != '' || $filterAcademicYear != '' || $search != '')
                    <div class="mt-6 pt-6 border-t border-black/10 dark:border-white/10">
                        <flux:heading size="xs" class="mb-3">Active Filters</flux:heading>
                        <div class="flex flex-wrap gap-2">
                            @if ($search != '')
                                <flux:badge variant="solid" color="zinc">
                                    Search: {{ $search }}
                                    <flux:badge.close wire:click="$set('search', '')" />
                                </flux:badge>
                            @endif
                            @if ($filterMonth != '')
                                <flux:badge variant="solid" color="zinc">
                                    {{ $filterMonth }}
                                    <flux:badge.close wire:click="clearFilterMonth" />
                                </flux:badge>
                            @endif
                            @if ($filterYear != '')
                                <flux:badge variant="solid" color="zinc">
                                    {{ $filterYear }}
                                    <flux:badge.close wire:click="clearFilterYear" />
                                </flux:badge>
                            @endif
                            @if ($filterAcademicYear != '')
                                <flux:badge variant="solid" color="zinc">
                                    {{ $filterAcademicYear }}
                                    <flux:badge.close wire:click="clearFilterAcademicYear" />
                                </flux:badge>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Log Session Cards --}}
        <div class="flex-1">
            @if ($logSessions->isEmpty())
                <div class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-12">
                    <div class="flex flex-col justify-center items-center gap-3">
                        <flux:icon.magnifying-glass variant="solid" class="w-12 h-12 text-gray-400 dark:text-neutral-500" />
                        <flux:heading size="lg">No Log Sessions Found</flux:heading>
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Try adjusting your filters or search terms.</p>
                    </div>
                </div>
            @else
                {{-- Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($logSessions as $logSession)
                        <a href="{{ route('view_logs', $logSession) }}" wire:navigate class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-5 hover:shadow-lg transition-shadow">
                            {{-- Date (Name) --}}
                            <div class="mb-3">
                                <flux:heading size="base" class="text-gray-800 dark:text-neutral-200">
                                    {{ \Carbon\Carbon::parse($logSession->date)->format('F j, Y') }}
                                </flux:heading>
                                <flux:heading size="base" class="text-gray-800 dark:text-neutral-200">
                                    {{ \Carbon\Carbon::parse($logSession->date)->format('l') }}
                                </flux:heading>
                            </div>

                            {{-- School Year --}}
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    {{ $logSession->school_year }}
                                </p>
                            </div>

                            {{-- Log Count / Student Count --}}
                            <div class="flex items-center justify-end gap-2 pt-4 border-t border-black/10 dark:border-white/10">
                                <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-neutral-400">
                                    <flux:icon.users variant="outline" class="w-4 h-4" />
                                    @php
                                        $count = $logSession->students_count ?? $logSession->log_records_count ?? 0;
                                    @endphp
                                    <span>{{ $count }} {{ $count == 1 ? 'student' : 'students' }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($logSessions->hasPages())
                    <div class="mt-6">
                        {{ $logSessions->links('pagination::tailwind') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
