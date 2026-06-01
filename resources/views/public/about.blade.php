<x-public-layout>
    <section class="bg-blue-900 py-14 text-center text-white sm:py-20">
        <div class="pitstop-container">
            <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-blue-100">Tentang
                PitStop</span>
            <h1 class="mx-auto mt-4 max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl">Service Kendaraan Lebih
                Mudah, Cepat, dan Praktis</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-blue-200 sm:text-base">
                PitStop membantu anda membuat jadwal service dengan mudah dan praktis.
            </p>
        </div>
    </section>

    <section class="bg-white py-14 sm:py-20">
        <div class="pitstop-container grid gap-8 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Masalah yang Kami Selesaikan</p>
                <h2 class="mt-3 text-2xl font-bold text-blue-900 sm:text-3xl">Service kendaraan tidak harus serba manual
                </h2>
                <p class="mt-4 text-sm leading-7 text-slate-500">
                    Antrean manual, catatan yang terpisah, dan perkiraan biaya yang belum jelas membuat service
                    kendaraan sulit direncanakan.
                </p>
            </div>
            <div class="grid gap-3">
                @foreach ([['Antre Manual', 'Pelanggan sering harus datang atau menghubungi bengkel lebih dulu untuk memastikan jadwal.'], ['Pencatatan Tidak Rapi', 'Data booking dan riwayat service bisa sulit dicari jika dicatat secara terpisah.'], ['Estimasi Tidak Transparan', 'Pelanggan sering belum mengetahui perkiraan biaya dan waktu pengerjaan sejak awal.']] as [$title, $description])
                    <x-ui.card>
                        <h3 class="font-semibold text-slate-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-14 sm:py-20">
        <div class="pitstop-container">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Solusi Kami</p>
                <h2 class="mt-3 text-2xl font-bold text-blue-900 sm:text-3xl">Untuk Anda</h2>
            </div>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Booking Online', 'Pilih layanan, tentukan jadwal, dan buat booking dengan lebih mudah.'], ['Estimasi Transparan', 'Lihat perkiraan harga, durasi, dan waktu selesai sebelum booking dikirim.'], ['Riwayat Booking', 'Status dan riwayat service kendaraan tersimpan dalam akun kamu.'], ['Dashboard Admin', 'Layanan, booking, dan riwayat service dapat dipantau dalam satu tempat.']] as [$title, $description])
                    <x-ui.card>
                        <h3 class="font-semibold text-slate-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-blue-900 py-14 text-center text-white sm:py-16">
        <div class="pitstop-container">
            <h2 class="text-2xl font-bold">Mulai rencanakan service kendaraanmu</h2>
            <p class="mt-2 text-sm text-blue-200">Daftar atau masuk untuk mulai membuat booking service kendaraan.</p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <x-ui.button href="{{ route('register') }}">Daftar Sekarang</x-ui.button>
                <x-ui.button href="{{ route('login') }}" variant="secondary">Masuk</x-ui.button>
            </div>
        </div>
    </section>
</x-public-layout>
