@props(['service'])

<article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    @if ($service->image)
        <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name }}" class="h-40 w-full object-cover">
    @else
        <div class="flex h-40 items-center justify-center bg-blue-50 text-blue-600">
            <div class="text-center">
                <span class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-blue-600 text-base font-bold text-white">PS</span>
                <p class="mt-2 text-xs font-semibold uppercase tracking-wider text-blue-700">PitStop Service</p>
            </div>
        </div>
    @endif

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
        <x-ui.button href="{{ route('services.index') }}" variant="secondary" size="sm" class="mt-4 w-full">
            Lihat Detail
        </x-ui.button>
    </div>
</article>
