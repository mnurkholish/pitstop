@props([
    'service',
    'size' => 'card',
])

@php
    $classes = match ($size) {
        'thumbnail' => 'size-12 shrink-0 rounded-xl',
        'detail' => 'h-56 w-full rounded-xl sm:h-72',
        default => 'h-40 w-full',
    };
@endphp

@if ($service->image)
    <img
        src="{{ asset('storage/'.$service->image) }}"
        alt="{{ $service->name }}"
        {{ $attributes->class([$classes, 'object-cover']) }}
    >
@else
    <div {{ $attributes->class([$classes, 'flex items-center justify-center bg-blue-50 text-blue-600']) }}>
        <span class="flex size-10 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white">PS</span>
    </div>
@endif
