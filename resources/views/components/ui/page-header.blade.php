@props(['title', 'description' => null])

<div {{ $attributes->class(['flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-blue-900">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-col gap-2 sm:flex-row">{{ $actions }}</div>
    @endisset
</div>
