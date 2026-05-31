<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPlaceholderController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/services', [PublicPageController::class, 'services'])->name('services.index');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');

Route::get('/dashboard', UserDashboardController::class)
    ->middleware(['auth', 'role:user'])
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/services/search', [AdminServiceController::class, 'search'])->name('services.search');
    Route::resource('/services', AdminServiceController::class);
    Route::get('/bookings', [AdminPlaceholderController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/history', [AdminPlaceholderController::class, 'history'])->name('bookings.history');
});

Route::middleware(['auth', 'role:user'])->prefix('my-bookings')->name('user.bookings.')->group(function () {
    Route::get('/', [UserBookingController::class, 'index'])->name('index');
    Route::post('/', [UserBookingController::class, 'store'])->name('store');
    Route::get('/search', [UserBookingController::class, 'search'])->name('search');
    Route::get('/{booking}', [UserBookingController::class, 'show'])->name('show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
