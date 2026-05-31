<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingHistoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.bookings.history.index', [
            'bookings' => $this->filteredBookings($request)->get(),
            'summary' => $this->summary(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $bookings = $this->filteredBookings($request)->get();

        return response()->json([
            'count' => $bookings->count(),
            'desktop' => view('admin.bookings.history.partials.desktop-rows', compact('bookings'))->render(),
            'mobile' => view('admin.bookings.history.partials.mobile-cards', compact('bookings'))->render(),
            'empty' => view('admin.bookings.history.partials.empty')->render(),
        ]);
    }

    public function show(Booking $booking): JsonResponse
    {
        abort_unless(in_array($booking->status, ['selesai', 'dibatalkan'], true), 404);

        $booking->load('services');

        return response()->json([
            'booking' => [
                'booking_code' => $booking->booking_code,
                'customer_name' => $booking->customer_name,
                'plate_number' => $booking->plate_number,
                'vehicle_type' => $booking->vehicle_type,
                'vehicle_model' => $booking->vehicle_model,
                'slot' => $booking->slot,
                'start_time' => $booking->start_time->format('d M Y, H:i').' WIB',
                'end_time' => $booking->end_time->format('d M Y, H:i').' WIB',
                'total_price' => $this->formatCurrency($booking->total_price),
                'total_duration_minutes' => $booking->total_duration_minutes,
                'status' => $booking->status,
                'status_label' => $booking->statusLabel(),
                'status_variant' => $booking->statusBadgeVariant(),
                'notes' => $booking->notes ?: '-',
                'cancel_reason' => $booking->cancel_reason,
                'completed_at' => $booking->completed_at
                    ? $booking->completed_at->format('d M Y, H:i').' WIB'
                    : '-',
                'services' => $booking->services->map(fn ($service) => [
                    'name' => $service->name,
                    'price' => $this->formatCurrency($service->pivot->price_snapshot),
                    'duration_minutes' => $service->pivot->duration_snapshot,
                ])->values(),
            ],
        ]);
    }

    private function filteredBookings(Request $request): Builder
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');
        $date = (string) $request->string('date');

        return Booking::query()
            ->with('services')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->when(in_array($status, ['selesai', 'dibatalkan'], true), fn (Builder $query) => $query->where('status', $status))
            ->when($date !== '', fn (Builder $query) => $query->whereDate('start_time', $date))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('booking_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('services', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('start_time');
    }

    private function summary(): array
    {
        $bookings = Booking::query()->whereIn('status', ['selesai', 'dibatalkan']);

        return [
            'total' => (clone $bookings)->count(),
            'completed' => (clone $bookings)->where('status', 'selesai')->count(),
            'cancelled' => (clone $bookings)->where('status', 'dibatalkan')->count(),
        ];
    }

    private function formatCurrency(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
