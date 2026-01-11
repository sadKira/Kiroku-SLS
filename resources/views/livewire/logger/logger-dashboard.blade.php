<div class="max-w-5xl mx-auto px-4">
     {{-- App Header --}}
    <div class="flex items-center justify-between">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('logger_dashboard') }}">Dashboard</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

    </div>

    <div class="mt-10">
        <flux:heading size="lg">Student Logs</flux:heading>
    </div>

    {{-- Logs Content --}}
    <livewire:logger.logger-table />

</div>