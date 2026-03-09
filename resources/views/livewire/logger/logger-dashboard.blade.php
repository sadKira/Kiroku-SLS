<div class="max-w-5xl mx-auto px-4">
     {{-- App Header --}}
    <div class="flex items-center justify-between">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('logger_dashboard') }}" wire:navigate>Dashboard</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

    </div>

    {{-- Today's Log Section --}}
    <div class="mt-10 mb-8">
        <flux:heading size="xl" class="mb-6">Today</flux:heading>

        @if ($todayLogSession)
            {{-- Today's Log Card --}}
            <a href="{{ route('view_logs', $todayLogSession) }}" wire:navigate
                class="block bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-5 hover:shadow-lg transition-shadow max-w-sm">
                {{-- Date --}}
                <div class="mb-3">
                    <flux:heading size="base" class="text-gray-800 dark:text-neutral-200">
                        {{ \Carbon\Carbon::parse($todayLogSession->date)->format('F j, Y') }}
                    </flux:heading>
                    <flux:heading size="base" class="text-gray-800 dark:text-neutral-200">
                        {{ \Carbon\Carbon::parse($todayLogSession->date)->format('l') }}
                    </flux:heading>
                </div>

                {{-- School Year --}}
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-neutral-400">
                        {{ $todayLogSession->school_year }}
                    </p>
                </div>

                {{-- Student Count --}}
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-black/10 dark:border-white/10">
                    <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-neutral-400">
                        <flux:icon.users variant="outline" class="w-4 h-4" />
                        @php
                            $count = $todayLogSession->students_count ?? $todayLogSession->log_records_count ?? 0;
                        @endphp
                        <span>{{ $count }} {{ $count == 1 ? 'student' : 'students' }}</span>
                    </div>
                </div>
            </a>
        @else
            {{-- Empty State --}}
            <div class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-12 max-w-sm">
                <div class="flex flex-col justify-center items-center gap-3">
                    <flux:icon.calendar-days variant="outline" class="w-12 h-12 text-gray-400 dark:text-neutral-500" />
                    <flux:heading size="lg">No Log Session Today</flux:heading>
                    <p class="text-sm text-gray-500 dark:text-neutral-400 text-center">
                        Contact your administrator to create one.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="mb-6">
        <flux:heading size="xl">Student Logs</flux:heading>
    </div>

    {{-- Logs Content --}}
    <livewire:logger.logger-table />

</div>