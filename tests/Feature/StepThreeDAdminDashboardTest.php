<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

function createAdminDashboardBooking(User $user, string $code, string $status, mixed $startTime = '2026-06-10 09:00:00'): Booking
{
    return Booking::create([
        'booking_code' => $code,
        'user_id' => $user->id,
        'slot' => 'A',
        'start_time' => $startTime,
        'end_time' => now()->parse($startTime)->addHour(),
        'customer_name' => $user->name,
        'plate_number' => 'B 1234 XYZ',
        'vehicle_type' => 'Mobil',
        'vehicle_model' => 'Toyota Avanza',
        'total_price' => 100000,
        'total_duration_minutes' => 60,
        'status' => $status,
    ]);
}

test('admin dashboard is restricted to authenticated admins', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
});

test('admin dashboard renders statistics from database', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);

    Service::create(['name' => 'Service Aktif', 'price' => 85000, 'duration_minutes' => 30, 'is_active' => true]);
    Service::create(['name' => 'Service Nonaktif', 'price' => 100000, 'duration_minutes' => 60, 'is_active' => false]);

    createAdminDashboardBooking($customer, 'PS-PENDING', 'pending', now()->setTime(9, 0));
    createAdminDashboardBooking($customer, 'PS-PROCESSING', 'diproses');
    createAdminDashboardBooking($customer, 'PS-COMPLETED', 'selesai');
    createAdminDashboardBooking($customer, 'PS-CANCELLED', 'dibatalkan');

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('data-testid="admin-stat-services-total" data-value="2"', false)
        ->assertSee('data-testid="admin-stat-services-active" data-value="1"', false)
        ->assertSee('data-testid="admin-stat-services-inactive" data-value="1"', false)
        ->assertSee('data-testid="admin-stat-bookings-total" data-value="4"', false)
        ->assertSee('data-testid="admin-stat-bookings-pending" data-value="1"', false)
        ->assertSee('data-testid="admin-stat-bookings-processing" data-value="1"', false)
        ->assertSee('data-testid="admin-stat-bookings-completed" data-value="1"', false)
        ->assertSee('data-testid="admin-stat-bookings-cancelled" data-value="1"', false)
        ->assertSee('data-testid="admin-stat-bookings-today" data-value="1"', false)
        ->assertSee('PS-PENDING')
        ->assertSee('Service Aktif')
        ->assertSee('Service Nonaktif');
});

test('admin dashboard renders consistent booking status labels', function (string $status, string $label) {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    createAdminDashboardBooking($customer, 'PS-'.$status, $status);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee($label);
})->with([
    ['pending', 'Menunggu'],
    ['diproses', 'Diproses'],
    ['selesai', 'Selesai'],
    ['dibatalkan', 'Dibatalkan'],
]);

test('admin navigation placeholders are restricted to admins', function (string $path) {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get($path)->assertRedirect('/login');
    $this->actingAs($user)->get($path)->assertForbidden();
    $this->actingAs($admin)->get($path)->assertOk();
})->with([
    '/admin/services',
    '/admin/bookings',
    '/admin/bookings/history',
]);
