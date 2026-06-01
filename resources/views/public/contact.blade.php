<x-public-layout>
    <section class="bg-white">
        <div class="pitstop-container py-12 text-center sm:py-16">
            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Hubungi
                Kami</span>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-blue-900 sm:text-4xl">Kontak PitStop Service Center
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                Punya pertanyaan? Hubungi kami melalui kontak di bawah ini.
            </p>
        </div>
    </section>

    <section class="bg-slate-50 py-12 sm:py-16">
        <div class="pitstop-container grid gap-5 lg:grid-cols-2">
            <x-ui.card>
                <h2 class="text-lg font-bold text-blue-900">Informasi Kontak</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Alamat</dt>
                        <dd class="mt-1 font-medium leading-6 text-slate-700">Jl. Kalimantan, Sumber Sari, Jember</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Telepon</dt>
                        <dd class="mt-1 font-medium text-slate-700">08123456789</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Email</dt>
                        <dd class="mt-1 break-all font-medium text-slate-700">pitstop@gmail.com</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Jam Operasional</dt>
                        <dd class="mt-1 font-medium text-slate-700">Setiap hari, 08:00-17:00 WIB</dd>
                    </div>
                </dl>
            </x-ui.card>

            <div
                class="flex min-h-64 items-center justify-center rounded-2xl border border-slate-200 bg-blue-100 p-5 text-center shadow-sm">
                <div>
                    <span
                        class="mx-auto flex size-12 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white">PS</span>
                    <p class="mt-3 font-semibold text-blue-900">Lokasi PitStop</p>
                    <p class="mt-1 text-xs text-blue-700">Jl. PitStop No. 88, Jakarta Selatan</p>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
