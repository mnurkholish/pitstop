<?php

use App\Models\Service;
use Database\Seeders\ServiceSeeder;

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
        ->assertSee('favicons/pitstop-light.ico', false)
        ->assertSee('images/logo-pitstop-light.png', false)
        ->assertSee('images/hero.png', false)
        ->assertSee('images/services/service-default.png', false)
        ->assertSee('Service Kendaraan')
        ->assertSee('Tanpa Antre')
        ->assertSee('Layanan Unggulan')
        ->assertSee('Kenapa Pilih PitStop?')
        ->assertSee('Cara Kerja PitStop')
        ->assertSee('Siap service kendaraanmu?')
        ->assertSee(route('login', ['redirect' => '/dashboard']), escape: false);
});

test('public layout uses dark visual assets when the dark theme cookie is selected', function () {
    $this->withCookie('pitstop_theme', 'dark')
        ->get('/')
        ->assertOk()
        ->assertSee('favicons/pitstop-dark.ico', false)
        ->assertSee('images/logo-pitstop-dark.png', false);
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

test('public navbar pages are reachable', function (string $path, string $text) {
    $this->get($path)
        ->assertOk()
        ->assertSee($text);
})->with([
    ['/services', 'Layanan'],
    ['/about', 'Masalah yang Kami Selesaikan'],
    ['/contact', 'Kontak'],
]);

test('service seeder maps demo services to bundled public images', function () {
    $this->seed(ServiceSeeder::class);

    expect(Service::where('name', 'Ganti Oli Mesin')->value('image'))
        ->toBe('images/services/ganti-oli-mesin.png');
});

test('public service detail resolves bundled service images', function () {
    $service = createHomeService('Ganti Oli Mesin');
    $service->update(['image' => 'images/services/ganti-oli-mesin.png']);

    $this->getJson(route('services.show', $service))
        ->assertOk()
        ->assertJsonPath('service.image_url', asset('images/services/ganti-oli-mesin.png'));
});

test('public contact page only displays workshop information', function () {
    $this->get('/contact')
        ->assertOk()
        ->assertSee('Informasi Kontak')
        ->assertSee('Jam Operasional')
        ->assertDontSee('Kirim Pesan')
        ->assertDontSee('contact_message', false);
});
