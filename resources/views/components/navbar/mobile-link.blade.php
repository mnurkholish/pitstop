@props(['active' => false])

<a {{ $attributes->class([
    'block rounded-lg px-3 py-2.5 text-sm font-medium transition',
    'bg-blue-100 text-blue-700' => $active,
    'text-slate-600 hover:bg-slate-50 hover:text-blue-700' => ! $active,
]) }}>
    {{ $slot }}
</a>
