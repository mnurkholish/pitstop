<?php

use App\Models\Booking;
use App\Models\User;

function createCancellableBooking(User $user, array $attributes = []): Booking
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

test('booking cancellation is restricted to authenticated customers', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);
    $booking = createCancellableBooking($user);

    $this->patch("/my-bookings/{$booking->id}/cancel", [
        'cancel_reason' => 'Jadwal berubah.',
    ])->assertRedirect('/login');

    $this->actingAs($admin)
        ->patch("/my-bookings/{$booking->id}/cancel", [
            'cancel_reason' => 'Jadwal berubah.',
        ])
        ->assertForbidden();
});

test('customer can cancel their pending booking with reason', function () {
    $user = User::factory()->create(['role' => 'user']);
    $booking = createCancellableBooking($user, ['booking_code' => 'PS-CANCEL']);

    $this->actingAs($user)
        ->patch("/my-bookings/{$booking->id}/cancel", [
            'cancel_reason' => 'Ada perubahan jadwal kerja.',
        ])
        ->assertRedirect(route('user.bookings.index'))
        ->assertSessionHas('success', 'Booking PS-CANCEL berhasil dibatalkan.');

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'dibatalkan',
        'cancel_reason' => 'Ada perubahan jadwal kerja.',
    ]);
});

test('booking cancellation reason is required and constrained', function (string $reason) {
    $user = User::factory()->create(['role' => 'user']);
    $booking = createCancellableBooking($user);

    $this->actingAs($user)
        ->patch("/my-bookings/{$booking->id}/cancel", [
            'cancel_reason' => $reason,
        ])
        ->assertSessionHasErrors('cancel_reason');

    expect($booking->fresh()->status)->toBe('pending');
})->with([
    'empty reason' => '',
    'too short' => 'No',
    'too long' => str_repeat('A', 256),
]);

test('customer cannot cancel another customers booking', function () {
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);
    $booking = createCancellableBooking($otherUser);

    $this->actingAs($user)
        ->patch("/my-bookings/{$booking->id}/cancel", [
            'cancel_reason' => 'Bukan booking saya.',
        ])
        ->assertNotFound();

    expect($booking->fresh()->status)->toBe('pending');
});

test('customer cannot cancel a booking that is no longer pending', function (string $status) {
    $user = User::factory()->create(['role' => 'user']);
    $booking = createCancellableBooking($user, ['status' => $status]);

    $this->actingAs($user)
        ->patch("/my-bookings/{$booking->id}/cancel", [
            'cancel_reason' => 'Ingin membatalkan.',
        ])
        ->assertRedirect(route('user.bookings.index'))
        ->assertSessionHas('error', 'Booking hanya dapat dibatalkan selama masih berstatus Menunggu.');

    expect($booking->fresh()->status)->toBe($status);
})->with(['diproses', 'selesai', 'dibatalkan']);

test('my bookings only renders cancellation action for pending booking', function () {
    $user = User::factory()->create(['role' => 'user']);
    $pending = createCancellableBooking($user, ['booking_code' => 'PS-PENDING']);
    $completed = createCancellableBooking($user, [
        'booking_code' => 'PS-DONE',
        'status' => 'selesai',
    ]);

    $this->actingAs($user)
        ->get('/my-bookings')
        ->assertOk()
        ->assertSee("openCancel({$pending->id}", false)
        ->assertDontSee("openCancel({$completed->id}", false);
});

test('cancelled booking detail includes cancellation reason', function () {
    $user = User::factory()->create(['role' => 'user']);
    $booking = createCancellableBooking($user, [
        'status' => 'dibatalkan',
        'cancel_reason' => 'Tidak dapat datang.',
    ]);

    $this->actingAs($user)
        ->getJson("/my-bookings/{$booking->id}")
        ->assertOk()
        ->assertJsonPath('booking.status', 'dibatalkan')
        ->assertJsonPath('booking.cancel_reason', 'Tidak dapat datang.');
});
