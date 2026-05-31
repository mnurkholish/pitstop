<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();
        $services = Service::query()->get()->keyBy('name');

        $bookings = [
            [
                'booking_code' => 'PS-0001',
                'slot' => 'A',
                'start_time' => now()->addDay()->setTime(9, 0),
                'status' => 'pending',
                'plate_number' => 'B 1234 XYZ',
                'vehicle_model' => 'Toyota Avanza',
                'services' => ['Ganti Oli Mesin', 'Tune Up'],
            ],
            [
                'booking_code' => 'PS-0002',
                'slot' => 'B',
                'start_time' => now()->addDays(2)->setTime(10, 30),
                'status' => 'diproses',
                'plate_number' => 'B 5678 ABC',
                'vehicle_model' => 'Honda Brio',
                'services' => ['Service Rem'],
            ],
            [
                'booking_code' => 'PS-0003',
                'slot' => 'C',
                'start_time' => now()->subDays(2)->setTime(13, 0),
                'status' => 'selesai',
                'plate_number' => 'P 7777 EF',
                'vehicle_model' => 'Suzuki Ertiga',
                'services' => ['Cek Mesin'],
            ],
            [
                'booking_code' => 'PS-0004',
                'slot' => 'A',
                'start_time' => now()->subDay()->setTime(8, 30),
                'status' => 'dibatalkan',
                'plate_number' => 'P 9999 GH',
                'vehicle_model' => 'Daihatsu Xenia',
                'cancel_reason' => 'Pelanggan membatalkan jadwal service.',
                'services' => ['Spooring Balancing'],
            ],
        ];

        foreach ($bookings as $data) {
            $selectedServices = collect($data['services'])
                ->map(fn (string $name) => $services->get($name))
                ->filter();
            $totalPrice = $selectedServices->sum('price');
            $totalDuration = $selectedServices->sum('duration_minutes');
            $startTime = $data['start_time'];

            $booking = Booking::updateOrCreate(
                ['booking_code' => $data['booking_code']],
                [
                    'user_id' => $user->id,
                    'slot' => $data['slot'],
                    'start_time' => $startTime,
                    'end_time' => $startTime->copy()->addMinutes($totalDuration),
                    'customer_name' => $user->name,
                    'plate_number' => $data['plate_number'],
                    'vehicle_type' => 'Mobil',
                    'vehicle_model' => $data['vehicle_model'],
                    'total_price' => $totalPrice,
                    'total_duration_minutes' => $totalDuration,
                    'status' => $data['status'],
                    'notes' => null,
                    'cancel_reason' => $data['cancel_reason'] ?? null,
                    'completed_at' => $data['status'] === 'selesai' ? $startTime->copy()->addMinutes($totalDuration) : null,
                ],
            );

            $booking->services()->sync(
                $selectedServices->mapWithKeys(fn (Service $service) => [
                    $service->id => [
                        'price_snapshot' => $service->price,
                        'duration_snapshot' => $service->duration_minutes,
                    ],
                ])->all(),
            );
        }
    }
}
