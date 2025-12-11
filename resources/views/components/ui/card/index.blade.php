@props(['size' => 'md'])

@php
    $variantClasses = match ($size) {
        'xs' => 'max-w-xs',
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' =>  'max-w-2xl',
    };
    
    // dark:bg-neutral-900
    $classes = [
        'bg-white dark:bg-zinc-800 border border-black/10 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-zinc-700',
        '[:where(&)]:p-4 [:where(&)]:rounded-lg',
        $variantClasses
    ];

@endphp

<div {{ $attributes->class(Arr::toCssClasses($classes)) }}>
    {{ $slot }}
</div>