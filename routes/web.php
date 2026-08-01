<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\WaiverController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Public join flow
Route::get('/join', [JoinController::class, 'show'])->name('join.show');
Route::post('/join', [JoinController::class, 'store'])->name('join.store');

// QR check-in (no auth required — token is the credential)
Route::get('/checkin/{token}', [CheckInController::class, 'token'])->name('checkin.token');

// Authenticated member routes
Route::middleware(['auth'])->group(function () {
    // Waiver
    Route::get('/waiver', [WaiverController::class, 'show'])->name('waiver.show');
    Route::post('/waiver', [WaiverController::class, 'accept'])->name('waiver.accept');

    // Member dashboard & payments
    Route::get('/member', [MemberController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/member/payments', [MemberController::class, 'payments'])->name('member.payments');

    // Self check-in
    Route::get('/checkin', [CheckInController::class, 'show'])->name('checkin.show');
    Route::post('/checkin', [CheckInController::class, 'store'])->name('checkin.store');

    // Stripe checkout
    Route::post('/stripe/checkout', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/members', [Admin\MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [Admin\MemberController::class, 'show'])->name('members.show');
    Route::post('/members/{member}/payment', [Admin\MemberController::class, 'recordPayment'])->name('members.payment');
    Route::post('/members/{member}/status', [Admin\MemberController::class, 'updateStatus'])->name('members.status');
    Route::post('/members/{member}/checkin', [Admin\MemberController::class, 'staffCheckIn'])->name('members.checkin');
    Route::post('/members/{member}/reminder', [Admin\MemberController::class, 'sendReminder'])->name('members.reminder');

    Route::middleware('admin')->group(function () {
        Route::get('/settings', [Admin\SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
