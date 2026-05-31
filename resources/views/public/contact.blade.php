<x-public-layout>
    <section class="bg-white">
        <div class="pitstop-container py-12 text-center sm:py-16">
            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Hubungi Kami</span>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-blue-900 sm:text-4xl">Kontak PitStop Service Center</h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                Butuh informasi tambahan sebelum booking? Tim kami siap membantu selama jam operasional bengkel.
            </p>
        </div>
    </section>

    <section class="bg-slate-50 py-12 sm:py-16">
        <div class="pitstop-container grid gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <x-ui.card x-data="{ sent: false }">
                <h2 class="text-xl font-bold text-blue-900">Kirim Pesan</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Form ini merupakan simulasi UI dan tidak mengirim email sungguhan.</p>

                <x-ui.alert x-show="sent" x-cloak variant="success" class="mt-5">
                    Pesan simulasi berhasil dicatat. Tim PitStop akan menghubungi Anda pada implementasi produksi.
                </x-ui.alert>

                <form class="mt-5 space-y-4" x-on:submit.prevent="sent = true; $el.reset()">
                    <x-form.input label="Nama" name="contact_name" required minlength="3" placeholder="Nama lengkap" />
                    <x-form.input label="Email" name="contact_email" type="email" required placeholder="nama@example.com" />
                    <x-form.input label="Nomor Telepon" name="contact_phone" type="tel" required placeholder="08xxxxxxxxxx" />
                    <div>
                        <label for="contact_message" class="mb-1.5 block text-sm font-medium text-slate-700">Pesan <span class="text-red-500">*</span></label>
                        <textarea id="contact_message" name="contact_message" rows="5" required minlength="10" class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan pertanyaan Anda"></textarea>
                    </div>
                    <x-ui.button type="submit" class="w-full sm:w-auto">Kirim Pesan Simulasi</x-ui.button>
                </form>
            </x-ui.card>

            <div class="space-y-5">
                <x-ui.card>
                    <h2 class="text-lg font-bold text-blue-900">Informasi Bengkel</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="text-xs text-slate-400">Alamat</dt>
                            <dd class="mt-1 font-medium leading-6 text-slate-700">Jl. PitStop No. 88, Jakarta Selatan</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">Telepon</dt>
                            <dd class="mt-1 font-medium text-slate-700">(021) 555-0188</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">Email</dt>
                            <dd class="mt-1 break-all font-medium text-slate-700">hello@pitstop.example</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">Jam Operasional</dt>
                            <dd class="mt-1 font-medium text-slate-700">Setiap hari, 08:00-17:00 WIB</dd>
                        </div>
                    </dl>
                </x-ui.card>

                <div class="flex min-h-48 items-center justify-center rounded-2xl border border-slate-200 bg-blue-100 p-5 text-center shadow-sm">
                    <div>
                        <span class="mx-auto flex size-12 items-center justify-center rounded-xl bg-blue-600 text-sm font-bold text-white">PS</span>
                        <p class="mt-3 font-semibold text-blue-900">Lokasi PitStop</p>
                        <p class="mt-1 text-xs text-blue-700">Placeholder peta bengkel</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
