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

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 9999px;
            font-weight: 500;
            margin-bottom: 2px;
            margin-right: 4px;
            white-space: nowrap;
        }
        .badge-college {
            background-color: #dbeafe;
            color: #1e3a8a;
        }
        .badge-shs {
            background-color: #fee2e2;
            color: #7f1d1d;
        }
        .badge-faculty {
            background-color: #fef3c7;
            color: #78350f;
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased p-4">
    <div class="mb-6">
        <img src="{{ asset('images/shusseki-export-seal.png') }}" alt="MKD Learning Resource Center" class="h-10 w-auto mx-auto block mb-6" />
        <flux:heading size="xl" class="mt-15"><span class="font-bold">User Logs</span>: {{ \Carbon\Carbon::parse($logSession->date)->format('F j, Y') }}</flux:heading>
        <p class=" text-gray-600">
            Academic Year: {{ $logSession->school_year }}
        </p>
    </div>

    {{-- Legend --}}
    <div class="mb-4 p-3 bg-gray-50 rounded border border-gray-200">
        <p class="text-xs font-semibold text-gray-700 mb-3">Abbreviations:</p>
        <div class="grid grid-cols-3 gap-4 text-xs text-gray-600 items-start">
            <div>
                <p class="font-semibold mb-1 text-gray-800">College Courses</p>
                @foreach(\App\Models\Course::orderBy('code')->get() as $courseItem)
                    <div><span class="font-medium">{{ $courseItem->code }}</span> - {{ $courseItem->name }}</div>
                @endforeach
            </div>
            <div>
                <p class="font-semibold mb-1 text-gray-800">SHS Strands</p>
                @foreach(\App\Models\Strand::orderBy('code')->get() as $strandItem)
                    <div><span class="font-medium">{{ $strandItem->code }}</span> - {{ $strandItem->name }}</div>
                @endforeach
            </div>
            <div>
                <p class="font-semibold mb-1 text-gray-800">Faculty Levels</p>
                @foreach(\App\Models\InstructionalLevel::orderBy('code')->get() as $levelItem)
                    <div><span class="font-medium">{{ $levelItem->code }}</span> - {{ $levelItem->name }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <table class="log-table">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Classification</th>
                <th>In</th>
                <th>Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logRecords as $index => $record)
                @php
                    $entity = $record->loggable_type === 'faculty' ? $record->faculty : $record->student;
                @endphp
                <tr class="log-record">
                    <td class="text-sm">{{ $index + 1 }}</td>
                    <td class="text-sm font-mono">
                        @if ($record->loggable_type === 'faculty' && $entity)
                            {{ $entity->id_faculty }}
                        @elseif ($entity)
                            {{ $entity->id_student }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="text-sm font-medium">
                        @if ($entity)
                            {{ $entity->last_name }}, {{ $entity->first_name }}
                        @else
                            <span class="text-gray-400">Unknown user</span>
                        @endif
                    </td>
                    <td class="text-sm">
                        @if ($entity)
                            @if ($record->loggable_type === 'faculty')
                                @php 
                                    $levelCode = \App\Models\InstructionalLevel::where('name', $entity->instructional_level)->value('code') ?? $entity->instructional_level; 
                                @endphp
                                <span class="badge badge-faculty">Faculty</span>
                                <span class="badge badge-faculty">{{ $levelCode }}</span>
                            @elseif ($entity->user_type === 'shs')
                                @php 
                                    $strandCode = \App\Models\Strand::where('name', $entity->strand)->value('code') ?? $entity->strand; 
                                @endphp
                                <span class="badge badge-shs">SHS</span>
                                <span class="badge badge-shs">{{ $entity->year_level }} - {{ $strandCode }}</span>
                            @else
                                @php 
                                    $courseCode = \App\Models\Course::where('name', $entity->course)->value('code') ?? $entity->course; 
                                @endphp
                                <span class="badge badge-college">College</span>
                                <span class="badge badge-college">{{ $entity->year_level }} - {{ $courseCode }}</span>
                            @endif
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
