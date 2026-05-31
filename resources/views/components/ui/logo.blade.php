@props(['admin' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 font-bold tracking-tight text-blue-900']) }}>
    <span class="inline-flex size-8 items-center justify-center rounded-lg bg-blue-600 text-xs font-bold text-white">PS</span>
    <span>PitStop{{ $admin ? ' Admin' : '' }}</span>
</span>
