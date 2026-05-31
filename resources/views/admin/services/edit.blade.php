<x-admin-layout>
    <div class="pitstop-container py-8 sm:py-10">
        <x-ui.page-header title="Edit Layanan" description="Perbarui informasi layanan bengkel." />
        <x-ui.card class="mt-6">
            <form method="POST" action="{{ route('admin.services.update', $service) }}">
                @csrf
                @method('PUT')
                @include('admin.services.form', ['service' => $service])
                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <x-ui.button href="{{ route('admin.services.index') }}" variant="secondary">Batal</x-ui.button>
                    <x-ui.button type="submit">Simpan Perubahan</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-admin-layout>
