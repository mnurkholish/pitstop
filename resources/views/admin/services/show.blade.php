<x-admin-layout>
    <div class="pitstop-container py-8 sm:py-10">
        <x-ui.page-header title="Detail Layanan" description="Informasi layanan bengkel.">
            <x-slot name="actions">
                <x-ui.button href="{{ route('admin.services.edit', $service) }}">Edit Layanan</x-ui.button>
            </x-slot>
        </x-ui.page-header>
        <x-ui.card class="mt-6">
            <x-ui.service-image :service="$service" size="detail" />
            <div class="mt-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-blue-900">{{ $service->name }}</h2>
                    <p class="mt-2 leading-7 text-slate-500">{{ $service->description ?: 'Belum ada deskripsi layanan.' }}</p>
                </div>
                <x-ui.badge :variant="$service->is_active ? 'active' : 'inactive'">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
            </div>
            <dl class="mt-6 grid gap-3 sm:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs text-slate-400">Estimasi Harga</dt>
                    <dd class="mt-1 font-semibold text-slate-700">Rp {{ number_format($service->price, 0, ',', '.') }}</dd>
                </div>
                <div class="rounded-xl bg-slate-50 p-4">
                    <dt class="text-xs text-slate-400">Estimasi Durasi</dt>
                    <dd class="mt-1 font-semibold text-slate-700">{{ $service->duration_minutes }} menit</dd>
                </div>
            </dl>
            <div class="mt-6">
                <x-ui.button href="{{ route('admin.services.index') }}" variant="secondary">Kembali</x-ui.button>
            </div>
        </x-ui.card>
    </div>
</x-admin-layout>
