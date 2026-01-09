<div>
     {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('student_logs') }}">Student Logs</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        {{-- Profile Section --}}
        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex items-center justify-between mb-7">

        <flux:heading size="lg" class="font-bold">Student Logs</flux:heading>

        {{-- Add Log --}}
        <flux:button icon="plus" wire:click="" variant="primary" size="sm">Add Log</flux:button>

    </div>

    {{-- Table --}}
    <livewire:management.student-logs-table />

</div>

