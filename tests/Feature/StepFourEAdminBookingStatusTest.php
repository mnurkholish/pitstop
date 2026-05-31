<?php

use App\Models\Booking;
use App\Models\User;

function createAdminStatusBooking(User $user, array $attributes = []): Booking
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

test('admin booking status update is restricted to authenticated admins', function () {
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer);

    $this->patch("/admin/bookings/{$booking->id}/status", [
        'status' => 'diproses',
    ])->assertRedirect('/login');

    $this->actingAs($customer)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'diproses',
        ])
        ->assertForbidden();

    expect($booking->fresh()->status)->toBe('pending');
});

test('admin can process a pending booking', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer, ['booking_code' => 'PS-PROCESS']);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'diproses',
        ])
        ->assertRedirect(route('admin.bookings.index'))
        ->assertSessionHas('success', 'Status booking PS-PROCESS berhasil diperbarui.');

    expect($booking->fresh()->status)->toBe('diproses')
        ->and($booking->fresh()->completed_at)->toBeNull()
        ->and($booking->fresh()->cancel_reason)->toBeNull();
});

test('admin can complete a processing booking and completed at is stored automatically', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer, [
        'booking_code' => 'PS-DONE',
        'status' => 'diproses',
    ]);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'selesai',
        ])
        ->assertRedirect(route('admin.bookings.index'));

    expect($booking->fresh()->status)->toBe('selesai')
        ->and($booking->fresh()->completed_at)->not->toBeNull()
        ->and($booking->fresh()->cancel_reason)->toBeNull();

    $this->actingAs($admin)
        ->getJson('/admin/bookings/search')
        ->assertOk()
        ->assertJsonPath('count', 0);

    $this->actingAs($admin)
        ->get('/admin/bookings/history')
        ->assertSee('PS-DONE');
});

test('admin can cancel an active booking with reason', function (string $status) {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer, [
        'booking_code' => 'PS-CANCEL',
        'status' => $status,
    ]);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'dibatalkan',
            'cancel_reason' => 'Slot bengkel tidak tersedia.',
        ])
        ->assertRedirect(route('admin.bookings.index'));

    expect($booking->fresh()->status)->toBe('dibatalkan')
        ->and($booking->fresh()->cancel_reason)->toBe('Slot bengkel tidak tersedia.')
        ->and($booking->fresh()->completed_at)->toBeNull();
})->with(['pending', 'diproses']);

test('admin booking cancellation reason is required and constrained', function (string $reason) {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'dibatalkan',
            'cancel_reason' => $reason,
        ])
        ->assertSessionHasErrors('cancel_reason');

    expect($booking->fresh()->status)->toBe('pending');
})->with([
    'empty reason' => '',
    'too short' => 'No',
    'too long' => str_repeat('A', 256),
]);

test('admin booking status update rejects arbitrary statuses', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => 'unknown',
        ])
        ->assertSessionHasErrors('status');

    expect($booking->fresh()->status)->toBe('pending');
});

test('admin booking status update rejects forbidden transitions', function (string $currentStatus, string $nextStatus) {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $booking = createAdminStatusBooking($customer, ['status' => $currentStatus]);

    $this->actingAs($admin)
        ->patch("/admin/bookings/{$booking->id}/status", [
            'status' => $nextStatus,
        ])
        ->assertRedirect(route('admin.bookings.index'))
        ->assertSessionHas('error', 'Perubahan status booking tidak diizinkan.');

    expect($booking->fresh()->status)->toBe($currentStatus);
})->with([
    ['pending', 'selesai'],
    ['diproses', 'diproses'],
    ['selesai', 'diproses'],
    ['dibatalkan', 'diproses'],
]);

test('admin active booking list renders actions matching current status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    createAdminStatusBooking($customer, ['booking_code' => 'PS-PENDING', 'status' => 'pending']);
    createAdminStatusBooking($customer, ['booking_code' => 'PS-PROCESS', 'status' => 'diproses']);

    $response = $this->actingAs($admin)
        ->get('/admin/bookings')
        ->assertOk();

    $response
        ->assertSee('value="diproses"', false)
        ->assertSee('value="selesai"', false)
        ->assertSee('data-booking-action="cancel"', false)
        ->assertSee('Proses')
        ->assertSee('Selesaikan')
        ->assertSee('Batalkan');
});
