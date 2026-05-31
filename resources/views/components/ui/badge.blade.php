@props(['variant' => 'neutral'])

@php
    $classes = [
        'neutral' => 'bg-slate-100 text-slate-600',
        'pending' => 'bg-amber-100 text-amber-700',
        'processing' => 'bg-blue-100 text-blue-700',
        'success' => 'bg-emerald-100 text-emerald-700',
        'danger' => 'bg-red-100 text-red-700',
        'active' => 'bg-emerald-100 text-emerald-700',
        'inactive' => 'bg-slate-100 text-slate-600',
    ][$variant];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ $slot }}
</span>
