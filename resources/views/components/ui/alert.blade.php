@props(['variant' => 'info', 'title' => null])

@php
    $classes = [
        'info' => 'border-blue-200 bg-blue-50 text-blue-700',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-700',
        'danger' => 'border-red-200 bg-red-50 text-red-700',
    ][$variant];
@endphp

<div role="alert" {{ $attributes->merge(['class' => "rounded-xl border px-4 py-3 text-sm {$classes}"]) }}>
    @if ($title)
        <p class="font-semibold">{{ $title }}</p>
    @endif
    <div @class(['mt-1' => $title])>{{ $slot }}</div>
</div>
