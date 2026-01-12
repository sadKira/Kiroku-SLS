<div>
    @php
        $logRecords = $logSession->logRecords()->with('student')->orderBy('time_in', 'desc')->get();
    @endphp

    @if ($logRecords->isEmpty())
        <div class="flex flex-col justify-center items-center gap-3 py-12">
            <flux:icon.clipboard-document-list variant="solid" class="w-12 h-12 text-gray-400 dark:text-neutral-500" />
            <flux:heading size="lg" class="text-gray-800 dark:text-neutral-200">No Log Records Yet</flux:heading>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Start scanning barcodes to log student attendance.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($logRecords as $record)
                <div class="bg-white dark:bg-zinc-800 border border-black/10 dark:border-white/10 rounded-lg p-4 mr-5">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Student Info --}}
                        <div class="flex justify-center">
                            <div class="flex items-center gap-3">
                               
                                <div>
                                    @if ($record->student)
                                        <flux:heading size="base" class="text-gray-800 dark:text-neutral-200">
                                            {{ $record->student->last_name }}, {{ $record->student->first_name }}
                                        </flux:heading>
                                        <p class="text-sm text-gray-600 dark:text-neutral-400">
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
                                    @else
                                        <flux:heading size="base" class="text-gray-400">Unknown Student</flux:heading>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Time In/Out --}}
                        <div class="flex items-center gap-4">
                            {{-- Time In --}}
                            @if ($record->time_in)
                                <div class="flex items-center gap-2">
                                    <flux:icon.log-in class="text-green-500" variant="micro" />
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                            {{ \Carbon\Carbon::parse($record->time_in)->timezone('Asia/Manila')->format('g:i a') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-sm">—</span>
                            @endif

                            {{-- Time Out --}}
                            @if ($record->time_out)
                                <div class="flex items-center gap-2">
                                    <flux:icon.log-out class="text-red-500" variant="micro" />
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                            {{ \Carbon\Carbon::parse($record->time_out)->timezone('Asia/Manila')->format('g:i a') }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 italic text-sm">—</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
