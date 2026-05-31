@foreach ($bookings as $booking)
    <tr>
        <td class="px-4 py-3 font-semibold text-blue-700">{{ $booking->booking_code }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->plate_number }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->services->pluck('name')->join(', ') }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->start_time->format('d M Y, H:i') }} WIB</td>
        <td class="px-4 py-3 text-slate-600">Slot {{ $booking->slot }}</td>
        <td class="px-4 py-3 font-semibold text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
        <td class="px-4 py-3"><x-ui.badge :variant="$booking->statusBadgeVariant()">{{ $booking->statusLabel() }}</x-ui.badge></td>
        <td class="px-4 py-3">
            <div class="flex flex-wrap gap-2">
                <x-ui.button type="button" variant="secondary" size="sm" x-on:click="openDetail({{ $booking->id }})">Detail</x-ui.button>
                @if ($booking->status === 'pending')
                    <x-ui.button type="button" variant="danger" size="sm" x-on:click="openCancel({{ $booking->id }}, @js($booking->booking_code))">Batalkan</x-ui.button>
                @endif
            </div>
        </td>
    </tr>
@endforeach
