<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

function createDashboardBooking(User $user, string $code, string $status = 'pending'): Booking
{
    return Booking::create([
        'booking_code' => $code,
        'user_id' => $user->id,
        'slot' => 'A',
        'start_time' => '2026-06-10 09:00:00',
        'end_time' => '2026-06-10 10:00:00',
        'customer_name' => $user->name,
        'plate_number' => 'B 1234 XYZ',
        'vehicle_type' => 'Mobil',
        'vehicle_model' => 'Toyota Avanza',
        'total_price' => 100000,
        'total_duration_minutes' => 60,
        'status' => $status,
    ]);
}

test('guest is redirected to login from customer dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('customer dashboard only renders bookings owned by the authenticated user', function () {
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);
    createDashboardBooking($user, 'PS-OWNED');
    createDashboardBooking($otherUser, 'PS-PRIVATE');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('PS-OWNED')
        ->assertDontSee('PS-PRIVATE');
});

test('customer dashboard only offers active services in booking form', function () {
    $user = User::factory()->create(['role' => 'user']);
    Service::create([
        'name' => 'Service Aktif',
        'description' => null,
        'price' => 85000,
        'duration_minutes' => 30,
        'is_active' => true,
    ]);
    Service::create([
        'name' => 'Service Nonaktif',
        'description' => null,
        'price' => 100000,
        'duration_minutes' => 60,
        'is_active' => false,
    ]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('Buat Booking Baru')
        ->assertSee('Service Aktif')
        ->assertDontSee('Service Nonaktif')
        ->assertSee('Booking Sekarang');
});

test('customer dashboard renders booking status labels', function (string $status, string $label) {
    $user = User::factory()->create(['role' => 'user']);
    createDashboardBooking($user, 'PS-'.$status, $status);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee($label);
})->with([
    ['pending', 'Menunggu'],
    ['diproses', 'Diproses'],
    ['selesai', 'Selesai'],
    ['dibatalkan', 'Dibatalkan'],
]);

test('booking saya placeholder is restricted to customers', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get('/my-bookings')->assertRedirect('/login');
    $this->actingAs($admin)->get('/my-bookings')->assertForbidden();
    $this->actingAs($user)->get('/my-bookings')->assertOk();
});
