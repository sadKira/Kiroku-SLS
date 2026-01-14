<div class=""> 

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">
        
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Dashboard</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    <div class="">
        {{-- Header with Set Academic Year Button --}}
        <div class="mt-6 mb-6 flex items-center justify-between">
            <flux:heading size="lg">Dashboard</flux:heading>

            <div class="flex-items-center gap-1">
                <flux:dropdown>
                    <flux:button 
                        icon="arrow-down-tray" 
                        variant="ghost" 
                        size="sm"
                    >
                        Export
                    </flux:button>

                    <flux:menu>
                        <flux:menu.item wire:click="openExportReportModal('monthly')">
                            Monthly Report
                        </flux:menu.item>
                        <flux:menu.item wire:click="openExportReportModal('semestral')">
                            Semestral Report
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>

                <flux:button 
                    icon="cog-6-tooth" 
                    wire:click="openSetSchoolYearModal" 
                    variant="ghost" 
                    size="sm"
                >
                    Set Academic Year
                </flux:button>
            </div>
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

                {{-- Charts Grid --}}
                <div class="grid lg:grid-cols-3 gap-6">

                    {{-- Yearly Activity Chart --}}
                    <x-ui.card class="!max-w-none grid lg:col-span-3">
                        <div class="flex flex-col w-full">
                            <div class="flex items-center justify-center mb-4">
                                <flux:heading>
                                    A.Y. {{ $activeSchoolYear }}
                                </flux:heading>
                            </div>

                            <div class="relative">
                                @if(collect($activityChartData)->sum('value') > 0)
                                    <div id="activityChart" wire:key="activity-chart-{{ $activeSchoolYear }}" class="w-full"></div>
                                @else
                                    {{-- Empty State --}}
                                    <div id="activityChart" wire:key="activity-chart-{{ $activeSchoolYear }}" class="w-full h-[100px]"></div>
                                    <div class="absolute bottom-15 inset-0 flex items-center justify-center pointer-events-none">
                                        <p class="text-sm text-[#71717a]">No activity data available</p>
                                    </div>
                                @endif
                            </div>
                            
                        </div>
                    </x-ui.card>
                </div>

                <div class="grid lg:grid-cols-2 gap-6">

                        {{-- This month Frequency --}}
                        <x-ui.card class="!max-w-none ">
                            <div class="flex flex-col h-full">
                                <flux:heading class="">
                                    This {{ \Carbon\Carbon::now('Asia/Manila')->format('F') }}
                                </flux:heading>

                                <div class="relative">
                                    @if(collect($monthlyDailyActivity)->sum(fn($item) => $item['y']) > 0)
                                        <div id="monthlyActivityChart" wire:key="monthly-activity-chart-{{ $activeSchoolYear }}" class="w-full flex-1 mt-5"></div>
                                    @else
                                        {{-- Empty State --}}
                                        <div class="flex items-center justify-center py-2">
                                            <p class="text-sm text-[#71717a] py-2">No data this month</p>
                                        </div>
                                    @endif
                                </div>
                                
                            </div>
                        </x-ui.card>

                        {{-- Today Frequency --}}
                        <x-ui.card class="!max-w-none ">
                            <div class="flex flex-col h-full">
                                <flux:heading class="">
                                    Today
                                </flux:heading>

                                <div class="relative">
                                    @if(collect($todayHourlyActivity)->sum(fn($item) => $item['y']) > 0)
                                        <div id="todayActivityChart" wire:key="today-activity-chart-{{ $activeSchoolYear }}" class="w-full flex-1 mt-5"></div>
                                    @else
                                        {{-- Empty State --}}
                                        <div class="flex items-center justify-center py-2">
                                            <p class="text-sm text-[#71717a] py-2">No activity today</p>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </x-ui.card>
                        

                </div>

                
            </div>

            {{-- Right Column: Today's Logs Table --}}
            <div class="lg:col-span-1">
                <x-ui.card class="!max-w-none">
                    <div class="flex flex-col">
                        <div class="flex items-center mb-2">
                            <flux:heading size="" class="">
                            <span class="relative flex items-center gap-2">
                                <span>{{ \Carbon\Carbon::now('Asia/Manila')->format('F j, Y') }}</span>
                                <span class="relative flex size-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex size-2 rounded-full bg-green-500"></span>
                                </span>
                            </span>
                            </flux:heading>
                        </div>
                        
                        @if($todayLogRecords->isEmpty())
                            <div class="flex justify-center items-center gap-3 py-12 max-h-[60vh]">
                                <flux:icon.book-text variant="mini" />
                                <flux:heading size="lg" class="">No Log Records Today</flux:heading>
                            </div>
                        @else
                            <div class="max-h-[60vh] overflow-y-auto
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

    {{-- Export Dashboard Report Modal --}}
    <flux:modal name="export-dashboard-report" :dismissible="false">
        <flux:heading size="lg" class="mb-4">Export Dashboard Report</flux:heading>
        
        <div class="space-y-6">
            {{-- Report Type (readonly, set by dropdown) --}}
            <div>
                <flux:heading size="sm" class="mb-2">Report Type</flux:heading>
                <flux:field>
                    <flux:input 
                        readonly 
                        type="text" 
                        label="Report Type" 
                        value="{{ $exportReportType === 'monthly' ? 'Monthly Report' : 'Semestral Report' }}"
                        variant="filled"
                        icon:trailing="lock-closed"
                    />
                </flux:field>
            </div>

            {{-- Month (only for monthly reports) --}}
            @if ($exportReportType === 'monthly')
                <div>
                    <flux:heading size="sm" class="mb-2">Month</flux:heading>
                    <flux:field>
                        <flux:select wire:model="exportMonth" label="Select Month" placeholder="Select Month">
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
                        <flux:error name="exportMonth" />
                    </flux:field>
                </div>
            @endif

            {{-- School Year --}}
            <div>
                <flux:heading size="sm" class="mb-2">Academic Year</flux:heading>
                <flux:field>
                    <flux:select wire:model="exportSchoolYear" label="Select Academic Year" placeholder="Select Academic Year">
                        @foreach ($availableAcademicYears as $academicYear)
                            <flux:select.option class="text-black dark:text-white" value="{{ $academicYear }}">{{ $academicYear }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="exportSchoolYear" />
                </flux:field>
            </div>

            {{-- Paper Size --}}
            <div>
                <flux:heading size="sm" class="mb-2">Paper Size</flux:heading>
                <flux:field>
                    <flux:select wire:model="exportPaperSize" label="Select Paper Size" placeholder="Select Paper Size">
                        <flux:select.option class="text-black dark:text-white" value="A4">A4</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="Letter">Letter</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="Legal">Legal</flux:select.option>
                    </flux:select>
                    <flux:error name="exportPaperSize" />
                </flux:field>
            </div>
        </div>

        <div class="flex gap-2 mt-6">
            <flux:spacer />
            <flux:button variant="ghost" size="sm" wire:click="resetExportForm">Cancel</flux:button>
            <flux:button wire:click="exportDashboardReport" variant="primary" size="sm">
                Export PDF
            </flux:button>
        </div>
    </flux:modal>
</div>
<script>
    let activityChart = null;
    let monthlyActivityChart = null;
    let todayActivityChart = null;

    function initializeCharts() {
        // Check if ApexCharts is loaded
        if (typeof ApexCharts === 'undefined') {
            console.warn('ApexCharts is not loaded');
            return;
        }

        // Yearly Activity Chart
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
                    height: 100,
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
                    padding: {
                        // left: -5,
                        // bottom: -10,
                        // right:-5,
                        top: -10,
                    },
                    borderColor: '#e4e4e7',
                    strokeDashArray: 0,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } }
                },
                xaxis: {
                    categories: activityData.map(item => item.label),
                    // floating: true,
                    labels: {
                        style: {
                            // colors: '#ffffff',
                            colors: '#71717a',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                },
                yaxis: {
                    floating: true,
                    labels: {
                        // offsetX: -15,
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

        // Monthly Daily Activity Chart (Time-Series)
        const monthlyActivityChartElement = document.querySelector("#monthlyActivityChart");
        if (monthlyActivityChartElement) {
            // Destroy existing chart if it exists
            if (monthlyActivityChart) {
                monthlyActivityChart.destroy();
            }

            const monthlyData = @json($monthlyDailyActivity);
            
            monthlyActivityChart = new ApexCharts(monthlyActivityChartElement, {
                series: [{
                    name: 'Logged Students',
                    data: monthlyData
                }],
                chart: {
                    type: 'area',
                    height: 20,
                    toolbar: { show: false },
                    fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                    sparkline: { enabled: true },
                    zoom: { enabled: false }
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
                    // padding: {
                    //     left: -5,
                    //     bottom: -5,
                    //     right:-5,
                    //     top: -5,
                    // },
                    borderColor: '#e4e4e7',
                    strokeDashArray: 0,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        style: {
                            colors: '#71717a',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        },
                        datetimeUTC: false
                    }
                },
                yaxis: {
                    floating: true,
                    labels: {
                        // offsetX: -15,
                        style: {
                            colors: '#ffffff',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                },
                tooltip: {
                    theme: 'light',
                    x: {
                        format: 'MMM dd, yyyy'
                    },
                    style: {
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                    }
                }
            });

            monthlyActivityChart.render();
        }

        // Today's Activity Chart (Sparkline - Hourly 8am-5pm)
        const todayActivityChartElement = document.querySelector("#todayActivityChart");
        if (todayActivityChartElement) {
            // Destroy existing chart if it exists
            if (todayActivityChart) {
                todayActivityChart.destroy();
            }

            const todayData = @json($todayHourlyActivity);
            
            todayActivityChart = new ApexCharts(todayActivityChartElement, {
                series: [{
                    name: 'Logged Students',
                    data: todayData
                }],
                chart: {
                    type: 'area',
                    height: 20,
                    toolbar: { show: false },
                    fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                    sparkline: { enabled: true },
                    zoom: { enabled: false }
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
                    yaxis: { lines: { show: false } }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        style: {
                            colors: '#71717a',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        },
                        datetimeUTC: false
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
                    x: {
                        format: 'h:mm TT'
                    },
                    style: {
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                    }
                }
            });

            todayActivityChart.render();
        }
    }

    // Initialize on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', initializeCharts);

    // Handle Livewire navigation events
    document.addEventListener('livewire:navigated', initializeCharts);
    document.addEventListener('livewire:navigating', () => {
        if (activityChart) {
            activityChart.destroy();
            activityChart = null;
        }
        if (monthlyActivityChart) {
            monthlyActivityChart.destroy();
            monthlyActivityChart = null;
        }
        if (todayActivityChart) {
            todayActivityChart.destroy();
            todayActivityChart = null;
        }
    });

    // Handle wire:navigate
    document.addEventListener('wire:navigated', initializeCharts);

    // Update charts when Livewire updates
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', ({ el, component }) => {
            // Check if charts need to be reinitialized
            const hasChartElements = el.querySelector('#activityChart') || el.querySelector('#monthlyActivityChart') || el.querySelector('#todayActivityChart');
            if (hasChartElements || el.id === 'activityChart' || el.id === 'monthlyActivityChart' || el.id === 'todayActivityChart') {
                setTimeout(initializeCharts, 150);
            }
        });
    }
    
    // Make initializeCharts globally accessible
    window.initializeCharts = initializeCharts;
    
    // Listen for Livewire property updates and school year changes
    document.addEventListener('livewire:init', () => {
        Livewire.on('$refresh', () => {
            setTimeout(initializeCharts, 150);
        });
        
        // Listen for school year change event
        Livewire.on('school-year-changed', () => {
            setTimeout(() => {
                initializeCharts();
            }, 200);
        });
    });
    
    // Watch for wire:key changes on chart elements (when activeSchoolYear changes)
    document.addEventListener('DOMContentLoaded', () => {
        const checkAndObserve = () => {
            const activityChartEl = document.querySelector('#activityChart');
            const monthlyChartEl = document.querySelector('#monthlyActivityChart');
            const todayChartEl = document.querySelector('#todayActivityChart');
            
            if (activityChartEl || monthlyChartEl || todayChartEl) {
                const observer = new MutationObserver((mutations) => {
                    let shouldReload = false;
                    mutations.forEach((mutation) => {
                        if (mutation.type === 'attributes' && 
                            (mutation.attributeName === 'wire:key' || mutation.attributeName === 'data-wire-key')) {
                            shouldReload = true;
                        }
                    });
                    if (shouldReload) {
                        setTimeout(initializeCharts, 200);
                    }
                });
                
                [activityChartEl, monthlyChartEl, todayChartEl].forEach((el) => {
                    if (el) {
                        observer.observe(el, { 
                            attributes: true, 
                            attributeFilter: ['wire:key', 'data-wire-key'] 
                        });
                    }
                });
            }
        };
        
        checkAndObserve();
        // Re-check after a short delay in case elements aren't ready yet
        setTimeout(checkAndObserve, 500);
    });
</script>
