<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">

        <flux:sidebar.header>

            <flux:sidebar.brand :href="route('admin_dashboard')" name="Kiroku ALS">
                <x-slot>
                    <img src="{{ asset('mkdlib-logo.svg') }}" alt="MKD Library Logo" class="w-auto h-5">
                </x-slot>
            </flux:sidebar.brand>
            
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>

            <flux:sidebar.item icon="home" :href="route('admin_dashboard')" :current="request()->routeIs('admin_dashboard')" wire:navigate>
                Dashboard
            </flux:sidebar.item>

            <flux:sidebar.item icon="users" :href="route('student_list')" :current="request()->routeIs('student_list')"  wire:navigate>
                Student List
            </flux:sidebar.item>

            <flux:sidebar.group expandable icon="document-chart-bar" heading="Records" class="grid">
                <flux:sidebar.item :href="route('hourly_record')" :current="request()->routeIs('hourly_record')"  wire:navigate>Hourly Record</flux:sidebar.item>
                <flux:sidebar.item :href="route('daily_record')" :current="request()->routeIs('daily_record')"  wire:navigate>Daily Record</flux:sidebar.item>
                <flux:sidebar.item :href="route('monthly_record')" :current="request()->routeIs('monthly_record')"  wire:navigate>Monthly Record</flux:sidebar.item>
                <flux:sidebar.item :href="route('semestral_record')" :current="request()->routeIs('semestral_record')"  wire:navigate>Semestral Record</flux:sidebar.item>
            </flux:sidebar.group>

            @can('SA')
                <flux:sidebar.item :href="url('/system-logs')" icon="folder-git-2" target="_blank">
                    System Logs
                </flux:sidebar.item>
            @endcan

        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>

            <flux:sidebar.item :href="route('about_kiroku')" :current="request()->routeIs('about_kiroku')" icon="information-circle" wire:navigate>
                About Kiroku
            </flux:sidebar.item>

            {{-- Log out button --}}
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:sidebar.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                    Log Out
                </flux:sidebar.item>
            </form>

        </flux:sidebar.nav>

    </flux:sidebar>

    {{ $slot }}

    @fluxScripts

    
</body>

</html>
