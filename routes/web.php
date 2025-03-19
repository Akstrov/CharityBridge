<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorDashboardController;
use App\Http\Controllers\CharityDashboardController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

// Charity Dashboard
Route::get('/charity/dashboard', [CharityDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:charity'])
    ->name('charity.dashboard');

Route::get('/charity/claims', [CharityDashboardController::class, 'claims'])
    ->middleware(['auth', 'verified', 'role:charity'])
    ->name('charity.claims.index');

Route::get('/charity/claims/{claim}', [CharityDashboardController::class, 'show'])
    ->middleware(['auth', 'verified', 'role:charity'])
    ->name('charity.claims.show');

Route::delete('/charity/claims/{claim}', [CharityDashboardController::class, 'destroy'])
    ->middleware(['auth', 'verified', 'role:charity'])
    ->name('charity.claims.destroy');

// Add claim route
Route::post('/donations/{donation}/claim', [ClaimController::class, 'store'])
    ->middleware(['auth', 'verified', 'role:charity'])
    ->name('claims.store');

// Donor Dashboard
Route::get('/donor/dashboard', [DonorDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:donor'])
    ->name('donor.dashboard');

Route::middleware(['auth', 'verified', 'role:donor'])->prefix('donor')->name('donor.')->group(function () {
    Route::get('/my-donations', [DonationController::class, 'myDonations'])
        ->name('my-donations');
    Route::get('/donations/create', [DonationController::class, 'create'])
        ->name('donations.create');
    Route::post('/donations', [DonationController::class, 'store'])
        ->name('donations.store');
    Route::get('/donations/{donation}/edit', [DonationController::class, 'edit'])
        ->name('donations.edit');
    Route::put('/donations/{donation}', [DonationController::class, 'update'])
        ->name('donations.update');
    Route::delete('/donations/{donation}', [DonationController::class, 'destroy'])
        ->name('donations.destroy');
    // Route::get('/claims/{claim}/messages', [App\Http\Controllers\Donor\MessageController::class, 'index'])->name('messages.index');
    // Route::post('/claims/{claim}/messages', [App\Http\Controllers\Donor\MessageController::class, 'store'])->name('messages.store');
    // Route::post('/claims/{claim}/messages/read', [App\Http\Controllers\Donor\MessageController::class, 'markAsRead'])->name('messages.read');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
    Route::get('/claims/{claim}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/claims/{claim}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
