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
                                                <flux:badge size="sm" color="amber" variant="pill">Faculty</flux:badge>
                                                @php 
                                                    $levelCode = \App\Models\InstructionalLevel::where('name', $entity->instructional_level)->value('code') ?? $entity->instructional_level; 
                                                @endphp
                                                <flux:badge size="sm" color="amber" variant="pill">{{ $levelCode }}</flux:badge>
                                            @elseif ($entity->user_type === 'shs')
                                                <flux:badge size="sm" color="red" variant="pill">SHS</flux:badge>
                                                @php 
                                                    $strandCode = \App\Models\Strand::where('name', $entity->strand)->value('code') ?? $entity->strand; 
                                                @endphp
                                                <flux:badge size="sm" color="red" variant="pill">{{ $entity->year_level }} - {{ $strandCode }}</flux:badge>
                                            @else
                                                <flux:badge size="sm" color="blue" variant="pill">College</flux:badge>
                                                @php 
                                                    $courseCode = \App\Models\Course::where('name', $entity->course)->value('code') ?? $entity->course; 
                                                @endphp
                                                <flux:badge size="sm" color="blue" variant="pill">{{ $entity->year_level }} - {{ $courseCode }}</flux:badge>
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
