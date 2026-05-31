<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\QueryException;

function createService(array $attributes = []): Service
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

function createBooking(User $user, array $attributes = []): Booking
{
    return Booking::create(array_merge([
        'booking_code' => 'PS-'.fake()->unique()->numerify('####'),
        'user_id' => $user->id,
        'slot' => 'A',
        'start_time' => '2026-06-01 09:00:00',
        'end_time' => '2026-06-01 10:00:00',
        'customer_name' => $user->name,
        'plate_number' => 'B 1234 XYZ',
        'vehicle_type' => 'Mobil',
        'vehicle_model' => 'Toyota Avanza',
        'total_price' => 100000,
        'total_duration_minutes' => 60,
        'status' => 'pending',
    ], $attributes));
}

test('dashboard access is restricted by role', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)->get('/dashboard')->assertOk();
    $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    $this->actingAs($admin)->get('/dashboard')->assertForbidden();
});

test('admin login redirects to admin dashboard', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard', absolute: false));
});

test('public registration always assigns the user role', function () {
    $this->post('/register', [
        'name' => 'Public User',
        'email' => 'public@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ])->assertRedirect(route('dashboard', absolute: false));

    expect(User::where('email', 'public@example.com')->value('role'))->toBe('user');
});

test('a booking belongs to its customer only', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $booking = createBooking($owner);

    expect($owner->bookings->contains($booking))->toBeTrue()
        ->and($otherUser->bookings->contains($booking))->toBeFalse()
        ->and($booking->user->is($owner))->toBeTrue();
});

test('conflicting scope finds overlapping active bookings only', function () {
    $user = User::factory()->create();
    createBooking($user);
    createBooking($user, [
        'booking_code' => 'PS-9001',
        'status' => 'selesai',
    ]);

    expect(Booking::conflicting('A', '2026-06-01 09:30:00', '2026-06-01 10:30:00')->count())->toBe(1)
        ->and(Booking::conflicting('B', '2026-06-01 09:30:00', '2026-06-01 10:30:00')->count())->toBe(0)
        ->and(Booking::conflicting('A', '2026-06-01 10:00:00', '2026-06-01 11:00:00')->count())->toBe(0);
});

test('service snapshots preserve historical values', function () {
    $user = User::factory()->create();
    $service = createService(['price' => 85000, 'duration_minutes' => 30]);
    $booking = createBooking($user, ['total_price' => 85000, 'total_duration_minutes' => 30]);

    $booking->services()->attach($service, [
        'price_snapshot' => $service->price,
        'duration_snapshot' => $service->duration_minutes,
    ]);
    $service->update(['price' => 100000, 'duration_minutes' => 45]);

    $snapshot = $booking->services()->firstOrFail()->pivot;

    expect($snapshot->price_snapshot)->toBe(85000)
        ->and($snapshot->duration_snapshot)->toBe(30)
        ->and($booking->fresh()->total_price)->toBe(85000);
});

test('unused services can be deleted', function () {
    $service = createService();

    $service->delete();

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

test('services used by bookings cannot be deleted', function () {
    $user = User::factory()->create();
    $service = createService();
    $booking = createBooking($user);
    $booking->services()->attach($service, [
        'price_snapshot' => $service->price,
        'duration_snapshot' => $service->duration_minutes,
    ]);

    expect(fn () => $service->delete())->toThrow(QueryException::class);
});

test('users with bookings are soft deleted and their booking history remains', function () {
    $user = User::factory()->create();
    $booking = createBooking($user);

    $user->delete();

    $this->assertSoftDeleted($user);
    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'user_id' => $user->id,
    ]);
    expect($booking->fresh()->user->is($user))->toBeTrue();
});

test('soft deleted users cannot login', function () {
    $user = User::factory()->create();
    $user->delete();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('email from a soft deleted user cannot be registered again', function () {
    $user = User::factory()->create();
    $user->delete();

    $this->post('/register', [
        'name' => 'Replacement User',
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');

    expect(User::withTrashed()->where('email', $user->email)->count())->toBe(1);
});
