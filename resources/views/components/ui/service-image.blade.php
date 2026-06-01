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
    $image = $service->image
        ? asset('storage/'.$service->image)
        : asset('images/services/service-default.png');
@endphp

<img
    src="{{ $image }}"
    alt="{{ $service->name }}"
    width="1536"
    height="1024"
    {{ $attributes->class([$classes, 'object-cover']) }}
>
