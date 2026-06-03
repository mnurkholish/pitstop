<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $bookings = $request->user()->bookings();

        return view('user.dashboard', [
            'summary' => [
                'total' => (clone $bookings)->count(),
                'pending' => (clone $bookings)->where('status', 'pending')->count(),
                'processing' => (clone $bookings)->where('status', 'diproses')->count(),
                'completed' => (clone $bookings)->where('status', 'selesai')->count(),
            ],
            'activeBookings' => (clone $bookings)
                ->with('services')
                ->whereIn('status', ['pending', 'diproses'])
                ->orderBy('start_time')
                ->limit(2)
                ->get(),
            'recentBookings' => (clone $bookings)
                ->with('services')
                ->latest('start_time')
                ->limit(3)
                ->get(),
            'services' => Service::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }
}
