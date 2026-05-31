<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $booking = Cache::lock("booking-slot:{$validated['slot']}", 10)->block(
                5,
                fn () => DB::transaction(fn () => $this->createBooking($request, $validated)),
            );
        } catch (LockTimeoutException) {
            throw ValidationException::withMessages([
                'slot' => 'Slot sedang diproses oleh booking lain. Silakan coba lagi.',
            ]);
        }

        return redirect()
            ->route('user.bookings.index')
            ->with('success', "Booking {$booking->booking_code} berhasil dibuat.");
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

    private function filteredBookings(Request $request)
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

    /**
     * @param  array<string, mixed>  $validated
     */
    private function createBooking(Request $request, array $validated): Booking
    {
        $services = Service::query()
            ->whereKey($validated['services'])
            ->where('is_active', true)
            ->get();

        $this->ensureAllServicesAreActive($services, $validated['services']);

        $startTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            "{$validated['service_date']} {$validated['arrival_time']}",
        );
        $totalDuration = $services->sum('duration_minutes');
        $endTime = $startTime->copy()->addMinutes($totalDuration);

        $this->ensureWithinOperatingHours($startTime, $endTime);

        if (Booking::query()->conflicting($validated['slot'], $startTime, $endTime)->exists()) {
            throw ValidationException::withMessages([
                'slot' => 'Slot dan waktu yang dipilih bertabrakan dengan booking aktif lain.',
            ]);
        }

        $booking = Booking::create([
            'booking_code' => 'TMP-'.Str::uuid(),
            'user_id' => $request->user()->id,
            'slot' => $validated['slot'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'customer_name' => $validated['customer_name'],
            'plate_number' => Str::upper($validated['plate_number']),
            'vehicle_type' => $validated['vehicle_type'],
            'vehicle_model' => $validated['vehicle_model'],
            'total_price' => $services->sum('price'),
            'total_duration_minutes' => $totalDuration,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        $booking->update([
            'booking_code' => sprintf('PS-%04d', $booking->id),
        ]);
        $booking->services()->attach(
            $services->mapWithKeys(fn (Service $service) => [
                $service->id => [
                    'price_snapshot' => $service->price,
                    'duration_snapshot' => $service->duration_minutes,
                ],
            ]),
        );

        return $booking;
    }

    /**
     * @param  Collection<int, Service>  $services
     * @param  array<int, int|string>  $requestedServiceIds
     */
    private function ensureAllServicesAreActive(Collection $services, array $requestedServiceIds): void
    {
        if ($services->count() !== count($requestedServiceIds)) {
            throw ValidationException::withMessages([
                'services' => 'Pilih layanan aktif yang masih tersedia.',
            ]);
        }
    }

    private function ensureWithinOperatingHours(Carbon $startTime, Carbon $endTime): void
    {
        $openingTime = $startTime->copy()->setTime(8, 0);
        $closingTime = $startTime->copy()->setTime(17, 0);

        if ($startTime->lt($openingTime) || $endTime->gt($closingTime)) {
            throw ValidationException::withMessages([
                'arrival_time' => 'Booking harus dimulai dan selesai dalam jam operasional 08:00 sampai 17:00.',
            ]);
        }
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
