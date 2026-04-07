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

        /* Legend badges (no color) */
        .legend-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 12px;
            color: #374151;
        }

        .legend-count {
            font-weight: 600;
        }

        /* Code → full-name reference key */
        .legend-key {
            margin-bottom: 12px;
        }

        .legend-key-title {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .legend-key-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 4px 16px;
        }

        .legend-key-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            font-size: 12px;
            color: #374151;
        }

        .legend-key-code {
            font-weight: 600;
            flex-shrink: 0;
        }

        .legend-key-sep {
            color: #9ca3af;
            flex-shrink: 0;
        }

        .legend-key-name {
            color: #6b7280;
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased p-4">

    @if ($reportType === 'monthly')
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- MONTHLY REPORT                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="mb-6">
            <flux:heading size="xl"><span class="font-bold">MKD Learning Resource Center</span></flux:heading>
            <flux:heading size="xl" class="mt-3"><span class="font-bold">Monthly Activity Report</span>: {{ $month }} {{ Carbon\Carbon::parse($dateRange['start'])->format('Y') }}</flux:heading>
            <p class="text-gray-600 mt-1">
                Academic Year: {{ $schoolYear }} |
                Period: {{ \Carbon\Carbon::parse($dateRange['start'])->format('F j') }} –
                {{ \Carbon\Carbon::parse($dateRange['end'])->format('F j, Y') }}
            </p>
        </div>

        {{-- Unified Abbreviations Legend --}}
        @php
            $courseAbbreviations = \App\Models\Course::pluck('code', 'name')->toArray();
            $strandAbbreviations = \App\Models\Strand::pluck('code', 'name')->toArray();
            $hasCourses = count($stats['courseDistribution']) > 0;
            $hasStrands = count($stats['strandDistribution']) > 0;
        @endphp

        @if ($hasCourses || $hasStrands)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Legend</flux:heading>
                <div class="p-3 bg-gray-50 rounded border border-gray-200">
                    @if ($hasCourses)
                        <p class="text-xs font-semibold text-gray-700 mb-2">Course Abbreviations:</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600 mb-3">
                            @foreach (\App\Models\Course::orderBy('code')->get() as $c)
                                <div><span class="font-medium">{{ $c->code }}</span> - {{ $c->name }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if ($hasStrands)
                        <p class="text-xs font-semibold text-gray-700 mb-2">Strand Abbreviations:</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                            @foreach (\App\Models\Strand::orderBy('code')->get() as $s)
                                <div><span class="font-medium">{{ $s->code }}</span> - {{ $s->name }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Monthly Overview --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Monthly Overview</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div class="stat-box">
                    <div class="stat-label">User Logs</div>
                    <div class="stat-value">{{ number_format($stats['totalUserLogs']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Active Rate</div>
                    <div class="stat-value">{{ $stats['monthlyActiveRate'] }}%</div>
                    <div class="stat-label" style="margin-top: 4px;">
                        {{ number_format($stats['uniqueUsers']) }} of {{ number_format($stats['totalUsers']) }} users
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Statistics (Monthly) --}}
        <div class="mb-6 page-break">
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
                        <td class="text-sm">Log Sessions</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['logSessionsCount']) }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Avg Logs per Session</td>
                        <td class="text-sm font-medium" style="text-align: right;">
                            {{ $stats['logSessionsCount'] > 0 ? number_format($stats['totalUserLogs'] / $stats['logSessionsCount'], 1) : '0' }}
                        </td>
                    </tr>
                    @if($stats['mostActiveDay'])
                    <tr>
                        <td class="text-sm">Most Active Day</td>
                        <td class="text-sm font-medium" style="text-align: right;">
                            {{ $stats['mostActiveDay']['label'] }} ({{ $stats['mostActiveDayPercent'] }}%)
                        </td>
                    </tr>
                    @endif
                    @if($stats['leastActiveDay'])
                    <tr>
                        <td class="text-sm">Least Active Day</td>
                        <td class="text-sm text-gray-500" style="text-align: right;">
                            {{ $stats['leastActiveDay']['label'] }} ({{ $stats['leastActiveDayPercent'] }}%)
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-sm">Most Active Course</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ $stats['courseMinMax']['max'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Least Active Course</td>
                        <td class="text-sm text-gray-500" style="text-align: right;">{{ $stats['courseMinMax']['min'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Most Active Strand</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ $stats['strandMinMax']['max'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Least Active Strand</td>
                        <td class="text-sm text-gray-500" style="text-align: right;">{{ $stats['strandMinMax']['min'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Most Active Faculty Level</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ $stats['facultyMinMax']['max'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Daily Activity Table --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Daily Activity Breakdown</flux:heading>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day of Week</th>
                        <th style="text-align: right;">User Logs</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dailyData = collect($stats['dailyActivity'])->filter(fn($item) => $item['value'] > 0);
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

        {{-- Course Distribution --}}
        @if ($hasCourses)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-2">Course Distribution</flux:heading>

                @php $courseTotal = array_sum($stats['courseDistribution']); @endphp


                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Course Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['courseDistribution'] as $course => $count)
                            <tr>
                                <td class="text-sm">{{ $courseAbbreviations[$course] ?? $course }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $courseTotal > 0 ? number_format(($count / $courseTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($courseTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Strand Distribution --}}
        @if ($hasStrands)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-2">Strand Distribution</flux:heading>

                @php $strandTotal = array_sum($stats['strandDistribution']); @endphp


                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Strand</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Strand Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['strandDistribution'] as $strand => $count)
                            <tr>
                                <td class="text-sm">{{ $strandAbbreviations[$strand] ?? $strand }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $strandTotal > 0 ? number_format(($count / $strandTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($strandTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Faculty Distribution --}}
        @if (count($stats['facultyDistribution']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-2">Faculty Distribution</flux:heading>

                @php $facultyTotal = array_sum($stats['facultyDistribution']); @endphp


                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Instructional Level</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Faculty Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['facultyDistribution'] as $level => $count)
                            <tr>
                                <td class="text-sm">{{ $level }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $facultyTotal > 0 ? number_format(($count / $facultyTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($facultyTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

    @else
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- SEMESTRAL REPORT                                                --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div class="mb-6">
            <flux:heading size="xl"><span class="font-bold">MKD Learning Resource Center</span></flux:heading>
            <flux:heading size="xl" class="mt-3"><span class="font-bold">Semestral Activity Report</span>: A.Y. {{ $schoolYear }}</flux:heading>
            <p class="text-gray-600 mt-1">
                Period: {{ \Carbon\Carbon::parse($dateRange['start'])->format('F j, Y') }} –
                {{ \Carbon\Carbon::parse($dateRange['end'])->format('F j, Y') }}
            </p>
        </div>

        {{-- Unified Abbreviations Legend --}}
        @php
            $courseAbbreviations = \App\Models\Course::pluck('code', 'name')->toArray();
            $strandAbbreviations = \App\Models\Strand::pluck('code', 'name')->toArray();
            $hasCourses = count($stats['courseDistribution']) > 0;
            $hasStrands = count($stats['strandDistribution']) > 0;
        @endphp

        @if ($hasCourses || $hasStrands)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Legend</flux:heading>
                <div class="p-3 bg-gray-50 rounded border border-gray-200">
                    @if ($hasCourses)
                        <p class="text-xs font-semibold text-gray-700 mb-2">Course Abbreviations:</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600 mb-3">
                            @foreach (\App\Models\Course::orderBy('code')->get() as $c)
                                <div><span class="font-medium">{{ $c->code }}</span> - {{ $c->name }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if ($hasStrands)
                        <p class="text-xs font-semibold text-gray-700 mb-2">Strand Abbreviations:</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                            @foreach (\App\Models\Strand::orderBy('code')->get() as $s)
                                <div><span class="font-medium">{{ $s->code }}</span> - {{ $s->name }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Semester Overview (2 cards only) --}}
        <div class="mb-6 page-break">
            <flux:heading size="lg" class="mb-4">Semester Overview</flux:heading>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="stat-box">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{ number_format($stats['totalUsers']) }}</div>
                    <div class="stat-label" style="margin-top: 4px;">
                        College {{ number_format($stats['totalCollege']) }} &middot;
                        SHS {{ number_format($stats['totalShs']) }} &middot;
                        Faculty {{ number_format($stats['totalFaculty']) }}
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Library Active Rate</div>
                    <div class="stat-value">{{ $stats['libraryActiveRate'] }}%</div>
                    <div class="stat-label" style="margin-top: 4px;">
                        {{ number_format($stats['uniqueUsers']) }} of {{ number_format($stats['totalUsers']) }} users logged
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Statistics (Semestral) --}}
        <div class="mb-6 page-break">
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
                        <td class="text-sm">Total Logs</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['totalUserLogs']) }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Log Sessions</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ number_format($stats['logSessionsCount']) }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Avg Logs per Session</td>
                        <td class="text-sm font-medium" style="text-align: right;">
                            {{ $stats['logSessionsCount'] > 0 ? number_format($stats['totalUserLogs'] / $stats['logSessionsCount'], 1) : '0' }}
                        </td>
                    </tr>
                    @if($stats['mostActiveMonth'])
                    <tr>
                        <td class="text-sm">Most Active Month</td>
                        <td class="text-sm font-medium" style="text-align: right;">
                            {{ $stats['mostActiveMonth']['label'] }} ({{ $stats['mostActiveMonthPercent'] }}%)
                        </td>
                    </tr>
                    @endif
                    @if($stats['leastActiveMonth'])
                    <tr>
                        <td class="text-sm">Least Active Month</td>
                        <td class="text-sm text-gray-500" style="text-align: right;">
                            {{ $stats['leastActiveMonth']['label'] }} ({{ $stats['leastActiveMonthPercent'] }}%)
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-sm">Most Active Course</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ $stats['courseMinMax']['max'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Least Active Course</td>
                        <td class="text-sm text-gray-500" style="text-align: right;">{{ $stats['courseMinMax']['min'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Most Active Strand</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ $stats['strandMinMax']['max'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Least Active Strand</td>
                        <td class="text-sm text-gray-500" style="text-align: right;">{{ $stats['strandMinMax']['min'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-sm">Most Active Faculty Level</td>
                        <td class="text-sm font-medium" style="text-align: right;">{{ $stats['facultyMinMax']['max'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Monthly Activity Breakdown --}}
        @if (count($stats['monthlyActivity']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-4">Monthly Activity Breakdown</flux:heading>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Period Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $monthlyTotal = collect($stats['monthlyActivity'])->sum('value'); @endphp
                        @foreach ($stats['monthlyActivity'] as $month)
                            <tr>
                                <td class="text-sm font-medium">{{ $month['label'] }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($month['value']) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $monthlyTotal > 0 ? number_format(($month['value'] / $monthlyTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($monthlyTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif


        {{-- Course Distribution --}}
        @if ($hasCourses)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-2">Course Distribution</flux:heading>

                @php $courseTotal = array_sum($stats['courseDistribution']); @endphp


                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Course Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['courseDistribution'] as $course => $count)
                            <tr>
                                <td class="text-sm">{{ $courseAbbreviations[$course] ?? $course }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $courseTotal > 0 ? number_format(($count / $courseTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($courseTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Strand Distribution --}}
        @if ($hasStrands)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-2">Strand Distribution</flux:heading>

                @php $strandTotal = array_sum($stats['strandDistribution']); @endphp


                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Strand</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Strand Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['strandDistribution'] as $strand => $count)
                            <tr>
                                <td class="text-sm">{{ $strandAbbreviations[$strand] ?? $strand }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $strandTotal > 0 ? number_format(($count / $strandTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($strandTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Faculty Distribution --}}
        @if (count($stats['facultyDistribution']) > 0)
            <div class="mb-6 page-break">
                <flux:heading size="lg" class="mb-2">Faculty Distribution</flux:heading>

                @php $facultyTotal = array_sum($stats['facultyDistribution']); @endphp


                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Instructional Level</th>
                            <th style="text-align: right;">User Logs</th>
                            <th style="text-align: right;">% of Faculty Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stats['facultyDistribution'] as $level => $count)
                            <tr>
                                <td class="text-sm">{{ $level }}</td>
                                <td class="text-sm font-medium" style="text-align: right;">{{ number_format($count) }}</td>
                                <td class="text-sm text-gray-500" style="text-align: right;">
                                    {{ $facultyTotal > 0 ? number_format(($count / $facultyTotal) * 100, 1) : '0' }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid #374151;">
                            <td class="text-sm font-bold">Total</td>
                            <td class="text-sm font-bold" style="text-align: right;">{{ number_format($facultyTotal) }}</td>
                            <td class="text-sm font-bold" style="text-align: right;">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif


    @endif

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F j, Y g:i a') }}</p>
        <p>Kiroku Student Logging System 2025</p>
    </div>

</body>

</html>