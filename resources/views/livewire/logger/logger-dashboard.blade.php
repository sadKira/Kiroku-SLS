<div class="max-w-5xl mx-auto px-4">
     {{-- App Header --}}
    <div class="flex items-center justify-between">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('logger_dashboard') }}" wire:navigate>Dashboard</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

    </div>

    <div class="mt-10 mb-6">
        <flux:heading size="xl">Student Logs</flux:heading>
    </div>

    {{-- Logs Content --}}
    <livewire:logger.logger-table />

</div>