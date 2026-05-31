@foreach ($services as $service)
    <tr>
        <td class="px-4 py-3">
            <div class="flex items-center gap-3">
                <x-ui.service-image :service="$service" size="thumbnail" />
                <div class="min-w-0">
                    <p class="font-semibold text-slate-700">{{ $service->name }}</p>
                    <p class="mt-1 max-w-md truncate text-xs text-slate-400">{{ $service->description ?: 'Tanpa deskripsi' }}</p>
                </div>
            </div>
        </td>
        <td class="px-4 py-3 font-semibold text-slate-700">Rp {{ number_format($service->price, 0, ',', '.') }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $service->duration_minutes }} menit</td>
        <td class="px-4 py-3"><x-ui.badge :variant="$service->is_active ? 'active' : 'inactive'">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
        <td class="px-4 py-3">
            <div class="flex flex-wrap gap-2">
                <x-ui.button href="{{ route('admin.services.show', $service) }}" variant="secondary" size="sm">Detail</x-ui.button>
                <x-ui.button href="{{ route('admin.services.edit', $service) }}" variant="secondary" size="sm">Edit</x-ui.button>
                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Hapus layanan ini?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger" size="sm">Hapus</x-ui.button>
                </form>
            </div>
        </td>
    </tr>
@endforeach
