<x-public-layout>
    <section class="pitstop-container py-16 sm:py-24">
        <x-ui.card class="mx-auto max-w-2xl text-center">
            <h1 class="text-3xl font-bold text-blue-900">{{ $title }}</h1>
            <p class="mt-3 leading-7 text-slate-500">{{ $description }}</p>
            <x-ui.button href="{{ route('home') }}" variant="secondary" class="mt-6">Kembali ke Beranda</x-ui.button>
        </x-ui.card>
    </section>
</x-public-layout>
