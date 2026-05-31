@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $variantClasses = [
        'primary' => 'border-blue-600 bg-blue-600 text-white hover:border-blue-700 hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus:ring-blue-500',
        'danger' => 'border-red-600 bg-red-600 text-white hover:border-red-700 hover:bg-red-700 focus:ring-red-500',
        'ghost' => 'border-transparent bg-transparent text-slate-600 hover:bg-slate-100 hover:text-blue-700 focus:ring-blue-500',
    ][$variant];

    $sizeClasses = [
        'sm' => 'px-3 py-2 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-sm',
    ][$size];

    $classes = "inline-flex items-center justify-center gap-2 rounded-lg border font-semibold transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 {$variantClasses} {$sizeClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
