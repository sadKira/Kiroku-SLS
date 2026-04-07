<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $heading ?? 'Student Barcodes' }}</title>

    <link rel="icon" href="/mkdlib-logo.ico" sizes="any">
    <link rel="icon" href="/mkdlib-logo.svg" type="image/svg+xml">
    {{-- <link rel="apple-touch-icon" href="/apple-touch-icon.png"> --}}

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Avoid splitting barcode cards across PDF pages and ensure fixed width --}}
    <style>
        @media print {
            .barcode-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-white text-slate-900 antialiased p-4">

    <div class="mb-10">
        <flux:heading size="xl"><span class="font-bold">MKD Learning Resource Center</span></flux:heading>
        <div class="mt-3 flex items-center gap-2">
            <flux:heading size="xl"><span class="font-bold">{{ $heading ?? 'Student Barcodes' }}</span></flux:heading>
            <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M9 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM17 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 0 0-1.5-4.33A5 5 0 0 1 19 16v1h-6.07ZM6 11a5 5 0 0 1 5 5v1H1v-1a5 5 0 0 1 5-5Z" />
                </svg>
                {{ number_format($students->count()) }} {{ $students->count() === 1 ? 'barcode' : 'barcodes' }}
            </span>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 justify-center">
        @foreach ($students as $student)

            <x-ui.card size="sm" class="barcode-card p-3 text-center justify-center">

                <div class="text-center">
                    @php
                        // Generate a CODE_128 barcode SVG for best scanner support
                        // as recommended in the laravel-barcode package docs:
                        // "Most used types are CODE_128 and CODE_39."
                        $barcodeSvg = \AgeekDev\Barcode\Facades\Barcode::imageType('svg')
                            ->foregroundColor('#000000')
                            ->height(60)
                            ->widthFactor(2)
                            ->type(\AgeekDev\Barcode\Enums\BarcodeType::CODE_128)
                            ->generate((string) $student->{$idField});
                    @endphp

                    <div class="flex justify-center">
                        {!! $barcodeSvg !!}
                    </div>

                </div>

                <flux:heading class="truncate mt-1">{{ $student->last_name }}, {{ $student->first_name }}</flux:heading>
                <span class="block text-[9px] tracking-[0.06em]">{{ $student->{$idField} }}</span>

            </x-ui.card>

        @endforeach
    </div>

    <div class="mt-6 text-center text-xs text-gray-500">
        <p>Generated on {{ \Carbon\Carbon::now()->timezone('Asia/Manila')->format('F j, Y g:i a') }}</p>
        <p>Kiroku Student Logging System 2025</p>
    </div>
</body>

</html>