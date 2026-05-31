<x-ui.empty-state
    title="Booking tidak ditemukan"
    description="Coba ubah kata kunci atau filter status."
>
    <x-slot name="action">
        <x-ui.button href="{{ route('dashboard') }}" size="sm">Buat Booking Baru</x-ui.button>
    </x-slot>
</x-ui.empty-state>
