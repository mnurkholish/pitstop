@props([
    'title' => 'Data belum tersedia',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center']) }}>
    <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 13V7a2 2 0 0 0-2-2h-3.5l-2-2h-5A2.5 2.5 0 0 0 5 5.5V13m15 0-2.5 6h-11L4 13m16 0h-5l-1.5 2h-3L9 13H4" />
        </svg>
    </div>
    <h3 class="mt-4 text-sm font-semibold text-slate-700">{{ $title }}</h3>
    @if ($description)
        <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
