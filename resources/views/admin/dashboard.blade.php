<x-admin-layout>
    <div class="pitstop-container py-8 sm:py-10">
        <section>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-blue-900">Dashboard Admin</h1>
                    <p class="mt-1 text-sm text-slate-500">Pantau aktivitas booking dan layanan bengkel dari satu tempat.</p>
                </div>
                <p class="text-sm font-medium text-slate-500">{{ now()->format('d M Y') }}</p>
            </div>

            <div class="mt-7 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['services-total', 'Total Layanan', $summary['services_total'], 'bg-blue-100 text-blue-700'],
                    ['services-active', 'Layanan Aktif', $summary['services_active'], 'bg-emerald-100 text-emerald-700'],
                    ['services-inactive', 'Layanan Nonaktif', $summary['services_inactive'], 'bg-slate-100 text-slate-600'],
                    ['bookings-total', 'Total Booking', $summary['bookings_total'], 'bg-blue-100 text-blue-700'],
                    ['bookings-pending', 'Booking Menunggu', $summary['bookings_pending'], 'bg-amber-100 text-amber-700'],
                    ['bookings-processing', 'Booking Diproses', $summary['bookings_processing'], 'bg-blue-100 text-blue-700'],
                    ['bookings-completed', 'Booking Selesai', $summary['bookings_completed'], 'bg-emerald-100 text-emerald-700'],
                    ['bookings-cancelled', 'Booking Dibatalkan', $summary['bookings_cancelled'], 'bg-red-100 text-red-700'],
                ] as [$key, $label, $value, $color])
                    <x-ui.card class="flex items-center gap-3 p-4" data-testid="admin-stat-{{ $key }}" data-value="{{ $value }}">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $color }}">PS</span>
                        <div>
                            <p class="text-2xl font-bold text-blue-900">{{ $value }}</p>
                            <p class="text-xs text-slate-500">{{ $label }}</p>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>

            <x-ui.card class="mt-3 flex items-center justify-between gap-4 p-4" data-testid="admin-stat-bookings-today" data-value="{{ $summary['bookings_today'] }}">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Booking Hari Ini</p>
                    <p class="mt-1 text-xs text-slate-500">Jadwal service yang tercatat untuk hari ini.</p>
                </div>
                <p class="text-3xl font-bold text-blue-900">{{ $summary['bookings_today'] }}</p>
            </x-ui.card>
        </section>

        <section class="mt-8">
            <h2 class="text-lg font-bold text-blue-900">Menu Cepat</h2>
            <div class="mt-4 grid gap-3 lg:grid-cols-3">
                @foreach ([
                    [route('admin.services.index'), 'Kelola Layanan', 'Tambah, edit, dan kelola layanan bengkel.'],
                    [route('admin.bookings.index'), 'Daftar Booking', 'Kelola booking aktif yang masih menunggu atau diproses.'],
                    [route('admin.bookings.history'), 'Riwayat Booking', 'Lihat booking yang sudah selesai atau dibatalkan.'],
                ] as [$href, $title, $description])
                    <a href="{{ $href }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:shadow-md">
                        <p class="font-semibold text-blue-900">{{ $title }}</p>
                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="mt-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-blue-900">Booking Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Booking terkini dari pelanggan.</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat Semua</a>
            </div>

            @if ($latestBookings->isEmpty())
                <x-ui.empty-state class="mt-4" title="Belum ada booking" description="Booking pelanggan terbaru akan tampil di sini." />
            @else
                <x-ui.responsive-table class="mt-4">
                    <x-slot name="desktop">
                        <thead class="bg-slate-100 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Kode Booking</th>
                                <th class="px-4 py-3">Nama Pelanggan</th>
                                <th class="px-4 py-3">Nomor Plat</th>
                                <th class="px-4 py-3">Jadwal</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($latestBookings as $booking)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-blue-700">{{ $booking->booking_code }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $booking->customer_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $booking->plate_number }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $booking->start_time->format('d M Y, H:i') }} WIB</td>
                                    <td class="px-4 py-3"><x-ui.badge :variant="$booking->statusBadgeVariant()">{{ $booking->statusLabel() }}</x-ui.badge></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-slot>
                    <x-slot name="mobile">
                        @foreach ($latestBookings as $booking)
                            <x-ui.mobile-data-card :title="$booking->booking_code">
                                <x-slot name="badge">
                                    <x-ui.badge :variant="$booking->statusBadgeVariant()">{{ $booking->statusLabel() }}</x-ui.badge>
                                </x-slot>
                                <div>
                                    <p class="text-xs text-slate-400">Pelanggan</p>
                                    <p class="mt-1 font-medium text-slate-700">{{ $booking->customer_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Nomor Plat</p>
                                    <p class="mt-1 font-medium text-slate-700">{{ $booking->plate_number }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-slate-400">Jadwal</p>
                                    <p class="mt-1 font-medium text-slate-700">{{ $booking->start_time->format('d M Y, H:i') }} WIB</p>
                                </div>
                            </x-ui.mobile-data-card>
                        @endforeach
                    </x-slot>
                </x-ui.responsive-table>
            @endif
        </section>

        <section class="mt-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-blue-900">Layanan Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Ringkasan layanan bengkel yang tersedia.</p>
                </div>
                <a href="{{ route('admin.services.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kelola Layanan</a>
            </div>

            @if ($latestServices->isEmpty())
                <x-ui.empty-state class="mt-4" title="Belum ada layanan" description="Layanan bengkel akan tampil di sini." />
            @else
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($latestServices as $service)
                        <x-ui.card class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-semibold text-slate-800">{{ $service->name }}</h3>
                                    <p class="mt-2 text-sm text-slate-500">Rp {{ number_format($service->price, 0, ',', '.') }} · {{ $service->duration_minutes }} menit</p>
                                </div>
                                <x-ui.badge :variant="$service->is_active ? 'active' : 'inactive'">{{ $service->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-admin-layout>
