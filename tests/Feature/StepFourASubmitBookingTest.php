<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

function createSubmitService(array $attributes = []): Service
{
    return Service::create(array_merge([
        'name' => 'Service '.fake()->unique()->word(),
        'description' => null,
        'price' => 100000,
        'duration_minutes' => 60,
        'image' => null,
        'is_active' => true,
    ], $attributes));
}

function createSubmitBooking(User $user, array $attributes = []): Booking
{
    return Booking::create(array_merge([
        'booking_code' => 'PS-'.fake()->unique()->numerify('####'),
        'user_id' => $user->id,
        'slot' => 'A',
        'start_time' => '2026-06-15 09:00:00',
        'end_time' => '2026-06-15 10:00:00',
        'customer_name' => $user->name,
        'plate_number' => 'B 1234 XYZ',
        'vehicle_type' => 'Mobil',
        'vehicle_model' => 'Toyota Avanza',
        'total_price' => 100000,
        'total_duration_minutes' => 60,
        'status' => 'pending',
    ], $attributes));
}

function bookingPayload(Service|array $services, array $attributes = []): array
{
    $serviceIds = collect(is_array($services) ? $services : [$services])
        ->map(fn (Service|int $service) => $service instanceof Service ? $service->id : $service)
        ->all();

    return array_merge([
        'customer_name' => 'Budi Pelanggan',
        'plate_number' => 'b 1234 xyz',
        'vehicle_type' => 'Mobil',
        'vehicle_model' => 'Toyota Avanza',
        'service_date' => '2026-06-15',
        'arrival_time' => '09:00',
        'slot' => 'A',
        'services' => $serviceIds,
        'notes' => 'Periksa suara mesin.',
    ], $attributes);
}

test('booking submit is restricted to authenticated customers', function () {
    $service = createSubmitService();
    $admin = User::factory()->create(['role' => 'admin']);

    $this->post('/my-bookings', bookingPayload($service))->assertRedirect('/login');
    $this->actingAs($admin)->post('/my-bookings', bookingPayload($service))->assertForbidden();
});

test('customer booking submit validates required fields', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->post('/my-bookings', [])
        ->assertSessionHasErrors([
            'customer_name',
            'plate_number',
            'vehicle_type',
            'vehicle_model',
            'service_date',
            'arrival_time',
            'slot',
            'services',
        ]);
});

test('customer can submit booking with database totals snapshots and uppercase plate', function () {
    $user = User::factory()->create(['role' => 'user']);
    $oil = createSubmitService(['name' => 'Ganti Oli', 'price' => 85000, 'duration_minutes' => 30]);
    $tuneUp = createSubmitService(['name' => 'Tune Up', 'price' => 150000, 'duration_minutes' => 60]);

    $this->actingAs($user)
        ->post('/my-bookings', bookingPayload([$oil, $tuneUp]))
        ->assertRedirect(route('user.bookings.index'))
        ->assertSessionHas('success', 'Booking PS-0001 berhasil dibuat.');

    $booking = Booking::firstOrFail();

    expect($booking->booking_code)->toBe('PS-0001')
        ->and($booking->user_id)->toBe($user->id)
        ->and($booking->plate_number)->toBe('B 1234 XYZ')
        ->and($booking->total_price)->toBe(235000)
        ->and($booking->total_duration_minutes)->toBe(90)
        ->and($booking->status)->toBe('pending')
        ->and($booking->start_time->format('Y-m-d H:i'))->toBe('2026-06-15 09:00')
        ->and($booking->end_time->format('Y-m-d H:i'))->toBe('2026-06-15 10:30');

    $this->assertDatabaseHas('booking_service', [
        'booking_id' => $booking->id,
        'service_id' => $oil->id,
        'price_snapshot' => 85000,
        'duration_snapshot' => 30,
    ]);
    $this->assertDatabaseHas('booking_service', [
        'booking_id' => $booking->id,
        'service_id' => $tuneUp->id,
        'price_snapshot' => 150000,
        'duration_snapshot' => 60,
    ]);
});

test('booking codes use sequential ps format backed by booking ids', function () {
    $user = User::factory()->create(['role' => 'user']);
    $service = createSubmitService();

    $this->actingAs($user)->post('/my-bookings', bookingPayload($service));
    $this->actingAs($user)->post('/my-bookings', bookingPayload($service, [
        'arrival_time' => '11:00',
    ]));

    expect(Booking::query()->orderBy('id')->pluck('booking_code')->all())
        ->toBe(['PS-0001', 'PS-0002']);
});

test('booking submit rejects inactive and unknown services', function (array $services) {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->post('/my-bookings', bookingPayload($services))
        ->assertSessionHasErrors('services.0');

    expect(Booking::count())->toBe(0);
})->with([
    'inactive service' => fn () => [createSubmitService(['is_active' => false])],
    'unknown service' => fn () => [999999],
]);

test('booking submit rejects overlap with active bookings', function (string $status) {
    $user = User::factory()->create(['role' => 'user']);
    $service = createSubmitService(['duration_minutes' => 60]);
    createSubmitBooking($user, [
        'booking_code' => 'PS-EXISTING',
        'slot' => 'A',
        'start_time' => '2026-06-15 09:00:00',
        'end_time' => '2026-06-15 10:00:00',
        'status' => $status,
    ]);

    $this->actingAs($user)
        ->post('/my-bookings', bookingPayload($service, ['arrival_time' => '09:30']))
        ->assertSessionHasErrors('slot');

    expect(Booking::count())->toBe(1);
})->with(['pending', 'diproses']);

test('booking submit permits overlap with final bookings', function (string $status) {
    $user = User::factory()->create(['role' => 'user']);
    $service = createSubmitService(['duration_minutes' => 60]);
    createSubmitBooking($user, [
        'booking_code' => 'PS-EXISTING',
        'slot' => 'A',
        'start_time' => '2026-06-15 09:00:00',
        'end_time' => '2026-06-15 10:00:00',
        'status' => $status,
    ]);

    $this->actingAs($user)
        ->post('/my-bookings', bookingPayload($service, ['arrival_time' => '09:30']))
        ->assertRedirect(route('user.bookings.index'));

    expect(Booking::count())->toBe(2);
})->with(['selesai', 'dibatalkan']);

test('booking submit rejects schedules outside operating hours', function (string $arrivalTime, int $duration) {
    $user = User::factory()->create(['role' => 'user']);
    $service = createSubmitService(['duration_minutes' => $duration]);

    $this->actingAs($user)
        ->post('/my-bookings', bookingPayload($service, ['arrival_time' => $arrivalTime]))
        ->assertSessionHasErrors('arrival_time');

    expect(Booking::count())->toBe(0);
})->with([
    'before opening' => ['07:59', 30],
    'finishes after closing' => ['16:31', 30],
]);

test('booking submit permits a schedule ending exactly at closing time', function () {
    $user = User::factory()->create(['role' => 'user']);
    $service = createSubmitService(['duration_minutes' => 60]);

    $this->actingAs($user)
        ->post('/my-bookings', bookingPayload($service, ['arrival_time' => '16:00']))
        ->assertRedirect(route('user.bookings.index'));

    expect(Booking::firstOrFail()->end_time->format('H:i'))->toBe('17:00');
});
