<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

function createAdminListBooking(User $user, array $attributes = []): Booking
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
        'notes' => 'Catatan pelanggan.',
    ], $attributes));
}

test('admin active booking list is restricted to authenticated admins', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get('/admin/bookings')->assertRedirect('/login');
    $this->actingAs($user)->get('/admin/bookings')->assertForbidden();
    $this->actingAs($admin)->get('/admin/bookings')->assertOk();

    $this->actingAs($user)->getJson('/admin/bookings/search')->assertForbidden();
});

test('admin active booking list only renders pending and processing bookings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    createAdminListBooking($customer, ['booking_code' => 'PS-PENDING', 'status' => 'pending']);
    createAdminListBooking($customer, ['booking_code' => 'PS-PROCESS', 'status' => 'diproses']);
    createAdminListBooking($customer, ['booking_code' => 'PS-DONE', 'status' => 'selesai']);
    createAdminListBooking($customer, ['booking_code' => 'PS-CANCELLED', 'status' => 'dibatalkan']);

    $this->actingAs($admin)
        ->get('/admin/bookings')
        ->assertOk()
        ->assertSee('PS-PENDING')
        ->assertSee('PS-PROCESS')
        ->assertDontSee('PS-DONE')
        ->assertDontSee('PS-CANCELLED')
        ->assertSee('Menunggu')
        ->assertSee('Diproses');
});

test('admin active booking search filters by status and date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    createAdminListBooking($customer, [
        'booking_code' => 'PS-PENDING',
        'status' => 'pending',
        'start_time' => '2026-06-15 09:00:00',
        'end_time' => '2026-06-15 10:00:00',
    ]);
    createAdminListBooking($customer, [
        'booking_code' => 'PS-PROCESS',
        'status' => 'diproses',
        'start_time' => '2026-06-16 09:00:00',
        'end_time' => '2026-06-16 10:00:00',
    ]);

    $this->actingAs($admin)
        ->getJson('/admin/bookings/search?status=pending')
        ->assertOk()
        ->assertJsonPath('count', 1);

    $payload = $this->actingAs($admin)
        ->getJson('/admin/bookings/search?date=2026-06-16')
        ->assertOk()
        ->assertJsonPath('count', 1)
        ->json();

    expect($payload['desktop'])->toContain('PS-PROCESS')
        ->not->toContain('PS-PENDING');
});

test('admin active booking search matches code customer plate service and status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $service = Service::create([
        'name' => 'Tune Up Khusus',
        'price' => 200000,
        'duration_minutes' => 90,
        'is_active' => true,
    ]);
    $booking = createAdminListBooking($customer, [
        'booking_code' => 'PS-SEARCH',
        'customer_name' => 'Budi Santoso',
        'plate_number' => 'B 9876 ABC',
    ]);
    $booking->services()->attach($service, [
        'price_snapshot' => 175000,
        'duration_snapshot' => 75,
    ]);

    foreach (['PS-SEARCH', 'Budi', '9876', 'Tune Up', 'pending'] as $search) {
        $this->actingAs($admin)
            ->getJson('/admin/bookings/search?search='.urlencode($search))
            ->assertOk()
            ->assertJsonPath('count', 1);
    }
});

test('admin can load active booking detail with service snapshots', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $service = Service::create([
        'name' => 'Ganti Oli',
        'price' => 100000,
        'duration_minutes' => 45,
        'is_active' => true,
    ]);
    $booking = createAdminListBooking($customer, ['booking_code' => 'PS-DETAIL']);
    $booking->services()->attach($service, [
        'price_snapshot' => 85000,
        'duration_snapshot' => 30,
    ]);

    $this->actingAs($admin)
        ->getJson("/admin/bookings/{$booking->id}")
        ->assertOk()
        ->assertJsonPath('booking.booking_code', 'PS-DETAIL')
        ->assertJsonPath('booking.status_label', 'Menunggu')
        ->assertJsonPath('booking.services.0.name', 'Ganti Oli')
        ->assertJsonPath('booking.services.0.price', 'Rp 85.000')
        ->assertJsonPath('booking.services.0.duration_minutes', 30);
});

test('admin active booking detail rejects final bookings', function (string $status) {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminListBooking($customer, ['status' => $status]);

    $this->actingAs($admin)
        ->getJson("/admin/bookings/{$booking->id}")
        ->assertNotFound();
})->with(['selesai', 'dibatalkan']);

test('admin booking history route remains a separate placeholder', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/bookings/history')
        ->assertOk()
        ->assertSee('Riwayat Booking');
});
