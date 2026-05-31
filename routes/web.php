<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/services', [PublicPageController::class, 'services'])->name('services.index');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');

Route::get('/dashboard', UserDashboardController::class)
    ->middleware(['auth', 'role:user'])
    ->name('dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard-placeholder');
})->middleware(['auth', 'role:admin'])->name('admin.dashboard');

Route::get('/my-bookings', function () {
    return view('user.bookings-placeholder');
})->middleware(['auth', 'role:user'])->name('user.bookings.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
