<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $this->filteredBookings($request)->get();

        return view('user.bookings.index', [
            'bookings' => $bookings,
            'summary' => $this->summary($request),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $bookings = $this->filteredBookings($request)->get();

        return response()->json([
            'count' => $bookings->count(),
            'desktop' => view('user.bookings.partials.desktop-rows', compact('bookings'))->render(),
            'mobile' => view('user.bookings.partials.mobile-cards', compact('bookings'))->render(),
            'empty' => view('user.bookings.partials.empty')->render(),
        ]);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);

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
                'services' => $booking->services->map(fn ($service) => [
                    'name' => $service->name,
                    'price' => $this->formatCurrency($service->pivot->price_snapshot),
                    'duration_minutes' => $service->pivot->duration_snapshot,
                ])->values(),
            ],
        ]);
    }

    private function filteredBookings(Request $request): HasMany
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');

        return $request->user()
            ->bookings()
            ->with('services')
            ->when(in_array($status, ['pending', 'diproses', 'selesai', 'dibatalkan'], true), fn (Builder $query) => $query->where('status', $status))
            ->when($search !== '', function (Builder $query) use ($search) {
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('booking_code', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('services', fn (Builder $query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('start_time');
    }

    private function summary(Request $request): array
    {
        $bookings = $request->user()->bookings();

        return [
            'total' => (clone $bookings)->count(),
            'pending' => (clone $bookings)->where('status', 'pending')->count(),
            'processing' => (clone $bookings)->where('status', 'diproses')->count(),
            'completed' => (clone $bookings)->where('status', 'selesai')->count(),
        ];
    }

    private function formatCurrency(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
