<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">
        
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>About Kiroku</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    <div class="flex items-center justify-center">
        <img src="{{ asset('mkdlib-logo.ico') }}" alt="MKD Library Logo" class="w-auto h-50">
    </div>


</div>
