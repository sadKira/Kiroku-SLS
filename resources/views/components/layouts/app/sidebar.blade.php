<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<script>

    // Prevent Page Flickering at Render Time (SheafUI)
    const loadDarkMode = () => {
        const theme = localstorage.getItem('theme') ?? 'system'

        if(
            theme === 'dark' ||
            (theme === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)')
                .matches)
        ) {
            document.documentElement.classList.add('dark')
        }
    }
    loadDarkMode();
    document.addEventListener('livewire:navigated', function () {
        loadDarkMode();
    });

</script>

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
    <flux:sidebar sticky collapsible class="border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

        <flux:sidebar.header>

            <flux:sidebar.brand :href="route('admin_dashboard')" name="Kiroku SLS">
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

            <flux:sidebar.item icon="document-text" :href="route('student_logs')" :current="request()->routeIs('student_logs')"  wire:navigate>
                Student Logs
            </flux:sidebar.item>

            @can('SA')
                <flux:sidebar.group expandable icon="shield-check" heading="Super Administrator">
                    <flux:sidebar.item :href="url('/system-logs')" icon="folder-git-2" target="_blank">
                        System Logs
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endcan

        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>

            <flux:sidebar.item :href="route('about_kiroku')" :current="request()->routeIs('about_kiroku')" icon="information-circle" wire:navigate>
                About Kiroku
            </flux:sidebar.item>
        
        </flux:sidebar.nav>

    </flux:sidebar>

    {{ $slot }}

    {{-- @livewireScriptConfig --}}
    <script>
        loadDarkMode()
    </script>

    {{-- Sheaf UI Toast Notifications --}}
    <x-ui.toast position="bottom-right" maxToasts="5" />

    @fluxScripts

</body>

</html>
