<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Super Administrator</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>User Management</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex justify-between items-center mb-5">
        <flux:heading size="xl">User Management</flux:heading>
    </div>

    <livewire:management.user-management-table lazy />

</div>
