@props([
    'backHref' => null,
    'backLabel' => 'Kembali',
    'prompt' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'mt-6 flex flex-col gap-2 border-t border-slate-100 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between']) }}>
    @if ($backHref)
        <a href="{{ $backHref }}" class="font-medium text-slate-500 transition hover:text-blue-700">
            &larr; {{ $backLabel }}
        </a>
    @endif

    @if ($prompt && $actionHref && $actionLabel)
        <p class="text-slate-500">
            {{ $prompt }}
            <a href="{{ $actionHref }}" class="font-semibold text-blue-700 transition hover:text-blue-800">{{ $actionLabel }}</a>
        </p>
    @endif
</div>
