<div>
    {{-- App Header --}}
    <x-management.profile-section />

    {{-- Metric Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Total Students Card --}}
        <x-ui.card class="!max-w-none">
            <div class="flex flex-col">
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">Total Students</p>
                <p class="text-3xl font-semibold text-neutral-800 dark:text-white">1,245</p>
            </div>
        </x-ui.card>

        {{-- Total Log Sessions Card --}}
        <x-ui.card class="!max-w-none">
            <div class="flex flex-col">
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">Total Log Sessions (This Month)</p>
                <p class="text-3xl font-semibold text-neutral-800 dark:text-white">8,392</p>
            </div>
        </x-ui.card>

        {{-- Active Students Today - Highlight Card --}}
        <x-ui.card class="!max-w-none bg-black dark:bg-black border-black/20 dark:border-white/20">
            <div class="flex flex-col">
                <p class="text-sm text-white/80 mb-1">Active Students Today</p>
                <p class="text-3xl font-semibold text-white">342</p>
            </div>
        </x-ui.card>
    </div>

    {{-- Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Monthly Log Sessions Trend Chart --}}
        <x-ui.card class="!max-w-none">
            <div class="flex flex-col">
                <flux:heading size="md" class="mb-4">Monthly Log Sessions Trend</flux:heading>
                <div id="monthlyLogSessionsChart" class="w-full"></div>
            </div>
        </x-ui.card>

        {{-- Daily Student Activity Chart --}}
        <x-ui.card class="!max-w-none">
            <div class="flex flex-col">
                <flux:heading size="md" class="mb-4">Daily Student Activity</flux:heading>
                <div id="dailyStudentActivityChart" class="w-full"></div>
            </div>
        </x-ui.card>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if ApexCharts is loaded
        if (typeof ApexCharts === 'undefined') {
            console.warn('ApexCharts is not loaded');
            return;
        }

        // Monthly Log Sessions Trend Chart
        const monthlyChartElement = document.querySelector("#monthlyLogSessionsChart");
        if (monthlyChartElement && !monthlyChartElement.hasAttribute('data-chart-initialized')) {
            const monthlyOptions = {
                series: [{
                    name: 'Log Sessions',
                    data: [120, 145, 132, 168, 189, 203, 198, 215, 228, 240, 235, 250]
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                },
                colors: ['#52525b'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.3,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                grid: {
                    show: true,
                    borderColor: '#e4e4e7',
                    strokeDashArray: 0,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    labels: {
                        style: {
                            colors: '#71717a',
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#71717a',
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
            };

            const monthlyChart = new ApexCharts(monthlyChartElement, monthlyOptions);
            monthlyChart.render();
            monthlyChartElement.setAttribute('data-chart-initialized', 'true');
        }

        // Daily Student Activity Chart
        const dailyChartElement = document.querySelector("#dailyStudentActivityChart");
        if (dailyChartElement && !dailyChartElement.hasAttribute('data-chart-initialized')) {
            const dailyOptions = {
                series: [{
                    name: 'Active Students',
                    data: [285, 312, 298, 345, 328, 342, 356, 340, 365, 378, 342, 390, 375, 388, 402, 395, 410, 398, 415, 405, 420, 412, 428, 435, 430, 445, 438, 450, 442, 456]
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                },
                colors: ['#52525b'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '60%',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    show: true,
                    borderColor: '#e4e4e7',
                    strokeDashArray: 0,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                xaxis: {
                    categories: Array.from({length: 30}, (_, i) => i + 1),
                    labels: {
                        style: {
                            colors: '#71717a',
                            fontSize: '11px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        },
                        rotate: -45,
                        rotateAlways: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#71717a',
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
            };

            const dailyChart = new ApexCharts(dailyChartElement, dailyOptions);
            dailyChart.render();
            dailyChartElement.setAttribute('data-chart-initialized', 'true');
        }
    });
</script>
