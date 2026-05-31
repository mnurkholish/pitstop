<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

function createMyBooking(User $user, string $code, string $status = 'pending', string $plate = 'B 1234 XYZ'): Booking
{
    return Booking::create([
        'booking_code' => $code,
        'user_id' => $user->id,
        'slot' => 'A',
        'start_time' => '2026-06-10 09:00:00',
        'end_time' => '2026-06-10 10:00:00',
        'customer_name' => $user->name,
        'plate_number' => $plate,
        'vehicle_type' => 'Mobil',
        'vehicle_model' => 'Toyota Avanza',
        'total_price' => 100000,
        'total_duration_minutes' => 60,
        'status' => $status,
        'notes' => 'Catatan pelanggan.',
    ]);
}

test('my bookings page is restricted to authenticated customers', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get('/my-bookings')->assertRedirect('/login');
    $this->actingAs($admin)->get('/my-bookings')->assertForbidden();
    $this->actingAs($user)->get('/my-bookings')->assertOk();
});

test('my bookings page only renders bookings owned by authenticated customer', function () {
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);
    createMyBooking($user, 'PS-MINE');
    createMyBooking($otherUser, 'PS-PRIVATE');

    $this->actingAs($user)
        ->get('/my-bookings')
        ->assertOk()
        ->assertSee('PS-MINE')
        ->assertDontSee('PS-PRIVATE');
});

test('my bookings search endpoint filters by status and stays scoped to customer', function () {
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);
    createMyBooking($user, 'PS-PENDING', 'pending');
    createMyBooking($user, 'PS-DONE', 'selesai');
    createMyBooking($otherUser, 'PS-PRIVATE', 'pending');

    $this->actingAs($user)
        ->getJson('/my-bookings/search?status=pending')
        ->assertOk()
        ->assertJsonPath('count', 1);

    $payload = $this->actingAs($user)->getJson('/my-bookings/search?status=pending')->json();

    expect($payload['desktop'])->toContain('PS-PENDING')
        ->not->toContain('PS-DONE')
        ->not->toContain('PS-PRIVATE');
});

test('my bookings search endpoint searches code plate and service name', function () {
    $user = User::factory()->create(['role' => 'user']);
    $service = Service::create([
        'name' => 'Tune Up Spesial',
        'description' => null,
        'price' => 200000,
        'duration_minutes' => 90,
        'is_active' => true,
    ]);
    $booking = createMyBooking($user, 'PS-TUNE', 'pending', 'B 9999 ABC');
    $booking->services()->attach($service, [
        'price_snapshot' => 200000,
        'duration_snapshot' => 90,
    ]);

    foreach (['PS-TUNE', '9999', 'Tune Up'] as $search) {
        $this->actingAs($user)
            ->getJson('/my-bookings/search?search='.urlencode($search))
            ->assertOk()
            ->assertJsonPath('count', 1);
    }
});

test('customer can load their booking detail preview with snapshot services', function () {
    $user = User::factory()->create(['role' => 'user']);
    $service = Service::create([
        'name' => 'Ganti Oli',
        'description' => null,
        'price' => 85000,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);
    $booking = createMyBooking($user, 'PS-DETAIL');
    $booking->services()->attach($service, [
        'price_snapshot' => 75000,
        'duration_snapshot' => 25,
    ]);

    $this->actingAs($user)
        ->getJson("/my-bookings/{$booking->id}")
        ->assertOk()
        ->assertJsonPath('booking.booking_code', 'PS-DETAIL')
        ->assertJsonPath('booking.status_label', 'Menunggu')
        ->assertJsonPath('booking.services.0.name', 'Ganti Oli')
        ->assertJsonPath('booking.services.0.price', 'Rp 75.000')
        ->assertJsonPath('booking.services.0.duration_minutes', 25);
});

test('customer cannot load another customers booking detail preview', function () {
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);
    $booking = createMyBooking($otherUser, 'PS-PRIVATE');

    $this->actingAs($user)
        ->getJson("/my-bookings/{$booking->id}")
        ->assertNotFound();
});
