@props(['active' => false])

<a {{ $attributes->class([
    'rounded-lg px-3 py-2 text-sm font-medium transition',
    'bg-blue-100 text-blue-700' => $active,
    'text-slate-600 hover:bg-slate-50 hover:text-blue-700' => ! $active,
]) }}>
    {{ $slot }}
</a>
