<x-user-layout>
    <div
        class="pitstop-container py-8 sm:py-10"
        x-data="bookingEstimator(
            @js($services->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'duration' => $service->duration_minutes,
            ])->values()),
            @js(old('services', [])),
            @js(old('arrival_time', '')),
        )"
    >
        <section>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-blue-900">Halo, {{ Auth::user()->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">Kelola booking service kendaraanmu dari satu tempat.</p>
                </div>
                <x-ui.button href="{{ url('/my-bookings') }}" variant="secondary" size="sm">Booking Saya</x-ui.button>
            </div>

            <div class="mt-7 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['Total Booking Saya', $summary['total'], 'bg-blue-100 text-blue-700'],
                    ['Menunggu', $summary['pending'], 'bg-amber-100 text-amber-700'],
                    ['Diproses', $summary['processing'], 'bg-blue-100 text-blue-700'],
                    ['Selesai', $summary['completed'], 'bg-emerald-100 text-emerald-700'],
                ] as [$label, $value, $color])
                    <x-ui.card class="flex items-center gap-3 p-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $color }}">PS</span>
                        <div>
                            <p class="text-2xl font-bold text-blue-900">{{ $value }}</p>
                            <p class="text-xs text-slate-500">{{ $label }}</p>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        </section>

        <section class="mt-10">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-blue-900">Booking Aktif</h2>
                <a href="{{ url('/my-bookings') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat Semua</a>
            </div>
            @if ($activeBookings->isEmpty())
                <x-ui.empty-state class="mt-4" title="Belum ada booking aktif" description="Booking yang menunggu atau sedang diproses akan tampil di sini." />
            @else
                <div class="mt-4 grid gap-4 lg:grid-cols-2">
                    @foreach ($activeBookings as $booking)
                        <x-ui.booking-card :booking="$booking" compact />
                    @endforeach
                </div>
            @endif
        </section>

        <section class="mt-10">
            <h2 class="text-lg font-bold text-blue-900">Buat Booking Baru</h2>
            <p class="mt-1 text-sm text-slate-500">Isi rencana service dan lihat estimasi langsung sebelum mengirim booking.</p>

            @if ($errors->any())
                <x-ui.alert variant="danger" class="mt-4">
                    Booking belum dapat dibuat. Periksa kembali field yang ditandai.
                </x-ui.alert>
            @endif

            <form
                action="{{ route('user.bookings.store') }}"
                method="POST"
                class="mt-4 grid gap-5 lg:grid-cols-[minmax(0,1fr)_20rem]"
                @submit="if (selectedIds.length === 0) { $event.preventDefault(); serviceError = true }"
            >
                @csrf
                <x-ui.card>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-form.input label="Nama Pelanggan" name="customer_name" :value="Auth::user()->name" required readonly />
                        <x-form.input label="Nomor Plat" name="plate_number" placeholder="Contoh: B 1234 XYZ" x-on:input="$event.target.value = $event.target.value.toUpperCase()" required />
                        <div>
                            <label for="vehicle_type" class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kendaraan <span class="text-red-500">*</span></label>
                            <select id="vehicle_type" name="vehicle_type" required class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih jenis kendaraan</option>
                                @foreach (['Mobil', 'Motor'] as $vehicleType)
                                    <option value="{{ $vehicleType }}" @selected(old('vehicle_type') === $vehicleType)>{{ $vehicleType }}</option>
                                @endforeach
                            </select>
                            <x-form.error name="vehicle_type" />
                        </div>
                        <x-form.input label="Merek / Seri Kendaraan" name="vehicle_model" placeholder="Contoh: Toyota Avanza" required />
                        <x-form.input label="Tanggal Service" name="service_date" type="date" required />
                        <x-form.input label="Jam Kedatangan" name="arrival_time" type="time" x-model="arrivalTime" required />
                    </div>

                    <fieldset class="mt-5">
                        <legend class="text-sm font-medium text-slate-700">Slot Bengkel <span class="text-red-500">*</span></legend>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            @foreach (['A', 'B', 'C'] as $slot)
                                <label class="cursor-pointer">
                                    <input type="radio" name="slot" value="{{ $slot }}" class="peer sr-only" @checked(old('slot') === $slot) required>
                                    <span class="block rounded-xl border border-slate-200 px-3 py-3 text-center text-sm font-semibold text-slate-600 transition peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">
                                        Slot {{ $slot }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-form.error name="slot" />
                    </fieldset>

                    <fieldset class="mt-5">
                        <legend class="text-sm font-medium text-slate-700">Pilih Layanan <span class="text-red-500">*</span></legend>
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            @forelse ($services as $service)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="services[]" value="{{ $service->id }}" x-model.number="selectedIds" @change="serviceError = false" class="peer sr-only">
                                    <span
                                        class="flex items-start gap-3 rounded-xl border p-3 transition"
                                        :class="selectedIds.includes({{ $service->id }}) ? 'border-blue-600 bg-blue-50' : 'border-slate-200'"
                                    >
                                        <span
                                            class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded border text-xs"
                                            :class="selectedIds.includes({{ $service->id }}) ? 'border-blue-600 bg-blue-600 text-white' : 'border-slate-300 text-transparent'"
                                        >✓</span>
                                        <span>
                                            <span class="block text-sm font-semibold text-slate-700">{{ $service->name }}</span>
                                            <span class="mt-1 block text-xs text-slate-500">Rp {{ number_format($service->price, 0, ',', '.') }} · {{ $service->duration_minutes }} menit</span>
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada layanan aktif.</p>
                            @endforelse
                        </div>
                        <x-form.error name="services" />
                        <p x-show="serviceError" x-cloak class="mt-1.5 text-xs font-medium text-red-600">Pilih minimal satu layanan.</p>
                    </fieldset>

                    <div class="mt-5">
                        <label for="notes" class="mb-1.5 block text-sm font-medium text-slate-700">Pesan Tambahan</label>
                        <textarea id="notes" name="notes" rows="4" class="block w-full rounded-lg border-slate-300 bg-white text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: mesin berbunyi kasar saat pagi hari">{{ old('notes') }}</textarea>
                        <x-form.error name="notes" />
                    </div>

                    <x-ui.button type="submit" class="mt-5 w-full">Booking Sekarang</x-ui.button>
                </x-ui.card>

                <aside class="space-y-4 lg:sticky lg:top-6 lg:self-start">
                    <x-ui.card>
                        <h3 class="font-semibold text-blue-900">Panel Estimasi</h3>
                        <template x-if="selectedServices.length === 0">
                            <p class="mt-4 text-sm text-slate-500">Pilih minimal satu layanan untuk melihat estimasi.</p>
                        </template>
                        <template x-for="service in selectedServices" :key="service.id">
                            <div class="mt-3 flex justify-between gap-3 text-sm">
                                <span class="text-slate-600" x-text="service.name"></span>
                                <span class="whitespace-nowrap text-slate-500" x-text="formatCurrency(service.price)"></span>
                            </div>
                        </template>
                        <dl class="mt-4 space-y-3 border-t border-slate-200 pt-4 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Total Estimasi Harga</dt>
                                <dd class="font-semibold text-blue-900" x-text="formatCurrency(totalPrice)"></dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Total Estimasi Durasi</dt>
                                <dd class="font-semibold text-slate-700" x-text="`${totalDuration} menit`"></dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Estimasi Jam Selesai</dt>
                                <dd class="font-semibold text-blue-600" x-text="finishTime"></dd>
                            </div>
                        </dl>
                    </x-ui.card>

                    <x-ui.card>
                        <h3 class="font-semibold text-blue-900">Layanan Aktif</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $services->count() }} layanan tersedia untuk dipilih.</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($services->take(4) as $service)
                                <x-ui.badge variant="active">{{ $service->name }}</x-ui.badge>
                            @endforeach
                        </div>
                    </x-ui.card>
                </aside>
            </form>
        </section>

        <section class="mt-10">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-blue-900">Riwayat Booking Terbaru</h2>
                <a href="{{ url('/my-bookings') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Booking Saya</a>
            </div>
            @if ($recentBookings->isEmpty())
                <x-ui.empty-state class="mt-4" title="Belum ada riwayat booking" description="Riwayat booking terbaru akan tampil di sini." />
            @else
                <div class="mt-4 grid gap-4 lg:grid-cols-3">
                    @foreach ($recentBookings as $booking)
                        <x-ui.booking-card :booking="$booking" />
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <script>
        function bookingEstimator(services, selectedIds = [], arrivalTime = '') {
            return {
                services,
                selectedIds: selectedIds.map(Number),
                arrivalTime,
                serviceError: false,
                get selectedServices() {
                    return this.services.filter(service => this.selectedIds.includes(service.id));
                },
                get totalPrice() {
                    return this.selectedServices.reduce((total, service) => total + service.price, 0);
                },
                get totalDuration() {
                    return this.selectedServices.reduce((total, service) => total + service.duration, 0);
                },
                get finishTime() {
                    if (! this.arrivalTime || this.totalDuration === 0) {
                        return '-';
                    }

                    const [hours, minutes] = this.arrivalTime.split(':').map(Number);
                    const totalMinutes = (hours * 60 + minutes + this.totalDuration) % (24 * 60);
                    return `${String(Math.floor(totalMinutes / 60)).padStart(2, '0')}:${String(totalMinutes % 60).padStart(2, '0')} WIB`;
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0,
                    }).format(value);
                },
            };
        }
    </script>
</x-user-layout>
