@props([
    'service',
    'detail' => false,
])

<article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <x-ui.service-image :service="$service" />

    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-semibold text-slate-800">{{ $service->name }}</h3>
            <x-ui.badge variant="active">Aktif</x-ui.badge>
        </div>
        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">{{ $service->description }}</p>
        <div class="mt-auto flex flex-wrap gap-x-4 gap-y-1 pt-4 text-xs font-medium text-slate-600">
            <span>Rp {{ number_format($service->price, 0, ',', '.') }}</span>
            <span>{{ $service->duration_minutes }} menit</span>
        </div>
        @if ($detail)
            <x-ui.button
                type="button"
                variant="secondary"
                size="sm"
                class="mt-4 w-full"
                data-service-action="detail"
                data-service-id="{{ $service->id }}"
            >
                Lihat Detail
            </x-ui.button>
        @else
            <x-ui.button href="{{ route('services.index') }}" variant="secondary" size="sm" class="mt-4 w-full">
                Lihat Detail
            </x-ui.button>
        @endif
    </div>
</article>
