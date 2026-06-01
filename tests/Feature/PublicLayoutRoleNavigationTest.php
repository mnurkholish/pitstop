<?php

use App\Models\User;

test('public services keeps guest navigation for guests', function () {
    $this->get('/services')
        ->assertOk()
        ->assertSee('Beranda')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertDontSee('Booking Saya');
});

test('public services uses customer navigation for authenticated customers', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get('/services')
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Booking Saya')
        ->assertSee('Profil Saya')
        ->assertSee('Atur Preferensi')
        ->assertDontSee('Beranda');
});

test('public pages use admin navigation for authenticated admins', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/services')
        ->assertOk()
        ->assertSee('Dashboard Admin')
        ->assertSee('Kelola Layanan')
        ->assertSee('Daftar Booking')
        ->assertSee('Riwayat Booking')
        ->assertDontSee('Booking Saya');
});
