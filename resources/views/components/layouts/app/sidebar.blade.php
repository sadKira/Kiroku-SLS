<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">

<head>
    @include('partials.head')
</head>

<script>
  // Ensure the 'dark' class is removed from the html element
  document.documentElement.classList.remove('dark');
</script>   

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
    <flux:sidebar sticky class="border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

        <flux:sidebar.header>

            <flux:sidebar.brand :href="route('admin_dashboard')" name="Kiroku SLS" wire:navigate>
                <x-slot>
                    <img src="{{ asset('mkdlib-logo.svg') }}" alt="MKD Library Logo" class="w-auto h-5">
                </x-slot>
            </flux:sidebar.brand>
            
            {{-- <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" /> --}}
        </flux:sidebar.header>

        <flux:sidebar.nav>

            <flux:sidebar.item icon="home" :href="route('admin_dashboard')" :current="request()->routeIs('admin_dashboard')" wire:navigate>
                Dashboard
            </flux:sidebar.item>

            <flux:sidebar.item icon="document-text" :href="route('user_logs')" :current="request()->routeIs('user_logs')" wire:navigate>
                User Logs
            </flux:sidebar.item>

            <flux:sidebar.group expandable icon="user-group" heading="User List">
                <flux:sidebar.item icon="identification" :href="route('faculty_list')" :current="request()->routeIs('faculty_list')" wire:navigate>
                    Faculty
                </flux:sidebar.item>

                <flux:sidebar.item icon="academic-cap" :href="route('college_list')" :current="request()->routeIs('college_list')" wire:navigate>
                    College
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open" :href="route('shs_list')" :current="request()->routeIs('shs_list')" wire:navigate>
                    Senior High School
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group expandable icon="rectangle-stack" heading="Courses, Strands & Levels">
                <flux:sidebar.item icon="clipboard-document-list" :href="route('course_management')" :current="request()->routeIs('course_management')" wire:navigate>
                    Courses
                </flux:sidebar.item>

                <flux:sidebar.item icon="bars-3-center-left" :href="route('strand_management')" :current="request()->routeIs('strand_management')" wire:navigate>
                    Strands
                </flux:sidebar.item>

                <flux:sidebar.item icon="bars-3-bottom-left" :href="route('level_management')" :current="request()->routeIs('level_management')" wire:navigate>
                    Instructional Levels
                </flux:sidebar.item>
            </flux:sidebar.group>

            @can('SA')
                <flux:sidebar.group expandable icon="shield-check" heading="Super Administrator">
                    <flux:sidebar.item :href="url('/system-logs')" icon="server-stack" target="_blank">
                        System Logs
                    </flux:sidebar.item>
                    <flux:sidebar.item :href="route('user_management')" icon="cog-6-tooth" :current="request()->routeIs('user_management')" wire:navigate>
                        User Management
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

    @fluxScripts

    {{-- Sheaf UI Toast Notifications --}}
    <x-ui.toast position="bottom-right" maxToasts="5" />

</body>

</html>
