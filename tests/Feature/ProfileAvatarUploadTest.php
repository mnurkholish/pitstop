<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authenticated user can upload an avatar from profile', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('avatar.png'),
        ])
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->avatar)->toStartWith('avatars/');
    Storage::disk('public')->assertExists($user->avatar);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk()
        ->assertSee('storage/'.$user->avatar, false);
});

test('uploading a replacement avatar removes the old file', function () {
    Storage::fake('public');
    Storage::disk('public')->put('avatars/old.png', 'old-avatar');
    $user = User::factory()->create([
        'role' => 'user',
        'avatar' => 'avatars/old.png',
    ]);

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('replacement.jpg'),
        ])
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    Storage::disk('public')->assertMissing('avatars/old.png');
    Storage::disk('public')->assertExists($user->avatar);
});

test('avatar upload rejects unsupported files and files larger than two megabytes', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->create('avatar.pdf', 10, 'application/pdf'),
        ])
        ->assertSessionHasErrors('avatar');

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('large.png')->size(2049),
        ])
        ->assertSessionHasErrors('avatar');
});

test('auth pages use Indonesian navigation copy', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Masuk ke PitStop')
        ->assertSee('Belum punya akun?')
        ->assertSee('Lupa password?')
        ->assertSee('Kembali');

    $this->get('/register')
        ->assertOk()
        ->assertSee('Daftar Akun PitStop')
        ->assertSee('Sudah punya akun?')
        ->assertSee('Kembali');

    $this->get('/forgot-password')
        ->assertOk()
        ->assertSee('Lupa Password')
        ->assertSee('Kembali ke halaman masuk')
        ->assertSee('Belum punya akun?');
});
