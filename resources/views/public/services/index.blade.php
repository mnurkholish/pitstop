<x-public-layout>
    <section class="bg-white">
        <div class="pitstop-container py-12 text-center sm:py-16">
            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Layanan PitStop</span>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-blue-900 sm:text-4xl">Perawatan Kendaraan yang Transparan</h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                Pilih layanan aktif sesuai kebutuhan kendaraanmu. Harga dan durasi ditampilkan sejak awal agar jadwal service lebih terencana.
            </p>
        </div>
    </section>

    <section
        class="bg-slate-50 py-12 sm:py-16"
        x-data="publicServiceCatalog({
            searchUrl: @js(route('services.search')),
            detailUrl: @js(route('services.show', ['service' => '__SERVICE__'])),
        })"
    >
        <div class="pitstop-container">
            <x-ui.card>
                <label>
                    <span class="sr-only">Cari layanan</span>
                    <input
                        type="search"
                        x-model="search"
                        @input.debounce.350ms="fetchServices"
                        placeholder="Cari nama layanan atau harga..."
                        class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                    >
                </label>
            </x-ui.card>

            <x-ui.alert x-show="error" x-cloak variant="danger" class="mt-5">
                Gagal memuat layanan. Silakan coba lagi.
            </x-ui.alert>

            <div x-show="loading" x-cloak class="mt-5 rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center shadow-sm">
                <p class="text-sm font-medium text-blue-700">Memuat layanan...</p>
            </div>

            <div x-show="! loading && ! error" class="mt-5" x-on:click="handleCatalogClick($event)">
                <div x-show="count === 0" x-cloak x-html="emptyHtml"></div>
                <div x-show="count > 0" x-html="cardsHtml" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @include('public.services.partials.cards', ['services' => $services])
                </div>
            </div>

            <div class="mt-10 rounded-2xl bg-blue-900 px-5 py-7 text-center text-white sm:px-8">
                <h2 class="text-xl font-bold">Sudah menemukan layanan yang dibutuhkan?</h2>
                <p class="mt-2 text-sm text-blue-200">Masuk untuk memilih jadwal dan membuat booking service kendaraanmu.</p>
                <x-ui.button href="{{ route('login', ['redirect' => '/dashboard']) }}" class="mt-5">Booking Sekarang</x-ui.button>
            </div>
        </div>

        <x-ui.modal name="public-service-detail" title="Detail Layanan" max-width="lg">
            <template x-if="detailLoading">
                <p class="text-sm text-slate-500">Memuat detail layanan...</p>
            </template>
            <template x-if="detailError">
                <x-ui.alert variant="danger">Gagal memuat detail layanan.</x-ui.alert>
            </template>
            <template x-if="detail && ! detailLoading">
                <div>
                    <template x-if="detail.image_url">
                        <img :src="detail.image_url" :alt="detail.name" class="h-52 w-full rounded-xl object-cover sm:h-64">
                    </template>
                    <template x-if="! detail.image_url">
                        <div class="flex h-52 items-center justify-center rounded-xl bg-blue-50 sm:h-64">
                            <span class="flex size-16 items-center justify-center rounded-2xl bg-blue-600 text-lg font-bold text-white">PS</span>
                        </div>
                    </template>
                    <div class="mt-5 flex items-start justify-between gap-3">
                        <h2 class="text-xl font-bold text-blue-900" x-text="detail.name"></h2>
                        <x-ui.badge variant="active">Aktif</x-ui.badge>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-slate-500" x-text="detail.description"></p>
                    <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <dt class="text-xs text-slate-400">Estimasi Harga</dt>
                            <dd class="mt-1 font-semibold text-blue-900" x-text="detail.price"></dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <dt class="text-xs text-slate-400">Estimasi Durasi</dt>
                            <dd class="mt-1 font-semibold text-slate-700" x-text="`${detail.duration_minutes} menit`"></dd>
                        </div>
                    </dl>
                    <x-ui.button href="{{ route('login', ['redirect' => '/dashboard']) }}" class="mt-5 w-full">Booking Layanan Ini</x-ui.button>
                </div>
            </template>
        </x-ui.modal>
    </section>

    <script>
        function publicServiceCatalog(config) {
            return {
                search: '',
                loading: false,
                error: false,
                count: @js($services->count()),
                cardsHtml: @js(view('public.services.partials.cards', ['services' => $services])->render()),
                emptyHtml: @js(view('public.services.partials.empty')->render()),
                detail: null,
                detailLoading: false,
                detailError: false,
                async fetchServices() {
                    this.loading = true;
                    this.error = false;

                    try {
                        const query = new URLSearchParams({ search: this.search });
                        const response = await fetch(`${config.searchUrl}?${query}`, {
                            headers: { Accept: 'application/json' },
                        });

                        if (! response.ok) {
                            throw new Error('Request gagal');
                        }

                        const payload = await response.json();
                        this.count = payload.count;
                        this.cardsHtml = payload.cards;
                        this.emptyHtml = payload.empty;
                    } catch (error) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },
                handleCatalogClick(event) {
                    const button = event.target.closest('[data-service-action="detail"]');

                    if (button) {
                        this.openDetail(Number(button.dataset.serviceId));
                    }
                },
                async openDetail(serviceId) {
                    this.detail = null;
                    this.detailError = false;
                    this.detailLoading = true;
                    this.$dispatch('open-modal', 'public-service-detail');

                    try {
                        const response = await fetch(config.detailUrl.replace('__SERVICE__', serviceId), {
                            headers: { Accept: 'application/json' },
                        });

                        if (! response.ok) {
                            throw new Error('Request gagal');
                        }

                        const payload = await response.json();
                        this.detail = payload.service;
                    } catch (error) {
                        this.detailError = true;
                    } finally {
                        this.detailLoading = false;
                    }
                },
            };
        }
    </script>
</x-public-layout>
