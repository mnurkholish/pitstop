<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

function createAdminHistoryBooking(User $user, array $attributes = []): Booking
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
        'status' => 'selesai',
        'notes' => 'Catatan pelanggan.',
        'completed_at' => '2026-06-15 10:00:00',
    ], $attributes));
}

test('admin booking history is restricted to authenticated admins', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get('/admin/bookings/history')->assertRedirect('/login');
    $this->actingAs($user)->get('/admin/bookings/history')->assertForbidden();
    $this->actingAs($admin)->get('/admin/bookings/history')->assertOk();

    $this->actingAs($user)->getJson('/admin/bookings/history/search')->assertForbidden();
});

test('admin booking history only renders completed and cancelled bookings', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    createAdminHistoryBooking($customer, ['booking_code' => 'PS-DONE', 'status' => 'selesai']);
    createAdminHistoryBooking($customer, ['booking_code' => 'PS-CANCELLED', 'status' => 'dibatalkan']);
    createAdminHistoryBooking($customer, ['booking_code' => 'PS-PENDING', 'status' => 'pending']);
    createAdminHistoryBooking($customer, ['booking_code' => 'PS-PROCESS', 'status' => 'diproses']);

    $this->actingAs($admin)
        ->get('/admin/bookings/history')
        ->assertOk()
        ->assertSee('PS-DONE')
        ->assertSee('PS-CANCELLED')
        ->assertDontSee('PS-PENDING')
        ->assertDontSee('PS-PROCESS')
        ->assertSee('Selesai')
        ->assertSee('Dibatalkan');
});

test('admin booking history search filters by status and date', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    createAdminHistoryBooking($customer, [
        'booking_code' => 'PS-DONE',
        'status' => 'selesai',
        'start_time' => '2026-06-15 09:00:00',
    ]);
    createAdminHistoryBooking($customer, [
        'booking_code' => 'PS-CANCELLED',
        'status' => 'dibatalkan',
        'start_time' => '2026-06-16 09:00:00',
    ]);

    $this->actingAs($admin)
        ->getJson('/admin/bookings/history/search?status=selesai')
        ->assertOk()
        ->assertJsonPath('count', 1);

    $payload = $this->actingAs($admin)
        ->getJson('/admin/bookings/history/search?date=2026-06-16')
        ->assertOk()
        ->assertJsonPath('count', 1)
        ->json();

    expect($payload['desktop'])->toContain('PS-CANCELLED')
        ->not->toContain('PS-DONE');
});

test('admin booking history search matches code customer plate service and status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $service = Service::create([
        'name' => 'Spooring Khusus',
        'price' => 200000,
        'duration_minutes' => 90,
        'is_active' => true,
    ]);
    $booking = createAdminHistoryBooking($customer, [
        'booking_code' => 'PS-HISTORY',
        'customer_name' => 'Siti Aminah',
        'plate_number' => 'B 5678 DEF',
    ]);
    $booking->services()->attach($service, [
        'price_snapshot' => 175000,
        'duration_snapshot' => 75,
    ]);

    foreach (['PS-HISTORY', 'Siti', '5678', 'Spooring', 'selesai'] as $search) {
        $this->actingAs($admin)
            ->getJson('/admin/bookings/history/search?search='.urlencode($search))
            ->assertOk()
            ->assertJsonPath('count', 1);
    }
});

test('admin can load booking history detail with snapshots and final fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $service = Service::create([
        'name' => 'Service Rem',
        'price' => 125000,
        'duration_minutes' => 60,
        'is_active' => true,
    ]);
    $booking = createAdminHistoryBooking($customer, [
        'booking_code' => 'PS-HISTORY-DETAIL',
        'status' => 'dibatalkan',
        'cancel_reason' => 'Pelanggan berhalangan hadir.',
        'completed_at' => null,
    ]);
    $booking->services()->attach($service, [
        'price_snapshot' => 100000,
        'duration_snapshot' => 45,
    ]);

    $this->actingAs($admin)
        ->getJson("/admin/bookings/history/{$booking->id}")
        ->assertOk()
        ->assertJsonPath('booking.booking_code', 'PS-HISTORY-DETAIL')
        ->assertJsonPath('booking.status_label', 'Dibatalkan')
        ->assertJsonPath('booking.cancel_reason', 'Pelanggan berhalangan hadir.')
        ->assertJsonPath('booking.completed_at', '-')
        ->assertJsonPath('booking.services.0.name', 'Service Rem')
        ->assertJsonPath('booking.services.0.price', 'Rp 100.000')
        ->assertJsonPath('booking.services.0.duration_minutes', 45);
});

test('admin booking history detail includes completed timestamp', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminHistoryBooking($customer, [
        'completed_at' => '2026-06-15 10:30:00',
    ]);

    $this->actingAs($admin)
        ->getJson("/admin/bookings/history/{$booking->id}")
        ->assertOk()
        ->assertJsonPath('booking.completed_at', '15 Jun 2026, 10:30 WIB');
});

test('admin booking history detail rejects active bookings', function (string $status) {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminHistoryBooking($customer, ['status' => $status]);

    $this->actingAs($admin)
        ->getJson("/admin/bookings/history/{$booking->id}")
        ->assertNotFound();
})->with(['pending', 'diproses']);
