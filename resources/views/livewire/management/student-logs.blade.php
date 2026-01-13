<div>
    
    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">
        
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Student Logs</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex items-center justify-between mb-7">

        <flux:heading size="lg" class="font-bold">Student Logs</flux:heading>

    </div>

    {{-- Table --}}
    <livewire:management.student-logs-table />

</div>

