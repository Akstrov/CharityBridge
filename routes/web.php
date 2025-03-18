<?php

use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

// Charity Dashboard
Route::get('/charity/dashboard', function () {
    return Inertia::render('Charity/Dashboard');
})->middleware(['auth', 'verified', 'role:charity'])->name('charity.dashboard');

// Donor Dashboard
Route::get('/donor/dashboard', [DonorDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:donor'])
    ->name('donor.dashboard');

Route::middleware(['auth', 'role:donor'])->group(function () {
    Route::get('/donor/my-donations', [DonationController::class, 'myDonations'])
        ->name('donations.my');
    Route::get('/donor/donations/create', [DonationController::class, 'create'])
        ->name('donations.create');
    Route::post('/donor/donations', [DonationController::class, 'store'])
        ->name('donations.store');
    Route::get('/donor/donations/{donation}/edit', [DonationController::class, 'edit'])
        ->name('donations.edit');
    Route::put('/donor/donations/{donation}', [DonationController::class, 'update'])
        ->name('donations.update');
    Route::delete('/donor/donations/{donation}', [DonationController::class, 'destroy'])
        ->name('donations.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::get('/donations/{donation}', [DonationController::class, 'show'])->name('donations.show');
});
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
