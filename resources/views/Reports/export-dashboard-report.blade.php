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

    {{-- Avoid splitting content across PDF pages --}}
    <style>
        @media print {
            .page-break {
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
            font-size: 14px;
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
            <flux:heading size="lg" class="mt-2">{{ $month }} {{ Carbon\Carbon::parse($dateRange['start'])->format('Y') }}</flux:heading>
            <p class="text-gray-600 mt-1">
                Academic Year: {{ $schoolYear }} | 
                Period: {{ \Carbon\Carbon::parse($dateRange['start'])->format('F j') }} - 
                {{ \Carbon\Carbon::parse($dateRange['end'])->format('F j, Y') }}
            </p>
        </div>

        {{-- Monthly Statistics --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Monthly Overview</flux:heading>
            <div class="grid grid-cols-3 gap-4">
                <div class="stat-box">
                    <div class="stat-label">Total Logs</div>
                    <div class="stat-value">{{ number_format($stats['totalLogs']) }}</div>
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

        {{-- Daily Activity Table --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Daily Activity Breakdown</flux:heading>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day of Week</th>
                        <th style="text-align: right;">Log Records</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dailyData = collect($stats['dailyActivity'])
                            ->filter(fn($item) => $item['value'] > 0);
                    @endphp
                    @if ($dailyData->isEmpty())
                        <tr>
                            <td colspan="3" class="text-sm text-gray-400 text-center py-4">No activity recorded for this month</td>
                        </tr>
                    @else
                        @foreach ($dailyData as $day)
                            <tr>
                                <td class="text-sm">{{ $day['label'] }}</td>
                                <td class="text-sm text-gray-500">{{ $day['day'] }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($day['value']) }}</td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold" colspan="2">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($dailyData->sum('value')) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Course Distribution Table --}}
        @if (count($stats['courseDistribution']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Course Distribution</flux:heading>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th style="text-align: right;">Log Records</th>
                            <th style="text-align: right;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalForPercentage = array_sum($stats['courseDistribution']);
                            $courseAbbreviations = [
                                'Bachelor of Arts in International Studies' => 'ABIS',
                                'Bachelor of Science in Information Systems' => 'BSIS',
                                'Bachelor of Human Services' => 'BHS',
                                'Bachelor of Secondary Education' => 'BSED',
                                'Bachelor of Elementary Education' => 'ECED',
                                'Bachelor of Special Needs Education' => 'SNED'
                            ];
                        @endphp
                        @foreach ($stats['courseDistribution'] as $course => $count)
                            <tr>
                                <td class="text-sm">{{ $courseAbbreviations[$course] ?? $course }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $totalForPercentage > 0 ? number_format(($count / $totalForPercentage) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($totalForPercentage) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

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
                    <div class="stat-label">Log Sessions</div>
                    <div class="stat-value">{{ number_format($stats['logSessionsCount']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Avg per Session</div>
                    <div class="stat-value">
                        {{ $stats['logSessionsCount'] > 0 ? number_format($stats['totalLogs'] / $stats['logSessionsCount'], 1) : '0' }}
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Students</div>
                    <div class="stat-value">{{ number_format($stats['totalStudents']) }}</div>
                </div>
            </div>
        </div>

        {{-- Monthly Activity Table --}}
        @if (count($stats['monthlyActivity']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Monthly Activity Breakdown</flux:heading>
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
                        @foreach ($stats['monthlyActivity'] as $month)
                            <tr>
                                <td class="text-sm font-medium">{{ $month['label'] }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($month['value']) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $totalForPercentage > 0 ? number_format(($month['value'] / $totalForPercentage) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($totalForPercentage) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Course Distribution Table --}}
        @if (count($stats['courseDistribution']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Course Distribution</flux:heading>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th style="text-align: right;">Log Records</th>
                            <th style="text-align: right;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalForPercentage = array_sum($stats['courseDistribution']);
                            $courseAbbreviations = [
                                'Bachelor of Arts in International Studies' => 'ABIS',
                                'Bachelor of Science in Information Systems' => 'BSIS',
                                'Bachelor of Human Services' => 'BHS',
                                'Bachelor of Secondary Education' => 'BSED',
                                'Bachelor of Elementary Education' => 'ECED',
                                'Bachelor of Special Needs Education' => 'SNED'
                            ];
                        @endphp
                        @foreach ($stats['courseDistribution'] as $course => $count)
                            <tr>
                                <td class="text-sm">{{ $courseAbbreviations[$course] ?? $course }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $totalForPercentage > 0 ? number_format(($count / $totalForPercentage) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($totalForPercentage) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

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
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F j, Y g:i a') }}</p>
        <p>Kiroku Student Logging System 2025</p>
    </div>

</body>

</html>