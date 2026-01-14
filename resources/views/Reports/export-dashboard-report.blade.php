<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Report - {{ $reportType === 'monthly' ? $month : 'Semestral' }} {{ $schoolYear }}</title>

    <link rel="icon" href="/mkdlib-logo.ico" sizes="any">
    <link rel="icon" href="/mkdlib-logo.svg" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Avoid splitting content across PDF pages --}}
    <style>
        @media print {
            .page-break {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        .stat-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table th,
        .summary-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-table th {
            background-color: #f9fafb;
            font-weight: 600;
            font-size: 16px;
            color: #374151;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased p-4">
    <div class="mb-6">
        <flux:heading size="xl"><span class="font-bold">MKD Learning Resource Center</span></flux:heading>
        <flux:heading size="xl" class="mt-3">
            <span class="font-bold">
                {{ $reportType === 'monthly' ? 'Monthly' : 'Semestral' }} Dashboard Report
            </span>
        </flux:heading>
        @if ($reportType === 'monthly')
            <flux:heading size="lg" class="mt-2">{{ $month }} {{ explode('-', $schoolYear)[0] }}</flux:heading>
        @else
            <flux:heading size="lg" class="mt-2">Academic Year {{ $schoolYear }}</flux:heading>
        @endif
        <p class="text-gray-600 mt-1">
            Period: {{ \Carbon\Carbon::parse($dateRange['start'])->format('F j, Y') }} - 
            {{ \Carbon\Carbon::parse($dateRange['end'])->format('F j, Y') }}
        </p>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 page-break">
        <div class="stat-card">
            <div class="stat-label">Total Log Records</div>
            <div class="stat-value">{{ number_format($stats['totalLogs']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Unique Students</div>
            <div class="stat-value">{{ number_format($stats['uniqueStudents']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Log Sessions</div>
            <div class="stat-value">{{ number_format($stats['logSessionsCount']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ number_format($stats['totalStudents']) }}</div>
        </div>
    </div>

    {{-- Daily Activity Chart (for monthly reports) --}}
    @if ($reportType === 'monthly' && count($stats['dailyActivity']) > 0)
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Daily Activity</flux:heading>
            <div id="dailyActivityChart" style="height: 300px;"></div>
        </div>
    @endif

    {{-- Monthly Activity Chart (for semestral reports) --}}
    @if ($reportType === 'semestral' && count($stats['monthlyActivity']) > 0)
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Monthly Activity</flux:heading>
            <div id="monthlyActivityChart" style="height: 300px;"></div>
        </div>
    @endif

    {{-- Course Distribution Chart --}}
    @if (count($stats['courseDistribution']) > 0)
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Course Distribution</flux:heading>
            <div id="courseDistributionChart" style="height: 300px;"></div>
        </div>
    @endif

    {{-- Summary Table --}}
    <div class="mt-6 page-break">
        <flux:heading size="lg" class="mb-4">Summary</flux:heading>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th style="text-align: right;">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-sm">Total Log Records</td>
                    <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['totalLogs']) }}</td>
                </tr>
                <tr>
                    <td class="text-sm">Unique Students</td>
                    <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['uniqueStudents']) }}</td>
                </tr>
                <tr>
                    <td class="text-sm">Total Students</td>
                    <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['totalStudents']) }}</td>
                </tr>
                <tr>
                    <td class="text-sm">Log Sessions</td>
                    <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['logSessionsCount']) }}</td>
                </tr>
                <tr>
                    <td class="text-sm">Average Logs per Session</td>
                    <td class="text-sm font-medium" style="text-align: right;">
                        {{ $stats['logSessionsCount'] > 0 ? number_format($stats['totalLogs'] / $stats['logSessionsCount'], 1) : '0' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F j, Y g:i a') }}</p>
        <p>Kiroku Student Logging System 2025</p>
    </div>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Activity Chart (Monthly Reports)
            @if ($reportType === 'monthly' && count($stats['dailyActivity']) > 0)
                const dailyData = @json($stats['dailyActivity']);
                const dailyChart = new ApexCharts(document.querySelector("#dailyActivityChart"), {
                    series: [{
                        name: 'Log Records',
                        data: dailyData.map(item => item.value)
                    }],
                    chart: {
                        type: 'column',
                        height: 300,
                        toolbar: { show: false },
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                    },
                    colors: ['#52525b'],
                    plotOptions: {
                        bar: {
                            columnWidth: '60%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                            fontWeight: 500,
                            colors: ['#111827']
                        }
                    },
                    xaxis: {
                        categories: dailyData.map(item => item.label),
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
                    grid: {
                        borderColor: '#e4e4e7',
                        strokeDashArray: 0
                    },
                    tooltip: {
                        theme: 'light',
                        style: {
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                });
                dailyChart.render();
            @endif

            // Monthly Activity Chart (Semestral Reports)
            @if ($reportType === 'semestral' && count($stats['monthlyActivity']) > 0)
                const monthlyData = @json($stats['monthlyActivity']);
                const monthlyChart = new ApexCharts(document.querySelector("#monthlyActivityChart"), {
                    series: [{
                        name: 'Log Records',
                        data: monthlyData.map(item => item.value)
                    }],
                    chart: {
                        type: 'column',
                        height: 300,
                        toolbar: { show: false },
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                    },
                    colors: ['#52525b'],
                    plotOptions: {
                        bar: {
                            columnWidth: '60%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                            fontWeight: 500,
                            colors: ['#111827']
                        }
                    },
                    xaxis: {
                        categories: monthlyData.map(item => item.label),
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
                    grid: {
                        borderColor: '#e4e4e7',
                        strokeDashArray: 0
                    },
                    tooltip: {
                        theme: 'light',
                        style: {
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                });
                monthlyChart.render();
            @endif

            // Course Distribution Chart
            @if (count($stats['courseDistribution']) > 0)
                const courseData = @json($stats['courseDistribution']);
                const courseLabels = Object.keys(courseData).map(course => {
                    const abbreviations = {
                        'Bachelor of Arts in International Studies': 'ABIS',
                        'Bachelor of Science in Information Systems': 'BSIS',
                        'Bachelor of Human Services': 'BHS',
                        'Bachelor of Secondary Education': 'BSED',
                        'Bachelor of Elementary Education': 'ECED',
                        'Bachelor of Special Needs Education': 'SNED'
                    };
                    return abbreviations[course] || course;
                });
                const courseValues = Object.values(courseData);
                
                const courseChart = new ApexCharts(document.querySelector("#courseDistributionChart"), {
                    series: [{
                        name: 'Log Records',
                        data: courseValues
                    }],
                    chart: {
                        type: 'column',
                        height: 300,
                        toolbar: { show: false },
                        fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                    },
                    colors: ['#52525b'],
                    plotOptions: {
                        bar: {
                            columnWidth: '60%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif',
                            fontWeight: 500,
                            colors: ['#111827']
                        }
                    },
                    xaxis: {
                        categories: courseLabels,
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
                    grid: {
                        borderColor: '#e4e4e7',
                        strokeDashArray: 0
                    },
                    tooltip: {
                        theme: 'light',
                        style: {
                            fontFamily: 'Instrument Sans, ui-sans-serif, system-ui, sans-serif'
                        }
                    }
                });
                courseChart.render();
            @endif
        });
    </script>
</body>

</html>
