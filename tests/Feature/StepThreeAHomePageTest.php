<?php

use App\Models\Service;

function createHomeService(string $name, bool $isActive = true): Service
{
    return Service::create([
        'name' => $name,
        'description' => 'Deskripsi layanan '.$name,
        'price' => 100000,
        'duration_minutes' => 60,
        'image' => null,
        'is_active' => $isActive,
    ]);
}

test('guest homepage renders its primary sections and booking call to action', function () {
    createHomeService('Ganti Oli Mesin');

    $this->get('/')
        ->assertOk()
        ->assertSee('Service Kendaraan')
        ->assertSee('Tanpa Antre')
        ->assertSee('Layanan Unggulan')
        ->assertSee('Kenapa Pilih PitStop?')
        ->assertSee('Cara Kerja PitStop')
        ->assertSee('Siap service kendaraanmu?')
        ->assertSee(route('login', ['redirect' => '/dashboard']), escape: false);
});

test('guest homepage only displays up to four active services', function () {
    foreach (range(1, 5) as $index) {
        createHomeService('Active Service '.$index);
    }

    createHomeService('Inactive Service', false);

    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('Active Service 1')
        ->assertSee('Active Service 4')
        ->assertDontSee('Active Service 5')
        ->assertDontSee('Inactive Service');
});

test('guest homepage increments its lightweight session visit counter', function () {
    $this->get('/')->assertSessionHas('home_visit_count', 1);
    $this->get('/')->assertSessionHas('home_visit_count', 2);
});

test('public navbar placeholder pages are reachable', function (string $path, string $text) {
    $this->get($path)
        ->assertOk()
        ->assertSee($text);
})->with([
    ['/services', 'Layanan'],
    ['/about', 'Tentang PitStop'],
    ['/contact', 'Kontak'],
]);
