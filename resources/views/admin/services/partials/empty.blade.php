<x-ui.empty-state title="Layanan tidak dapat ditemukan"
    description="Tidak ada layanan yang cocok. Coba ubah kata kunci lain">
    <x-slot name="action">
        <x-ui.button href="{{ route('admin.services.create') }}" size="sm">Tambah Layanan</x-ui.button>
    </x-slot>
</x-ui.empty-state>
