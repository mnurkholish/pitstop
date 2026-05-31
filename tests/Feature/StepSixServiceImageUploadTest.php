<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function stepSixServicePayload(array $attributes = []): array
{
    return array_merge([
        'name' => 'Cuci Premium',
        'description' => 'Perawatan kendaraan menyeluruh.',
        'price' => 125000,
        'duration_minutes' => 75,
        'is_active' => true,
    ], $attributes);
}

function createStepSixService(array $attributes = []): Service
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

function createStepSixBooking(User $user, array $attributes = []): Booking
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

test('admin can upload an image when creating a service', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post('/admin/services', stepSixServicePayload([
            'image' => UploadedFile::fake()->image('premium.png'),
        ]))
        ->assertRedirect(route('admin.services.index'));

    $service = \App\Models\Service::query()->where('name', 'Cuci Premium')->firstOrFail();

    expect($service->image)->toStartWith('services/');
    Storage::disk('public')->assertExists($service->image);
});

test('service image upload only accepts supported images up to two megabytes', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post('/admin/services', stepSixServicePayload([
            'image' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf'),
        ]))
        ->assertSessionHasErrors('image');

    $this->actingAs($admin)
        ->post('/admin/services', stepSixServicePayload([
            'image' => UploadedFile::fake()->image('large.jpg')->size(2049),
        ]))
        ->assertSessionHasErrors('image');
});

test('uploading a replacement service image removes the old image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('public')->put('services/old.png', 'old-image');
    $service = createStepSixService(['image' => 'services/old.png']);

    $this->actingAs($admin)
        ->put("/admin/services/{$service->id}", stepSixServicePayload([
            'image' => UploadedFile::fake()->image('replacement.jpg'),
        ]))
        ->assertRedirect(route('admin.services.index'));

    $service->refresh();

    Storage::disk('public')->assertMissing('services/old.png');
    Storage::disk('public')->assertExists($service->image);
});

test('deleting an unused service removes its image', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('public')->put('services/unused.png', 'unused-image');
    $service = createStepSixService(['image' => 'services/unused.png']);

    $this->actingAs($admin)
        ->delete("/admin/services/{$service->id}")
        ->assertRedirect(route('admin.services.index'));

    Storage::disk('public')->assertMissing('services/unused.png');
});

test('image remains when a service used by booking history cannot be deleted', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    Storage::disk('public')->put('services/used.png', 'used-image');
    $service = createStepSixService(['image' => 'services/used.png']);
    $booking = createStepSixBooking($customer);
    $booking->services()->attach($service, [
        'price_snapshot' => $service->price,
        'duration_snapshot' => $service->duration_minutes,
    ]);

    $this->actingAs($admin)
        ->delete("/admin/services/{$service->id}")
        ->assertSessionHas('error');

    Storage::disk('public')->assertExists('services/used.png');
});
