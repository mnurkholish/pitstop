@foreach ($services as $service)
    <x-ui.mobile-data-card :title="$service->name" class="mb-3">
        <x-slot name="badge">
            <x-ui.badge :variant="$service->is_active ? 'active' : 'inactive'">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
        </x-slot>
        <x-ui.service-image :service="$service" size="detail" class="h-36 sm:h-36" />
        <div>
            <p class="text-xs text-slate-400">Harga</p>
            <p class="mt-1 font-medium text-slate-700">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-xs text-slate-400">Durasi</p>
            <p class="mt-1 font-medium text-slate-700">{{ $service->duration_minutes }} menit</p>
        </div>
        <x-slot name="actions">
            <x-ui.button href="{{ route('admin.services.show', $service) }}" variant="secondary" size="sm">Detail</x-ui.button>
            <x-ui.button href="{{ route('admin.services.edit', $service) }}" variant="secondary" size="sm">Edit</x-ui.button>
            <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan ini?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger" size="sm" class="w-full">Hapus</x-ui.button>
            </form>
        </x-slot>
    </x-ui.mobile-data-card>
@endforeach
