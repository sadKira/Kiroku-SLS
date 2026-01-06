<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Student Barcodes</title>

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
    <div class="text-center mb-4">
        <flux:heading size="xl"><span class="font-bold">Kiroku ALS</span></flux:heading>
        <div class="flex items-center gap-1 justify-center">
            <flux:icon.barcode />
            <flux:heading size="lg"><span>Student Barcodes: {{ $students->count() }}</flux:heading>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 justify-center">
        @foreach ($students as $student)

            <x-ui.card size="sm" class="barcode-card p-3 text-center">

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
                            ->generate((string) $student->id_student);
                    @endphp

                    {!! $barcodeSvg !!}

                    <flux:heading class="truncate">{{ $student->full_name }}</flux:heading>
                    <span class="block text-[9px] tracking-[0.06em]">{{ $student->id_student }}</span>

                </div>
            </x-ui.card>

        @endforeach
    </div>
</body>

</html>