<x-admin-layout>
    <div class="pitstop-container py-8 sm:py-10" x-data="adminBookingHistory({
        searchUrl: @js(route('admin.bookings.history.search')),
        detailUrl: @js(route('admin.bookings.history.show', ['booking' => '__BOOKING__'])),
    })">
        <x-ui.page-header title="Riwayat Booking" description="Lihat riwayat booking pelanggan." />

        <div class="mt-7 grid grid-cols-3 gap-3">
            @foreach ([['Total Riwayat', $summary['total'], 'bg-blue-100 text-blue-700'], ['Selesai', $summary['completed'], 'bg-emerald-100 text-emerald-700'], ['Dibatalkan', $summary['cancelled'], 'bg-red-100 text-red-700']] as [$label, $value, $color])
                <x-ui.card class="flex items-center gap-3 p-4">
                    <span
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $color }}">PS</span>
                    <div>
                        <p class="text-2xl font-bold text-blue-900">{{ $value }}</p>
                        <p class="text-xs text-slate-500">{{ $label }}</p>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        <x-ui.card class="mt-6">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_12rem] lg:items-center">
                <label>
                    <span class="sr-only">Cari riwayat booking</span>
                    <input type="search" x-model="search" @input.debounce.350ms="fetchBookings"
                        placeholder="Cari kode, pelanggan, plat nomor, layanan, atau status..."
                        class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500">
                </label>
                <label>
                    <span class="sr-only">Filter tanggal riwayat booking</span>
                    <input type="date" x-model="date" @change="fetchBookings"
                        class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </label>
            </div>
            <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                @foreach (['' => 'Semua', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $value => $label)
                    <button type="button" @click="status = @js($value); fetchBookings()"
                        :class="status === @js($value) ? 'border-blue-600 bg-blue-600 text-white' :
                            'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                        class="whitespace-nowrap rounded-lg border px-3 py-2 text-xs font-semibold transition">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </x-ui.card>

        <section class="mt-5">
            <x-ui.alert x-show="error" x-cloak variant="danger" class="mb-4">
                Gagal memuat riwayat booking. Silakan coba lagi.
            </x-ui.alert>
            <div x-show="loading" x-cloak
                class="rounded-2xl border border-slate-200 bg-white px-6 py-10 text-center shadow-sm">
                <p class="text-sm font-medium text-blue-700">Memuat riwayat booking...</p>
            </div>
            <div x-show="! loading && ! error" x-on:click="handleListClick($event)">
                <div x-show="count === 0" x-cloak x-html="emptyHtml"></div>
                <x-ui.responsive-table x-show="count > 0">
                    <x-slot name="desktop">
                        <thead class="bg-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Kode Booking</th>
                                <th class="px-4 py-3">Pelanggan</th>
                                <th class="px-4 py-3">Nomor Plat</th>
                                <th class="px-4 py-3">Jadwal</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-html="desktopHtml" class="divide-y divide-slate-200">
                            @include('admin.bookings.history.partials.desktop-rows', [
                                'bookings' => $bookings,
                            ])
                        </tbody>
                    </x-slot>
                    <x-slot name="mobile">
                        <div x-html="mobileHtml">
                            @include('admin.bookings.history.partials.mobile-cards', [
                                'bookings' => $bookings,
                            ])
                        </div>
                    </x-slot>
                </x-ui.responsive-table>
            </div>
        </section>

        <x-ui.modal name="admin-booking-history-detail" title="Detail Riwayat Booking" max-width="lg">
            <template x-if="detailLoading">
                <p class="text-sm text-slate-500">Memuat detail riwayat booking...</p>
            </template>
            <template x-if="detailError">
                <x-ui.alert variant="danger">Gagal memuat detail riwayat booking.</x-ui.alert>
            </template>
            <template x-if="detail && ! detailLoading">
                <div>
                    <div class="flex items-start justify-between gap-4 rounded-xl bg-slate-100 p-4">
                        <div>
                            <p class="text-xs text-slate-400">Kode Booking</p>
                            <p class="mt-1 font-bold text-blue-700" x-text="detail.booking_code"></p>
                        </div>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold"
                            :class="badgeClass(detail.status_variant)" x-text="detail.status_label"></span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <template x-for="[label, value] in detailFields" :key="label">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <dt class="text-xs text-slate-400" x-text="label"></dt>
                                <dd class="mt-1 font-medium text-slate-700" x-text="value"></dd>
                            </div>
                        </template>
                    </dl>
                    <div class="mt-5">
                        <h3 class="text-sm font-semibold text-slate-700">Layanan</h3>
                        <template x-for="service in detail.services" :key="service.name">
                            <div class="mt-2 flex justify-between gap-3 rounded-xl border border-slate-200 p-3 text-sm">
                                <span>
                                    <span class="block font-medium text-slate-700" x-text="service.name"></span>
                                    <span class="text-xs text-slate-400"
                                        x-text="`${service.duration_minutes} menit`"></span>
                                </span>
                                <span class="font-semibold text-slate-700" x-text="service.price"></span>
                            </div>
                        </template>
                    </div>
                    <div class="mt-5 rounded-xl bg-slate-50 p-3 text-sm">
                        <p class="text-xs text-slate-400">Pesan Pelanggan</p>
                        <p class="mt-1 text-slate-700" x-text="detail.notes"></p>
                    </div>
                    <template x-if="detail.cancel_reason">
                        <div class="mt-3 rounded-xl bg-red-50 p-3 text-sm">
                            <p class="text-xs text-red-500">Alasan Pembatalan</p>
                            <p class="mt-1 text-red-700" x-text="detail.cancel_reason"></p>
                        </div>
                    </template>
                </div>
            </template>
        </x-ui.modal>
    </div>

    @push('scripts')
    <script>
        function adminBookingHistory(config) {
            return {
                search: '',
                status: '',
                date: '',
                loading: false,
                error: false,
                count: @js($bookings->count()),
                desktopHtml: @js(view('admin.bookings.history.partials.desktop-rows', ['bookings' => $bookings])->render()),
                mobileHtml: @js(view('admin.bookings.history.partials.mobile-cards', ['bookings' => $bookings])->render()),
                emptyHtml: @js(view('admin.bookings.history.partials.empty')->render()),
                detail: null,
                detailLoading: false,
                detailError: false,
                async fetchBookings() {
                    this.loading = true;
                    this.error = false;

                    try {
                        const query = new URLSearchParams({
                            search: this.search,
                            status: this.status,
                            date: this.date,
                        });
                        const response = await fetch(`${config.searchUrl}?${query}`, {
                            headers: {
                                Accept: 'application/json'
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Request gagal');
                        }

                        const payload = await response.json();
                        this.count = payload.count;
                        this.desktopHtml = payload.desktop;
                        this.mobileHtml = payload.mobile;
                        this.emptyHtml = payload.empty;
                    } catch (error) {
                        this.error = true;
                    } finally {
                        this.loading = false;
                    }
                },
                handleListClick(event) {
                    const button = event.target.closest('[data-booking-action="detail"]');

                    if (button) {
                        this.openDetail(Number(button.dataset.bookingId));
                    }
                },
                async openDetail(bookingId) {
                    this.detail = null;
                    this.detailError = false;
                    this.detailLoading = true;
                    this.$dispatch('open-modal', 'admin-booking-history-detail');

                    try {
                        const url = config.detailUrl.replace('__BOOKING__', bookingId);
                        const response = await fetch(url, {
                            headers: {
                                Accept: 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Request gagal');
                        }

                        const payload = await response.json();
                        this.detail = payload.booking;
                    } catch (error) {
                        this.detailError = true;
                    } finally {
                        this.detailLoading = false;
                    }
                },
                get detailFields() {
                    if (!this.detail) {
                        return [];
                    }

                    return [
                        ['Nama Pelanggan', this.detail.customer_name],
                        ['Nomor Plat', this.detail.plate_number],
                        ['Jenis Kendaraan', this.detail.vehicle_type],
                        ['Merek / Seri', this.detail.vehicle_model],
                        ['Jadwal Mulai', this.detail.start_time],
                        ['Estimasi Selesai', this.detail.end_time],
                        ['Slot Bengkel', `Slot ${this.detail.slot}`],
                        ['Total Harga', this.detail.total_price],
                        ['Total Durasi', `${this.detail.total_duration_minutes} menit`],
                        ['Selesai Pada', this.detail.completed_at],
                    ];
                },
                badgeClass(variant) {
                    return {
                        success: 'bg-emerald-100 text-emerald-700',
                        danger: 'bg-red-100 text-red-700',
                    } [variant] || 'bg-slate-100 text-slate-600';
                },
            };
        }
    </script>
    @endpush
</x-admin-layout>
