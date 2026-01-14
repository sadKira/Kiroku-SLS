<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $reportType === 'monthly' ? 'Monthly' : 'Semestral' }} Report - {{ $reportType === 'monthly' ? $month : $schoolYear }}</title>

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
            .log-record {
                break-inside: avoid;
                page-break-inside: avoid;
            }
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

        .stat-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased p-4">
    
    @if ($reportType === 'monthly')
        {{-- MONTHLY REPORT DESIGN --}}
        <div class="mb-6">
            <flux:heading size="xl"><span class="font-bold">MKD Learning Resource Center</span></flux:heading>
            <flux:heading size="xl" class="mt-3"><span class="font-bold">Monthly Activity Report</span></flux:heading>
            <flux:heading size="lg" class="mt-2">{{ $month }} {{ explode('-', $schoolYear)[0] }}</flux:heading>
            <p class="text-gray-600 mt-1">
                Academic Year: {{ $schoolYear }} | 
                Period: {{ \Carbon\Carbon::parse($dateRange['start'])->format('F j') }} - 
                {{ \Carbon\Carbon::parse($dateRange['end'])->format('F j, Y') }}
            </p>
        </div>

        {{-- Monthly Statistics --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Monthly Overview</flux:heading>
            <div class="grid grid-cols-4 gap-4">
                <div class="stat-box">
                    <div class="stat-label">Total Logs</div>
                    <div class="stat-value">{{ number_format($stats['totalLogs']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Active Students</div>
                    <div class="stat-value">{{ number_format($stats['uniqueStudents']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Log Sessions</div>
                    <div class="stat-value">{{ number_format($stats['logSessionsCount']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Avg per Session</div>
                    <div class="stat-value">
                        {{ $stats['logSessionsCount'] > 0 ? number_format($stats['totalLogs'] / $stats['logSessionsCount'], 1) : '0' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Daily Activity Chart --}}
        @if (count($stats['dailyActivity']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Daily Activity Breakdown</flux:heading>
                <div id="dailyActivityChart" style="height: 350px;"></div>
            </div>
        @endif

        {{-- Course Distribution --}}
        @if (count($stats['courseDistribution']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Course Distribution</flux:heading>
                <div id="courseDistributionChart" style="height: 300px;"></div>
            </div>
        @endif

        {{-- Daily Summary Table --}}
        <div class="mt-6 page-break">
            <flux:heading size="lg" class="mb-4">Daily Summary</flux:heading>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th style="text-align: right;">Log Records</th>
                        <th style="text-align: right;">Day of Week</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dailySummary = collect($stats['dailyActivity'])
                            ->map(function($item) {
                                $date = \Carbon\Carbon::parse($item['date']);
                                return [
                                    'date' => $date->format('F j, Y'),
                                    'day' => $date->format('l'),
                                    'count' => $item['value']
                                ];
                            })
                            ->filter(fn($item) => $item['count'] > 0)
                            ->sortByDesc('count')
                            ->take(10);
                    @endphp
                    @if ($dailySummary->isEmpty())
                        <tr>
                            <td colspan="3" class="text-sm text-gray-400 text-center py-4">No activity recorded for this month</td>
                        </tr>
                    @else
                        @foreach ($dailySummary as $day)
                            <tr>
                                <td class="text-sm">{{ $day['date'] }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($day['count']) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">{{ $day['day'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

    @else
        {{-- SEMESTRAL REPORT DESIGN --}}
        <div class="mb-6">
            <flux:heading size="xl"><span class="font-bold">MKD Learning Resource Center</span></flux:heading>
            <flux:heading size="xl" class="mt-3"><span class="font-bold">Semestral Activity Report</span></flux:heading>
            <flux:heading size="lg" class="mt-2">Academic Year {{ $schoolYear }}</flux:heading>
            <p class="text-gray-600 mt-1">
                Period: {{ \Carbon\Carbon::parse($dateRange['start'])->format('F j, Y') }} - 
                {{ \Carbon\Carbon::parse($dateRange['end'])->format('F j, Y') }}
            </p>
        </div>

        {{-- Semestral Statistics --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Semester Overview</flux:heading>
            <div class="grid grid-cols-4 gap-4 mb-4">
                <div class="stat-box">
                    <div class="stat-label">Total Log Records</div>
                    <div class="stat-value">{{ number_format($stats['totalLogs']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Unique Students</div>
                    <div class="stat-value">{{ number_format($stats['uniqueStudents']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Log Sessions</div>
                    <div class="stat-value">{{ number_format($stats['logSessionsCount']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value">{{ number_format($stats['totalStudents']) }}</div>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="stat-box">
                    <div class="stat-label">Avg Logs per Session</div>
                    <div class="stat-value">
                        {{ $stats['logSessionsCount'] > 0 ? number_format($stats['totalLogs'] / $stats['logSessionsCount'], 1) : '0' }}
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Student Participation</div>
                    <div class="stat-value">
                        {{ $stats['totalStudents'] > 0 ? number_format(($stats['uniqueStudents'] / $stats['totalStudents']) * 100, 1) : '0' }}%
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Avg Logs per Student</div>
                    <div class="stat-value">
                        {{ $stats['uniqueStudents'] > 0 ? number_format($stats['totalLogs'] / $stats['uniqueStudents'], 1) : '0' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Activity Chart --}}
        @if (count($stats['monthlyActivity']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Monthly Activity Trends</flux:heading>
                <div id="monthlyActivityChart" style="height: 350px;"></div>
            </div>
        @endif

        {{-- Course Distribution --}}
        @if (count($stats['courseDistribution']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Course Distribution</flux:heading>
                <div id="courseDistributionChart" style="height: 300px;"></div>
            </div>
        @endif

        {{-- Monthly Summary Table --}}
        <div class="mt-6 page-break">
            <flux:heading size="lg" class="mb-4">Monthly Summary</flux:heading>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th style="text-align: right;">Log Records</th>
                        <th style="text-align: right;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalForPercentage = collect($stats['monthlyActivity'])->sum('value');
                    @endphp
                    @if (count($stats['monthlyActivity']) === 0)
                        <tr>
                            <td colspan="3" class="text-sm text-gray-400 text-center py-4">No activity recorded for this semester</td>
                        </tr>
                    @else
                        @foreach ($stats['monthlyActivity'] as $month)
                            <tr>
                                <td class="text-sm font-medium">{{ $month['label'] }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($month['value']) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $totalForPercentage > 0 ? number_format(($month['value'] / $totalForPercentage) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Detailed Summary --}}
        <div class="mt-6 page-break">
            <flux:heading size="lg" class="mb-4">Detailed Statistics</flux:heading>
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
                        <td class="text-sm">Total Students (All Time)</td>
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
                    <tr>
                        <td class="text-sm">Average Logs per Student</td>
                        <td class="text-sm font-medium" style="text-align: right;">
                            {{ $stats['uniqueStudents'] > 0 ? number_format($stats['totalLogs'] / $stats['uniqueStudents'], 1) : '0' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-sm">Student Participation Rate</td>
                        <td class="text-sm font-medium" style="text-align: right;">
                            {{ $stats['totalStudents'] > 0 ? number_format(($stats['uniqueStudents'] / $stats['totalStudents']) * 100, 1) : '0' }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F j, Y g:i a') }}</p>
        <p>Kiroku Student Logging System 2025</p>
    </div>

    <script>
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            @if ($reportType === 'monthly')
                // Daily Activity Chart (Monthly Reports)
                @if (count($stats['dailyActivity']) > 0)
                    const dailyData = @json($stats['dailyActivity']);
                    const dailyChart = new ApexCharts(document.querySelector("#dailyActivityChart"), {
                        series: [{
                            name: 'Log Records',
                            data: dailyData.map(item => item.value)
                        }],
                        chart: {
                            type: 'column',
                            height: 350,
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
            @else
                // Monthly Activity Chart (Semestral Reports)
                @if (count($stats['monthlyActivity']) > 0)
                    const monthlyData = @json($stats['monthlyActivity']);
                    const monthlyChart = new ApexCharts(document.querySelector("#monthlyActivityChart"), {
                        series: [{
                            name: 'Log Records',
                            data: monthlyData.map(item => item.value)
                        }],
                        chart: {
                            type: 'column',
                            height: 350,
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
            @endif

            // Course Distribution Chart (Both Report Types)
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
