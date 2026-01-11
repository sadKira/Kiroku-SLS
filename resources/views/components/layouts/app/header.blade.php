<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">

            <flux:brand href="{{ route('logger_dashboard') }}" name="MKD Resource Center | Student Logger" class="font-logo">
                <x-slot name="logo" class="size-8">
                    <img src="{{ asset('mkdlib-logo.svg') }}" alt="MKD Logo" class="">
                </x-slot>
            </flux:brand>

            <flux:spacer />

            <flux:navbar class="mr-5 me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">

                <flux:input as="button" size="sm" placeholder="Search..." icon="magnifying-glass" kbd="⌘K" />
                
                {{-- <flux:tooltip content="Toggle Dark Mode" position="bottom">
                    <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" class="h-10 [&>div>svg]:size-5" icon="moon" variant="subtle" aria-label="Toggle dark mode" />
                </flux:tooltip> --}}
                
            </flux:navbar>

            <flux:dropdown position="bottom" align="end">
              
                <flux:profile
                    circle
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                    :name="auth()->user()->name"
                    icon:trailing="chevron-up-down"
                />
                <flux:navmenu>
                    
                    {{-- Log Out --}}
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:navmenu.item type="submit" icon="arrow-right-start-on-rectangle" variant="danger">Log Out</flux:navmenu.item>
                    </form>
                    
                </flux:navmenu>
            </flux:dropdown>

        </flux:header>

       
        {{ $slot }}

        @fluxScripts
    </body>
</html>
