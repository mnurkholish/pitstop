@php
    $initials = collect(explode(' ', trim(Auth::user()->name)))
        ->filter()
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex size-9 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700']) }}>
    {{ $initials ?: 'PS' }}
</span>
