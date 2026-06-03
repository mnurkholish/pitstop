<x-public-layout>
    <section class="bg-white">
        <div class="pitstop-container grid gap-10 py-12 sm:py-16 lg:grid-cols-2 lg:items-center lg:py-20">
            <div>
                <span
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                    Bengkel Modern, Booking Mudah
                </span>
                <h1 class="mt-5 text-4xl font-bold tracking-tight text-blue-900 sm:text-5xl">
                    Service Kendaraan
                    <span class="block text-blue-600">Tanpa Antre</span>
                </h1>
                <p class="mt-4 max-w-xl text-base leading-7 text-slate-500">
                    Pilih layanan, tentukan jadwal, dan lihat progress service kendaraanmu dengan mudah.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <x-ui.button href="{{ route('login', ['redirect' => '/dashboard']) }}" size="lg">
                        Booking Service Sekarang
                    </x-ui.button>
                    <x-ui.button href="{{ route('services.index') }}" variant="secondary" size="lg">
                        Lihat Layanan
                    </x-ui.button>
                </div>
                <div class="mt-8 grid grid-cols-3 divide-x divide-slate-200">
                    <div class="pr-3">
                        <p class="text-xl font-bold text-blue-900">1.200+</p>
                        <p class="mt-1 text-xs text-slate-400">Booking Selesai</p>
                    </div>
                    <div class="px-3 text-center">
                        <p class="text-xl font-bold text-blue-900">98%</p>
                        <p class="mt-1 text-xs text-slate-400">Kepuasan Pelanggan</p>
                    </div>
                    <div class="pl-3 text-right">
                        <p class="text-xl font-bold text-blue-900">{{ $activeServiceCount }}</p>
                        <p class="mt-1 text-xs text-slate-400">Jenis Layanan</p>
                    </div>
                </div>
                <div id="jember-weather-widget" hidden class="mt-5 max-w-xs rounded-xl border border-blue-100 bg-blue-50 px-3 py-2.5 text-blue-900">
                    <p class="text-[10px] font-semibold uppercase text-blue-600">Cuaca</p>
                    <div class="mt-1 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p data-weather-city class="truncate text-xs font-semibold text-blue-900">Jember</p>
                            <p data-weather-desc class="truncate text-[11px] text-blue-700"></p>
                        </div>
                        <p data-weather-temp class="shrink-0 text-lg font-bold text-blue-900"></p>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-blue-100 p-5 sm:p-8">
                <img src="{{ asset('images/hero.png') }}" alt="PitStop Service Center" width="612" height="408"
                    class="aspect-[3/2] w-full rounded-2xl object-cover">
                <div class="absolute bottom-3 left-3 rounded-xl bg-white px-4 py-3 shadow-lg sm:bottom-5 sm:left-5">
                    <p class="text-xs font-semibold text-emerald-700">Booking Praktis</p>
                    <p class="mt-1 text-xs text-slate-400">Jadwal service tersusun rapi</p>
                </div>
                <div class="absolute right-3 top-3 rounded-xl bg-white px-4 py-3 shadow-lg sm:right-5 sm:top-5">
                    <p class="text-xs text-slate-400">Tanpa Ribet</p>
                    <p class="mt-1 text-sm font-bold text-blue-900">Langsung Gercep</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-14 sm:py-20">
        <div class="pitstop-container">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-2xl font-bold text-blue-900 sm:text-3xl">Layanan Unggulan</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Pilihan perawatan kendaraan.</p>
            </div>

            @if ($services->isEmpty())
                <x-ui.empty-state class="mt-8" title="Layanan belum tersedia"
                    description="Layanan akan segera ditampilkan." />
            @else
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($services as $service)
                        <x-ui.service-card :service="$service" />
                    @endforeach
                </div>
            @endif

            <div class="mt-8 text-center">
                <x-ui.button href="{{ route('services.index') }}" variant="secondary">Lihat Semua Layanan</x-ui.button>
            </div>
        </div>
    </section>

    <section class="bg-white py-14 sm:py-20">
        <div class="pitstop-container">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-2xl font-bold text-blue-900 sm:text-3xl">Kenapa Pilih PitStop?</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Service kendaraan jadi lebih mudah, rapi, dan nyaman
                    untuk direncanakan.</p>
            </div>
            <div class="mt-9 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Booking Online', 'Buat jadwal service tanpa harus antre langsung di bengkel.'], ['Harga Transparan', 'Perkiraan biaya bisa dilihat sebelum datang ke bengkel.'], ['Estimasi Durasi', 'Perkiraan waktu pengerjaan membantu kamu mengatur jadwal dengan lebih baik.'], ['Riwayat Tersimpan', 'Riwayat service kendaraan tersimpan rapi di akun kamu.']] as [$title, $description])
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5 text-center">
                        <h3 class="font-semibold text-slate-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-14 sm:py-20">
        <div class="pitstop-container">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-2xl font-bold text-blue-900 sm:text-3xl">Cara Kerja PitStop</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Booking service kendaraan bisa dilakukan dalam tiga
                    langkah sederhana.</p>
            </div>
            <div class="mt-9 grid gap-5 lg:grid-cols-3">
                @foreach ([['01', 'Buat Akun', 'Daftar dengan email untuk mulai menggunakan PitStop.'], ['02', 'Pilih Layanan & Jadwal', 'Pilih layanan yang dibutuhkan, lalu tentukan tanggal dan jam kedatangan.'], ['03', 'Datang & Selesai', 'Datang sesuai jadwal dan serahkan kendaraan untuk dikerjakan.']] as [$number, $title, $description])
                    <x-ui.card class="relative">
                        <span
                            class="absolute -top-3 left-5 rounded-full bg-blue-600 px-2.5 py-1 text-xs font-bold text-white">{{ $number }}</span>
                        <h3 class="mt-2 font-semibold text-slate-800">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-blue-900 py-14 text-center text-white sm:py-16">
        <div class="pitstop-container">
            <h2 class="text-2xl font-bold sm:text-3xl">Siap service kendaraanmu?</h2>
            <p class="mt-2 text-sm text-blue-200">Buat akun dan mulai jadwalkan service kendaraanmu.</p>
            <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                <x-ui.button href="{{ route('register') }}" size="lg">Daftar Sekarang</x-ui.button>
                <x-ui.button href="{{ route('login') }}" variant="secondary" size="lg">Masuk</x-ui.button>
            </div>
        </div>
    </section>

    <p class="sr-only">Kunjungan beranda dalam sesi ini: {{ $visitCount }}</p>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const widget = document.getElementById('jember-weather-widget');

            if (! widget) {
                return;
            }

            fetch('https://wttr.in/Jember?format=j1')
                .then((response) => {
                    if (! response.ok) {
                        throw new Error('Cuaca tidak tersedia');
                    }

                    return response.json();
                })
                .then((payload) => {
                    const current = payload.current_condition?.[0];
                    const city = payload.nearest_area?.[0]?.areaName?.[0]?.value || 'Jember';
                    const temperature = current?.temp_C;
                    const description = current?.weatherDesc?.[0]?.value;

                    if (temperature === undefined || temperature === null || ! description) {
                        return;
                    }

                    widget.querySelector('[data-weather-city]').textContent = city;
                    widget.querySelector('[data-weather-temp]').textContent = `${temperature}°C`;
                    widget.querySelector('[data-weather-desc]').textContent = description;
                    widget.hidden = false;
                })
                .catch(() => {
                    widget.hidden = true;
                });
        });
    </script>
    @endpush
</x-public-layout>
