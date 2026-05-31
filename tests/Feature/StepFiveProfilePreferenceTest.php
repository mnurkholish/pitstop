<?php

use App\Models\User;

test('profile uses customer layout and fallback initials for customer', function () {
    $user = User::factory()->create([
        'name' => 'Budi Santoso',
        'role' => 'user',
    ]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Profil Saya')
        ->assertSee('Budi Santoso')
        ->assertSee('BS')
        ->assertSee('Pelanggan')
        ->assertSee('Avatar masih menggunakan fallback inisial')
        ->assertSee('Dashboard')
        ->assertDontSee('Dashboard Admin');
});

test('profile uses admin layout and fallback initials for admin', function () {
    $admin = User::factory()->create([
        'name' => 'Admin Pitstop',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Profil Saya')
        ->assertSee('AP')
        ->assertSee('Admin')
        ->assertSee('Dashboard Admin')
        ->assertDontSee('Booking Saya');
});

test('preferences require authentication and use safe defaults', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->get('/preferences')->assertRedirect('/login');

    $this->actingAs($user)
        ->get('/preferences')
        ->assertOk()
        ->assertSee('Atur Preferensi')
        ->assertSee('value="light"', false)
        ->assertSee('value="normal"', false)
        ->assertSee('pitstop-theme-light', false)
        ->assertSee('pitstop-font-normal', false);
});

test('preferences can be stored in cookies and applied to customer layout', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->patch('/preferences', [
            'theme' => 'dark',
            'font_size' => 'large',
        ])
        ->assertRedirect(route('preferences.edit'))
        ->assertSessionHas('success', 'Preferensi tampilan berhasil disimpan.')
        ->assertCookie('pitstop_theme', 'dark')
        ->assertCookie('pitstop_font_size', 'large');

    $this->actingAs($user)
        ->withCookie('pitstop_theme', $response->getCookie('pitstop_theme')->getValue())
        ->withCookie('pitstop_font_size', $response->getCookie('pitstop_font_size')->getValue())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('pitstop-theme-dark pitstop-font-large', false);
});

test('preferences are applied to admin layout', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->withCookie('pitstop_theme', 'dark')
        ->withCookie('pitstop_font_size', 'large')
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('pitstop-theme-dark pitstop-font-large', false);
});

test('preferences reject unsupported values', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->patch('/preferences', [
            'theme' => 'sepia',
            'font_size' => 'tiny',
        ])
        ->assertSessionHasErrors(['theme', 'font_size']);
});

test('preference stylesheet keeps dark selections readable and large font effective', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    expect($css)
        ->toContain('html.pitstop-font-large')
        ->toContain('font-size: 20px !important')
        ->toContain('.pitstop-theme-dark .bg-blue-50')
        ->toContain('.pitstop-theme-dark .text-blue-700');
});

test('soft delete account flow remains available from refreshed profile', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('Riwayat booking tetap tersimpan')
        ->assertSee('Hapus Akun');

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertRedirect('/');

    $this->assertSoftDeleted($user);
});
