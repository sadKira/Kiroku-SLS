<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Logs - {{ \Carbon\Carbon::parse($logSession->date)->format('F j, Y') }}</title>

    <link rel="icon" href="/mkdlib-logo.ico" sizes="any">
    <link rel="icon" href="/mkdlib-logo.svg" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Avoid splitting log records across PDF pages --}}
    <style>
        @media print {
            .log-record {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
        }

        .log-table th,
        .log-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .log-table th {
            background-color: #f9fafb;
            font-weight: 600;
            font-size: 16px;
            color: #374151;
        }

        .log-table tr:last-child td {
            border-bottom: none;
        }

        .time-in {
            color: #10b981;
        }

        .time-out {
            color: #ef4444;
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased p-4">
    <div class="mb-6">
        <flux:heading size="xl"><span class="font-bold">MKD Resource Center</span></flux:heading>
        <flux:heading size="xl" class="mt-3"><span class="font-bold">Student Logs</span></flux:heading>
        <flux:heading size="lg">
            {{ \Carbon\Carbon::parse($logSession->date)->format('F j, Y') }}
        </flux:heading>
        <p class=" text-gray-600">
            Academic Year: {{ $logSession->school_year }}
        </p>
    </div>

    <div class="mb-4">
        <p class="text-sm text-gray-600">
            Total Records: {{ $logRecords->count() }}
        </p>
    </div>

    {{-- Course Legend --}}
    <div class="mb-4 p-3 bg-gray-50 rounded border border-gray-200">
        <p class="text-xs font-semibold text-gray-700 mb-2">Course Abbreviations:</p>
        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
            <div><span class="font-medium">ABIS</span> - Bachelor of Arts in International Studies</div>
            <div><span class="font-medium">BSIS</span> - Bachelor of Science in Information Systems</div>
            <div><span class="font-medium">BHS</span> - Bachelor of Human Services</div>
            <div><span class="font-medium">BSED</span> - Bachelor of Secondary Education</div>
            <div><span class="font-medium">ECED</span> - Bachelor of Elementary Education</div>
            <div><span class="font-medium">SNED</span> - Bachelor of Special Needs Education</div>
        </div>
    </div>

    <table class="log-table">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Year Level</th>
                <th>Course</th>
                <th>In</th>
                <th>Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logRecords as $index => $record)
                <tr class="log-record">
                    <td class="text-sm">{{ $index + 1 }}</td>
                    <td class="text-sm font-mono">{{ $record->student->id_student ?? 'N/A' }}</td>
                    <td class="text-sm font-medium">
                        @if ($record->student)
                            {{ $record->student->last_name }}, {{ $record->student->first_name }}
                        @else
                            <span class="text-gray-400">Unknown student</span>
                        @endif
                    </td>
                    <td class="text-sm">{{ $record->student->year_level ?? 'N/A' }}</td>
                    <td class="text-sm font-medium">
                        @if ($record->student && $record->student->course)
                            @php
                                $course = $record->student->course;
                                $output = match (true) {
                                    $course == 'Bachelor of Arts in International Studies' => 'ABIS',
                                    $course == 'Bachelor of Science in Information Systems' => 'BSIS',
                                    $course == 'Bachelor of Human Services' => 'BHS',
                                    $course == 'Bachelor of Secondary Education' => 'BSED',
                                    $course == 'Bachelor of Elementary Education' => 'ECED',
                                    $course == 'Bachelor of Special Needs Education' => 'SNED',
                                    default => $course,
                                };
                            @endphp
                            {{ $output }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-sm time-in">
                        @if ($record->time_in)
                            {{ \Carbon\Carbon::parse($record->time_in)->timezone('Asia/Manila')->format('g:i a') }}
                        @else
                            <span class="text-gray-400 italic">—</span>
                        @endif
                    </td>
                    <td class="text-sm time-out">
                        @if ($record->time_out)
                            {{ \Carbon\Carbon::parse($record->time_out)->timezone('Asia/Manila')->format('g:i a') }}
                        @else
                            <span class="text-gray-400 italic">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F j, Y g:i a') }}</p>
        <p>Kiroku Student Logging System 2025</p>
    </div>
</body>

</html>
