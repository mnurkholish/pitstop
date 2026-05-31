<?php

use App\Models\Service;
use App\Models\User;

function servicePayload(array $attributes = []): array
{
    return array_merge([
        'name' => 'Cuci Premium',
        'description' => 'Perawatan kendaraan menyeluruh.',
        'price' => 125000,
        'duration_minutes' => 75,
        'is_active' => true,
    ], $attributes);
}

test('admin service management is restricted to authenticated admins', function () {
    $user = User::factory()->create(['role' => 'user']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->get('/admin/services')->assertRedirect('/login');
    $this->actingAs($user)->get('/admin/services')->assertForbidden();
    $this->actingAs($admin)->get('/admin/services')->assertOk();

    $this->actingAs($user)->get('/admin/services/search')->assertForbidden();
    $this->actingAs($user)->post('/admin/services', servicePayload())->assertForbidden();
});

test('admin service list displays active and inactive services', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    createService(['name' => 'Layanan Aktif', 'is_active' => true]);
    createService(['name' => 'Layanan Nonaktif', 'is_active' => false]);

    $this->actingAs($admin)
        ->get('/admin/services')
        ->assertOk()
        ->assertSee('Layanan Aktif')
        ->assertSee('Layanan Nonaktif')
        ->assertSee('Rp')
        ->assertSee('menit');
});

test('admin can search and filter services through json endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    createService([
        'name' => 'Salon Interior',
        'price' => 175000,
        'duration_minutes' => 90,
        'is_active' => true,
    ]);
    createService([
        'name' => 'Cuci Eksterior',
        'price' => 50000,
        'duration_minutes' => 30,
        'is_active' => false,
    ]);

    $this->actingAs($admin)
        ->getJson('/admin/services/search?search=Salon')
        ->assertOk()
        ->assertJsonPath('count', 1)
        ->assertJsonPath('empty', false)
        ->assertJsonFragment(['count' => 1])
        ->assertJson(fn ($json) => $json
            ->where('count', 1)
            ->where('empty', false)
            ->etc());

    $this->actingAs($admin)
        ->getJson('/admin/services/search?search=175000')
        ->assertOk()
        ->assertJsonPath('count', 1);

    $this->actingAs($admin)
        ->getJson('/admin/services/search?search=90')
        ->assertOk()
        ->assertJsonPath('count', 1);

    $this->actingAs($admin)
        ->getJson('/admin/services/search?status=inactive')
        ->assertOk()
        ->assertJsonPath('count', 1)
        ->assertJsonPath('empty', false);
});

test('admin can create a service with server side validation', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post('/admin/services', servicePayload())
        ->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('services', [
        'name' => 'Cuci Premium',
        'price' => 125000,
        'duration_minutes' => 75,
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->post('/admin/services', servicePayload([
            'name' => '',
            'price' => -1,
            'duration_minutes' => 0,
            'is_active' => 'invalid',
        ]))
        ->assertSessionHasErrors(['name', 'price', 'duration_minutes', 'is_active']);
});

test('admin can view and update a service', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $service = createService(['name' => 'Cuci Lama']);

    $this->actingAs($admin)
        ->get("/admin/services/{$service->id}")
        ->assertOk()
        ->assertSee('Cuci Lama');

    $this->actingAs($admin)
        ->put("/admin/services/{$service->id}", servicePayload([
            'name' => 'Cuci Baru',
            'is_active' => false,
        ]))
        ->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Cuci Baru',
        'is_active' => false,
    ]);
});

test('admin can delete an unused service', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $service = createService();

    $this->actingAs($admin)
        ->delete("/admin/services/{$service->id}")
        ->assertRedirect(route('admin.services.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

test('admin cannot delete a service used by booking history', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['role' => 'user']);
    $service = createService();
    $booking = createBooking($customer);
    $booking->services()->attach($service, [
        'price_snapshot' => $service->price,
        'duration_snapshot' => $service->duration_minutes,
    ]);

    $this->actingAs($admin)
        ->delete("/admin/services/{$service->id}")
        ->assertRedirect(route('admin.services.index'))
        ->assertSessionHas('error', 'Layanan sudah pernah dipakai booking dan tidak dapat dihapus. Nonaktifkan layanan jika sudah tidak tersedia.');

    $this->assertDatabaseHas('services', ['id' => $service->id]);
    $this->assertDatabaseHas('booking_service', [
        'booking_id' => $booking->id,
        'service_id' => $service->id,
    ]);
});
