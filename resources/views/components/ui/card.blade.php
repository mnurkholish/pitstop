@props(['padding' => true])

<div {{ $attributes->class([
    'rounded-2xl border border-slate-200 bg-white shadow-sm',
    'p-4 sm:p-6' => $padding,
]) }}>
    {{ $slot }}
</div>
