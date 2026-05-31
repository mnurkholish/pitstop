<?php

use App\Models\User;

test('user dashboard renders the customer navigation shell', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('PitStop')
        ->assertSee('Dashboard')
        ->assertSee('Layanan')
        ->assertSee('Booking Saya')
        ->assertSee('Profil Saya')
        ->assertSee('Atur Preferensi')
        ->assertDontSee('Kelola Layanan');
});

test('admin dashboard renders the admin navigation shell', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('PitStop Admin')
        ->assertSee('Dashboard Admin')
        ->assertSee('Kelola Layanan')
        ->assertSee('Daftar Booking')
        ->assertSee('Riwayat Booking')
        ->assertSee('Profil Saya')
        ->assertSee('Atur Preferensi')
        ->assertDontSee('Booking Saya');
});

test('breeze auth pages use the PitStop guest shell', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('PitStop');
});
