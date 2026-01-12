<div class=""> 

    {{-- App Header --}}
    <x-management.profile-section />

    <div class="">
        {{-- Header with Set Academic Year Button --}}
        <div class="mt-6 mb-6 flex items-center justify-between">
            <flux:heading size="lg">Dashboard</flux:heading>
            <flux:button 
                icon="cog-6-tooth" 
                wire:click="openSetSchoolYearModal" 
                variant="ghost" 
                size="sm"
            >
                Set Academic Year
            </flux:button>
        </div>

        {{-- Two Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-h-screen">
            {{-- Left Column: Cards and Chart --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Metric Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Academic Year - Highlight Card --}}
                    <x-ui.card class="!max-w-none !bg-black !dark:bg-black !border-black/20 !dark:border-white/20">
                        <div class="flex flex-col">
                            <p class="text-sm text-white/80 mb-1">Academic Year</p>
                            <p class="text-2xl font-semibold text-white">{{ $activeSchoolYear }}</p>
                        </div>
                    </x-ui.card>

                    {{-- Total Students Card --}}
                    <x-ui.card class="!max-w-none">
                        <div class="flex flex-col">
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Total Students</p>
                            <p class="text-2xl font-semibold">{{ number_format($totalStudents) }}</p>
                        </div>
                    </x-ui.card>

                    {{-- Active Students Card --}}
                    <x-ui.card class="!max-w-none">
                        <div class="flex flex-col">
                            <p class="text-sm text-neutral-600 dark:text-neutral-400">Active Students</p>
                            <p class="text-2xl font-semibold">{{ $attendancePercentage }}%</p>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Activity Chart --}}
                <x-ui.card class="!max-w-none">
                    <div class="flex flex-col">
                        <div class="flex items-center justify-between">
                            <flux:heading class="">
                                {{ $chartTitleValue }} total logs for A.Y. {{ $activeSchoolYear }}
                            </flux:heading>
                        </div>

                        <div id="activityChart" wire:key="activity-chart-{{ $activeSchoolYear }}" class="w-full"></div>
                    </div>
                </x-ui.card>
            </div>

            {{-- Right Column: Today's Logs Table --}}
            <div class="lg:col-span-1">
                <x-ui.card class="!max-w-none">
                    <div class="flex flex-col">
                        <div class="flex items-center mb-2">
                            <flux:heading size="" class="">
                                {{ \Carbon\Carbon::now('Asia/Manila')->format('F j, Y') }}
                            </flux:heading>
                            <svg fill="#00C951" width="24px" height="24px" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" stroke="#00C951">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M7.8 10a2.2 2.2 0 0 0 4.4 0 2.2 2.2 0 0 0-4.4 0z"></path>
                                </g>
                            </svg>
                        </div>
                        
                        @if($todayLogRecords->isEmpty())
                            <div class="flex flex-col justify-center items-center gap-3 py-12">
                                <flux:icon.clipboard-document-list variant="solid" class="w-12 h-12 text-gray-400 dark:text-neutral-500" />
                                <flux:heading size="lg" class="text-gray-800 dark:text-neutral-200">No Log Records Today</flux:heading>
                            </div>
                        @else
                            <div class="max-h-[50vh] overflow-y-auto
                                [&::-webkit-scrollbar]:w-2
                                [&::-webkit-scrollbar-thumb]:rounded-full
                                [&::-webkit-scrollbar-track]:bg-gray-100
                                [&::-webkit-scrollbar-thumb]:bg-gray-300
                                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                                @foreach($todayLogRecords as $record)
                                    <div class="flex gap-x-3">
                                        {{-- Left Content: Time --}}
                                        <div class="min-w-14 text-end">
                                            <span class="text-xs text-gray-500 dark:text-neutral-400">
                                                {{ \Carbon\Carbon::parse($record->time_in)->format('g:i A') }}
                                            </span>
                                        </div>

                                        {{-- Icon --}}
                                        <div class="relative {{ $loop->last ? '' : 'after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700' }}">
                                            <div class="relative z-10 size-7 flex justify-center items-center">
                                                <div class="size-2 rounded-full bg-gray-400 dark:bg-neutral-600"></div>
                                            </div>
                                        </div>

                                        {{-- Right Content --}}
                                        <div class="grow pt-0.5 {{ $loop->last ? 'pb-0' : 'pb-8' }}">
                                            <h3 class="flex gap-x-1.5 font-semibold text-gray-800 dark:text-white">
                                                @if($record->student)
                                                    {{ $record->student->last_name }}, {{ $record->student->first_name }}
                                                @else
                                                    Unknown Student
                                                @endif
                                            </h3>
                                            @if($record->student)
                                                <p class="mt-1 text-sm text-gray-600 dark:text-neutral-400">
                                                    {{ $record->student->year_level }} - 
                                                    @php
                                                        $course = $record->student->course;
                                                        $courseAbbr = match (true) {
                                                            $course == 'Bachelor of Arts in International Studies' => 'ABIS',
                                                            $course == 'Bachelor of Science in Information Systems' => 'BSIS',
                                                            $course == 'Bachelor of Human Services' => 'BHS',
                                                            $course == 'Bachelor of Secondary Education' => 'BSED',
                                                            $course == 'Bachelor of Elementary Education' => 'ECED',
                                                            $course == 'Bachelor of Special Needs Education' => 'SNED',
                                                            default => $course,
                                                        };
                                                    @endphp
                                                    {{ $courseAbbr }}
                                                </p>
                                            @endif
                                            @if($record->time_out)
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                    Out: {{ \Carbon\Carbon::parse($record->time_out)->format('g:i A') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>

    {{-- Set Academic Year Modal --}}
    <flux:modal name="set-academic-year" :dismissible="false">
        <flux:heading size="lg" class="mb-4">Set Academic Year</flux:heading>
        
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

        <div class="flex gap-2 mt-6">
            <flux:spacer />
            <flux:button variant="ghost" size="sm" wire:click="resetSetSchoolYearForm">Cancel</flux:button>
            <flux:button wire:click="setSchoolYear" variant="primary" size="sm">
                Set Academic Year
            </flux:button>
        </div>
    </flux:modal>
</div>
<script>
    let activityChart = null;

    function initializeChart() {
        // Check if ApexCharts is loaded
        if (typeof ApexCharts === 'undefined') {
            console.warn('ApexCharts is not loaded');
            return;
        }

        // Activity Chart
        const activityChartElement = document.querySelector("#activityChart");
        if (activityChartElement) {
            // Destroy existing chart if it exists
            if (activityChart) {
                activityChart.destroy();
            }

            const activityData = @json($activityChartData);
            
            activityChart = new ApexCharts(activityChartElement, {
                series: [{
                    name: 'Logged Students',
                    data: activityData.map(item => item.value)
                }],
                chart: {
                    type: 'area',
                    height: 200,
                    toolbar: { show: false },
                    fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                    sparkline: { enabled: false }
                },
                colors: ['#52525b'],
                stroke: {
                    curve: 'smooth',
                    width: 2,
                    colors: ['#52525b']
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.5,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 100],
                        colorStops: [
                            {
                                offset: 0,
                                color: '#52525b',
                                opacity: 0.7
                            },
                            {
                                offset: 100,
                                color: '#52525b',
                                opacity: 0.3
                            }
                        ]
                    }
                },
                grid: {
                    show: true,
                    borderColor: '#e4e4e7',
                    strokeDashArray: 0,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                xaxis: {
                    categories: activityData.map(item => item.label),
                    labels: {
                        style: {
                            colors: '#71717a',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                },
                yaxis: {
                    floating: true,
                    labels: {
                        style: {
                            colors: '#ffffff',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                },
                tooltip: {
                    theme: 'light',
                    style: {
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                    }
                }
            });

            activityChart.render();
        }
    }

    // Initialize on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', initializeChart);

    // Handle Livewire navigation events
    document.addEventListener('livewire:navigated', initializeChart);
    document.addEventListener('livewire:navigating', () => {
        if (activityChart) {
            activityChart.destroy();
            activityChart = null;
        }
    });

    // Handle wire:navigate
    document.addEventListener('wire:navigated', initializeChart);

    // Update charts when Livewire updates
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', () => {
            setTimeout(initializeChart, 100);
        });
    }
    
    // Make initializeChart globally accessible for Alpine.js
    window.initializeChart = initializeChart;
    
    // Listen for Livewire property updates
    document.addEventListener('livewire:init', () => {
        Livewire.on('$refresh', () => {
            setTimeout(initializeChart, 150);
        });
    });
</script>
