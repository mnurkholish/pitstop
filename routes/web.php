<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminBookingHistoryController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserBookingController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/services', [PublicPageController::class, 'services'])->name('services.index');
Route::get('/services/search', [PublicPageController::class, 'searchServices'])->name('services.search');
Route::get('/services/{service}', [PublicPageController::class, 'showService'])->name('services.show');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');

Route::get('/dashboard', UserDashboardController::class)
    ->middleware(['auth', 'role:user'])
    ->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/services/search', [AdminServiceController::class, 'search'])->name('services.search');
    Route::resource('/services', AdminServiceController::class);
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/search', [AdminBookingController::class, 'search'])->name('bookings.search');
    Route::get('/bookings/history', [AdminBookingHistoryController::class, 'index'])->name('bookings.history');
    Route::get('/bookings/history/search', [AdminBookingHistoryController::class, 'search'])->name('bookings.history.search');
    Route::get('/bookings/history/{booking}', [AdminBookingHistoryController::class, 'show'])->name('bookings.history.show');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status.update');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
});

Route::middleware(['auth', 'role:user'])->prefix('my-bookings')->name('user.bookings.')->group(function () {
    Route::get('/', [UserBookingController::class, 'index'])->name('index');
    Route::post('/', [UserBookingController::class, 'store'])->name('store');
    Route::get('/search', [UserBookingController::class, 'search'])->name('search');
    Route::patch('/{booking}/cancel', [UserBookingController::class, 'cancel'])->name('cancel');
    Route::get('/{booking}', [UserBookingController::class, 'show'])->name('show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/preferences', [PreferenceController::class, 'edit'])->name('preferences.edit');
    Route::patch('/preferences', [PreferenceController::class, 'update'])->name('preferences.update');
});

require __DIR__.'/auth.php';
