<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.bookings.index', [
            'bookings' => $this->filteredBookings($request)->get(),
            'summary' => $this->summary(),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $bookings = $this->filteredBookings($request)->get();

        return response()->json([
            'count' => $bookings->count(),
            'desktop' => view('admin.bookings.partials.desktop-rows', compact('bookings'))->render(),
            'mobile' => view('admin.bookings.partials.mobile-cards', compact('bookings'))->render(),
            'empty' => view('admin.bookings.partials.empty')->render(),
        ]);
    }

    public function show(Booking $booking): JsonResponse
    {
        abort_unless(in_array($booking->status, ['pending', 'diproses'], true), 404);

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

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['diproses', 'selesai', 'dibatalkan'])],
            'cancel_reason' => [
                Rule::requiredIf($request->input('status') === 'dibatalkan'),
                'nullable',
                'string',
                'min:3',
                'max:255',
            ],
        ]);

        $result = DB::transaction(function () use ($booking, $validated) {
            $lockedBooking = Booking::query()
                ->whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $this->canTransition($lockedBooking->status, $validated['status'])) {
                return null;
            }

            $lockedBooking->update([
                'status' => $validated['status'],
                'cancel_reason' => $validated['status'] === 'dibatalkan'
                    ? $validated['cancel_reason']
                    : null,
                'completed_at' => $validated['status'] === 'selesai' ? now() : null,
            ]);

            return $lockedBooking;
        });

        if (! $result) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', 'Perubahan status booking tidak diizinkan.');
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', "Status booking {$result->booking_code} berhasil diperbarui.");
    }

    private function filteredBookings(Request $request): Builder
    {
        $search = trim((string) $request->string('search'));
        $status = (string) $request->string('status');
        $date = (string) $request->string('date');

        return Booking::query()
            ->with('services')
            ->whereIn('status', ['pending', 'diproses'])
            ->when(in_array($status, ['pending', 'diproses'], true), fn (Builder $query) => $query->where('status', $status))
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
            ->orderBy('start_time');
    }

    private function summary(): array
    {
        $bookings = Booking::query()->whereIn('status', ['pending', 'diproses']);

        return [
            'total' => (clone $bookings)->count(),
            'pending' => (clone $bookings)->where('status', 'pending')->count(),
            'processing' => (clone $bookings)->where('status', 'diproses')->count(),
        ];
    }

    private function canTransition(string $currentStatus, string $nextStatus): bool
    {
        return in_array($nextStatus, match ($currentStatus) {
            'pending' => ['diproses', 'dibatalkan'],
            'diproses' => ['selesai', 'dibatalkan'],
            default => [],
        }, true);
    }

    private function formatCurrency(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
