<x-ui.empty-state title="Layanan tidak ditemukan" description="Coba ubah kata kunci atau filter status.">
    <x-slot name="action">
        <x-ui.button href="{{ route('admin.services.create') }}" size="sm">Tambah Layanan</x-ui.button>
    </x-slot>
</x-ui.empty-state>
