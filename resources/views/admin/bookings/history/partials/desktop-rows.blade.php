@foreach ($bookings as $booking)
    <tr>
        <td class="px-4 py-3 font-semibold text-blue-700">{{ $booking->booking_code }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->customer_name }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->plate_number }}</td>
        <td class="px-4 py-3 text-slate-600">{{ $booking->start_time->format('d M Y, H:i') }} WIB</td>
        <td class="px-4 py-3"><x-ui.badge :variant="$booking->statusBadgeVariant()">{{ $booking->statusLabel() }}</x-ui.badge></td>
        <td class="px-4 py-3">
            <x-ui.button type="button" variant="secondary" size="sm" data-booking-action="detail" data-booking-id="{{ $booking->id }}">Detail</x-ui.button>
        </td>
    </tr>
@endforeach
