@foreach ($bookings as $booking)
    <tr>
        <td class="px-4 py-3 font-semibold text-blue-700">{{ $booking->booking_code }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->customer_name }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->plate_number }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->start_time->format('d M Y, H:i') }} WIB</td>
        <td class="px-4 py-3 text-slate-600">Slot {{ $booking->slot }}</td>
        <td class="px-4 py-3"><x-ui.badge :variant="$booking->statusBadgeVariant()">{{ $booking->statusLabel() }}</x-ui.badge></td>
        <td class="px-4 py-3">
            <div class="flex flex-wrap gap-2">
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
            </div>
        </td>
    </tr>
@endforeach
