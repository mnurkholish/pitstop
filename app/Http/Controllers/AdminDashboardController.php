<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $services = Service::query();
        $bookings = Booking::query();

        return view('admin.dashboard', [
            'summary' => [
                'services_total' => (clone $services)->count(),
                'services_active' => (clone $services)->where('is_active', true)->count(),
                'services_inactive' => (clone $services)->where('is_active', false)->count(),
                'bookings_total' => (clone $bookings)->count(),
                'bookings_pending' => (clone $bookings)->where('status', 'pending')->count(),
                'bookings_processing' => (clone $bookings)->where('status', 'diproses')->count(),
                'bookings_completed' => (clone $bookings)->where('status', 'selesai')->count(),
                'bookings_cancelled' => (clone $bookings)->where('status', 'dibatalkan')->count(),
                'bookings_today' => (clone $bookings)->whereDate('start_time', today())->count(),
            ],
            'latestBookings' => Booking::query()
                ->with('services')
                ->latest('created_at')
                ->limit(5)
                ->get(),
            'latestServices' => Service::query()
                ->latest('created_at')
                ->limit(6)
                ->get(),
        ]);
    }
}
