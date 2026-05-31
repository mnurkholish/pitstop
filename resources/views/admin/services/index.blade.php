<x-admin-layout>
    <div
        class="pitstop-container py-8 sm:py-10"
        x-data="serviceList({
            searchUrl: @js(route('admin.services.search')),
        })"
    >
        <x-ui.page-header title="Kelola Layanan" description="Tambah, ubah, dan kelola layanan bengkel yang tersedia untuk pelanggan.">
            <x-slot name="actions">
                <x-ui.button href="{{ route('admin.services.create') }}">Tambah Layanan</x-ui.button>
            </x-slot>
        </x-ui.page-header>

        @if (session('success'))
            <x-ui.alert variant="success" class="mt-5">{{ session('success') }}</x-ui.alert>
        @endif
        @if (session('error'))
            <x-ui.alert variant="danger" class="mt-5">{{ session('error') }}</x-ui.alert>
        @endif

        <x-ui.card class="mt-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <label class="flex-1">
                    <span class="sr-only">Cari layanan</span>
                    <input
                        type="search"
                        x-model="search"
                        @input.debounce.350ms="fetchServices()"
                        placeholder="Cari nama, harga, durasi, atau status..."
                        class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                    >
                </label>
                <div class="flex gap-2 overflow-x-auto pb-1 lg:pb-0">
                    @foreach (['' => 'Semua', 'active' => 'Aktif', 'inactive' => 'Nonaktif'] as $value => $label)
                        <button
                            type="button"
                            @click="status = @js($value); fetchServices()"
                            :class="status === @js($value) ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                            class="whitespace-nowrap rounded-lg border px-3 py-2 text-xs font-semibold transition"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </x-ui.card>

        <section class="mt-5">
            <x-ui.alert x-show="error" x-cloak variant="danger" class="mb-4">
                Gagal memuat layanan. Silakan coba lagi.
            </x-ui.alert>
            <div x-show="loading" x-cloak class="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center shadow-sm">
                <p class="text-sm font-medium text-blue-700">Memuat data layanan...</p>
            </div>
            <div x-show="! loading && ! error">
                <div x-show="count === 0" x-cloak x-html="emptyHtml"></div>
                <x-ui.responsive-table x-show="count > 0">
                    <x-slot name="desktop">
                        <thead class="bg-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Layanan</th>
                                <th class="px-4 py-3">Harga</th>
                                <th class="px-4 py-3">Durasi</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-html="desktopHtml" class="divide-y divide-slate-200">
                            @include('admin.services.partials.desktop-rows', ['services' => $services])
                        </tbody>
                    </x-slot>
                    <x-slot name="mobile">
                        <div x-html="mobileHtml">
                            @include('admin.services.partials.mobile-cards', ['services' => $services])
                        </div>
                    </x-slot>
                </x-ui.responsive-table>
                <div x-html="paginationHtml" class="mt-4">
                    @include('admin.services.partials.pagination', ['services' => $services])
                </div>
            </div>
        </section>
    </div>

    <script>
        function serviceList(config) {
            return {
                search: '',
                status: '',
                loading: false,
                error: false,
                count: @js($services->total()),
                desktopHtml: @js(view('admin.services.partials.desktop-rows', ['services' => $services])->render()),
                mobileHtml: @js(view('admin.services.partials.mobile-cards', ['services' => $services])->render()),
                paginationHtml: @js(view('admin.services.partials.pagination', ['services' => $services])->render()),
                emptyHtml: @js(view('admin.services.partials.empty')->render()),
                async fetchServices(pageUrl = config.searchUrl) {
                    this.loading = true;
                    this.error = false;

                    try {
                        const requestedUrl = new URL(pageUrl, window.location.origin);
                        const url = new URL(config.searchUrl, window.location.origin);

                        if (requestedUrl.searchParams.has('page')) {
                            url.searchParams.set('page', requestedUrl.searchParams.get('page'));
                        }

                        url.searchParams.set('search', this.search);
                        url.searchParams.set('status', this.status);
                        const response = await fetch(url, { headers: { Accept: 'application/json' } });

                        if (! response.ok) {
                            throw new Error('Request gagal');
                        }

                        const payload = await response.json();
                        this.count = payload.count;
                        this.desktopHtml = payload.desktop;
                        this.mobileHtml = payload.mobile;
                        this.paginationHtml = payload.pagination;
                        this.emptyHtml = payload.emptyHtml;
                    } catch (error) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-admin-layout>
