<div>
    @php
        $logRecords = $logSession->logRecords()->with(['student', 'faculty'])->orderBy('time_in', 'desc')->get();
    @endphp

    @if ($logRecords->isEmpty())
        <div class="flex flex-col justify-center items-center gap-3 py-12">
            <flux:icon.clipboard-document-list variant="solid" class="w-12 h-12 text-gray-400 dark:text-neutral-500" />
            <flux:heading size="lg" class="text-gray-800 dark:text-neutral-200">No Log Records Yet</flux:heading>
            <p class="text-sm text-gray-500 dark:text-neutral-400">Start scanning barcodes to log attendance.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($logRecords as $record)
                @php
                    $entity = $record->loggable_type === 'faculty' ? $record->faculty : $record->student;
                @endphp
                <div class="bg-white dark:bg-zinc-800 border border-black/10 dark:border-white/10 rounded-lg p-4 mr-5">
                    <div class="flex items-center justify-between gap-4">
                        {{-- User Info --}}
                        <div class="flex justify-center">
                            <div class="flex items-center gap-3">
                               
                                <div>
                                    @if ($entity)
                                        <flux:heading size="base" class="text-gray-800 dark:text-neutral-200">
                                            {{ $entity->last_name }}, {{ $entity->first_name }}
                                        </flux:heading>
                                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                                            @if ($record->loggable_type === 'faculty')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200 mr-1">Faculty</span>
                                                {{ $entity->instructional_level }}
                                            @elseif ($entity->user_type === 'shs')
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200 mr-1">SHS</span>
                                                {{ $entity->year_level }} - {{ $entity->strand }}
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 mr-1">College</span>
                                                {{ $entity->year_level }} - 
                                                @php
                                                    $course = $entity->course;
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
                                            @endif
                                        </p>
                                    @else
                                        <flux:heading size="base" class="text-gray-400">Unknown User</flux:heading>
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
                                            {{ \Carbon\Carbon::parse($record->time_in)->format('g:i a') }}
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
                                            {{ \Carbon\Carbon::parse($record->time_out)->format('g:i a') }}
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
