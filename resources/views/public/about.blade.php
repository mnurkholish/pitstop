<x-public-layout>
    <section class="bg-blue-900 py-14 text-center text-white sm:py-20">
        <div class="pitstop-container">
            <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-blue-100">Tentang PitStop</span>
            <h1 class="mx-auto mt-4 max-w-3xl text-3xl font-bold tracking-tight sm:text-4xl">Service Kendaraan Lebih Terencana, Transparan, dan Praktis</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-blue-200 sm:text-base">
                PitStop adalah sistem booking service bengkel yang menghubungkan pelanggan dan admin dalam satu alur kerja yang sederhana.
            </p>
        </div>
    </section>

    <section class="bg-white py-14 sm:py-20">
        <div class="pitstop-container grid gap-8 lg:grid-cols-2 lg:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Masalah yang Kami Selesaikan</p>
                <h2 class="mt-3 text-2xl font-bold text-blue-900 sm:text-3xl">Service bengkel tidak harus serba manual</h2>
                <p class="mt-4 text-sm leading-7 text-slate-500">
                    Antrean manual, pencatatan yang tidak rapi, dan estimasi biaya yang kurang transparan membuat proses service sulit direncanakan.
                </p>
            </div>
            <div class="grid gap-3">
                @foreach ([
                    ['Antre Manual', 'Pelanggan harus datang atau menghubungi bengkel hanya untuk memastikan jadwal.'],
                    ['Pencatatan Tidak Rapi', 'Data booking dan riwayat service mudah tercecer ketika dicatat secara terpisah.'],
                    ['Estimasi Tidak Transparan', 'Pelanggan kesulitan memperkirakan biaya dan waktu pengerjaan sejak awal.'],
                ] as [$title, $description])
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
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Solusi PitStop</p>
                <h2 class="mt-3 text-2xl font-bold text-blue-900 sm:text-3xl">Satu sistem untuk alur service yang lebih jelas</h2>
            </div>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Booking Online', 'Tentukan layanan, jadwal, dan slot bengkel dari dashboard pelanggan.'],
                    ['Estimasi Transparan', 'Lihat estimasi harga, durasi, dan jam selesai sebelum mengirim booking.'],
                    ['Riwayat Booking', 'Pantau status dan simpan histori service kendaraan dalam satu akun.'],
                    ['Dashboard Admin', 'Kelola layanan, booking aktif, status pengerjaan, dan riwayat operasional.'],
                ] as [$title, $description])
                    <x-ui.card>
                        <span class="flex size-10 items-center justify-center rounded-xl bg-blue-100 text-xs font-bold text-blue-700">PS</span>
                        <h3 class="mt-4 font-semibold text-slate-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-14 sm:py-20">
        <div class="pitstop-container grid gap-5 lg:grid-cols-2">
            <x-ui.card>
                <h2 class="text-xl font-bold text-blue-900">Manfaat untuk Pelanggan</h2>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-500">
                    <li>Booking service tanpa antre manual.</li>
                    <li>Estimasi biaya dan waktu terlihat sejak awal.</li>
                    <li>Status dan riwayat booking mudah dipantau.</li>
                    <li>Jadwal service kendaraan lebih mudah direncanakan.</li>
                </ul>
            </x-ui.card>
            <x-ui.card>
                <h2 class="text-xl font-bold text-blue-900">Manfaat untuk Admin</h2>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-500">
                    <li>Data layanan dan booking tersimpan terpusat.</li>
                    <li>Booking aktif dan riwayat final terpisah dengan jelas.</li>
                    <li>Status pengerjaan dapat diperbarui sesuai alur kerja.</li>
                    <li>Statistik operasional mudah dipantau dari dashboard.</li>
                </ul>
            </x-ui.card>
        </div>
    </section>

    <section class="bg-blue-900 py-14 text-center text-white sm:py-16">
        <div class="pitstop-container">
            <h2 class="text-2xl font-bold">Mulai rencanakan service kendaraanmu</h2>
            <p class="mt-2 text-sm text-blue-200">Daftar gratis atau masuk untuk membuat booking melalui dashboard pelanggan.</p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <x-ui.button href="{{ route('register') }}">Daftar Sekarang</x-ui.button>
                <x-ui.button href="{{ route('login') }}" variant="secondary">Masuk</x-ui.button>
            </div>
        </div>
    </section>
</x-public-layout>
