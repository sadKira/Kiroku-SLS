<div class="max-w-4xl mx-auto px-4">
     {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('logger_dashboard') }}">Dashboard</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

    </div>

    <div class="mt-5">
        <flux:heading size="lg">Student Logs</flux:heading>
    </div>

    
</div>
