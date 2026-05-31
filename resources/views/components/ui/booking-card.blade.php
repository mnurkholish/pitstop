@props(['booking', 'compact' => false])

<article {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white p-4 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-xs text-slate-400">Kode Booking</p>
            <p class="mt-1 text-sm font-bold text-blue-700">{{ $booking->booking_code }}</p>
        </div>
        <x-ui.badge :variant="$booking->statusBadgeVariant()">{{ $booking->statusLabel() }}</x-ui.badge>
    </div>

    <dl class="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
        <div>
            <dt class="text-xs text-slate-400">Nomor Plat</dt>
            <dd class="mt-1 font-medium text-slate-700">{{ $booking->plate_number }}</dd>
        </div>
        <div>
            <dt class="text-xs text-slate-400">Slot</dt>
            <dd class="mt-1 font-medium text-slate-700">Slot {{ $booking->slot }}</dd>
        </div>
        <div>
            <dt class="text-xs text-slate-400">Layanan</dt>
            <dd class="mt-1 font-medium text-slate-700">{{ $booking->services->pluck('name')->join(', ') }}</dd>
        </div>
        <div>
            <dt class="text-xs text-slate-400">Jadwal</dt>
            <dd class="mt-1 font-medium text-slate-700">{{ $booking->start_time->format('d M Y, H:i') }} WIB</dd>
        </div>
        @if (! $compact)
            <div>
                <dt class="text-xs text-slate-400">Total Estimasi</dt>
                <dd class="mt-1 font-medium text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400">Durasi</dt>
                <dd class="mt-1 font-medium text-slate-700">{{ $booking->total_duration_minutes }} menit</dd>
            </div>
        @endif
    </dl>

    <x-ui.button href="{{ url('/my-bookings') }}" variant="secondary" size="sm" class="mt-4 w-full">
        Lihat Detail
    </x-ui.button>
</article>
