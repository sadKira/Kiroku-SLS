<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen overflow-hidden bg-zinc-50 dark:bg-zinc-800">
        
        {{ $slot }}

        @fluxScripts
    </body>
</html>
