@foreach ($bookings as $booking)
    <x-ui.mobile-data-card :title="$booking->booking_code" class="mb-3">
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
        <div>
            <p class="text-xs text-slate-400">Jadwal</p>
            <p class="mt-1 font-medium text-slate-700">{{ $booking->start_time->format('d M Y, H:i') }} WIB</p>
        </div>
        <div>
            <p class="text-xs text-slate-400">Slot</p>
            <p class="mt-1 font-medium text-slate-700">Slot {{ $booking->slot }}</p>
        </div>
        <x-slot name="actions">
            <p class="text-sm font-semibold text-blue-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
            <x-ui.button type="button" variant="secondary" size="sm" data-booking-action="detail" data-booking-id="{{ $booking->id }}">Detail</x-ui.button>
            @if ($booking->status === 'pending')
                <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST" onsubmit="return confirm('Proses booking ini?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="diproses">
                    <x-ui.button type="submit" size="sm">Proses</x-ui.button>
                </form>
            @endif
            @if ($booking->status === 'diproses')
                <form action="{{ route('admin.bookings.status.update', $booking) }}" method="POST" onsubmit="return confirm('Selesaikan booking ini?')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="selesai">
                    <x-ui.button type="submit" size="sm">Selesaikan</x-ui.button>
                </form>
            @endif
            <x-ui.button type="button" variant="danger" size="sm" data-booking-action="cancel" data-booking-id="{{ $booking->id }}" data-booking-code="{{ $booking->booking_code }}">Batalkan</x-ui.button>
        </x-slot>
    </x-ui.mobile-data-card>
@endforeach
