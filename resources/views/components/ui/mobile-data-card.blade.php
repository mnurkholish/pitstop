@props(['title' => null])

<article {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm']) }}>
    @if ($title || isset($badge))
        <div class="flex items-start justify-between gap-3">
            @if ($title)
                <h3 class="text-sm font-semibold text-slate-800">{{ $title }}</h3>
            @endif
            @isset($badge)
                {{ $badge }}
            @endisset
        </div>
    @endif

    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
        {{ $slot }}
    </div>

    @isset($actions)
        <div class="mt-4 flex flex-col gap-2 sm:flex-row">{{ $actions }}</div>
    @endisset
</article>
