<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">

            {{-- Left Portion w/ Image --}}
            <div class="relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800 bg-cover bg-center"
                 style="background-image: url('{{ asset('images/mkdlib-login.jpg') }}')">

                {{-- Gradient --}}
                <div class="absolute inset-0"
                    style="
                        background: radial-gradient(
                            circle at right center,
                            rgba(0, 0, 0, 0.55) 0%,
                            rgba(0, 0, 0, 0.65) 40%,
                            rgba(0, 0, 0, 0.85) 80%
                        );
                    ">
                </div>

                {{-- 0.4, 0.55, 0.75 --}}

                {{-- System Name --}}
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-20 w-30 items-center justify-center rounded-md">
                        <x-app-logo-icon class="me-2 h-7 fill-current text-white" />
                    </span>
                    Kiroku Attendance Logging System
                </a>

                {{-- Auto Generated Quotes --}}
                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="lg" class="text-white">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading class="text-white">{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div>
            </div>

            {{-- Login Form --}}
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md">
                            {{-- <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" /> --}}
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
