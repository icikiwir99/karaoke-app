<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\OwnerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Manajemen & Reservasi Karaoke
|--------------------------------------------------------------------------
*/

// Public / Customer Area
Route::get('/', [CustomerController::class, 'index'])->name('home');
Route::get('/rooms', [CustomerController::class, 'rooms'])->name('customer.rooms');
Route::get('/menu', [CustomerController::class, 'menu'])->name('customer.menu');
Route::get('/song-finder', [CustomerController::class, 'songFinder'])->name('customer.songs');
Route::get('/promos', [CustomerController::class, 'promos'])->name('customer.promos');
Route::get('/reservation', [CustomerController::class, 'reservationForm'])->name('customer.reservation');
Route::post('/reservation', [CustomerController::class, 'storeReservation'])->name('customer.reservation.store');
Route::get('/reservation/success/{id}', [CustomerController::class, 'reservationSuccess'])->name('customer.reservation.success');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Kasir / Operational Area (Protected by role:kasir - owner also has supervisor access)
Route::prefix('kasir')->name('kasir.')->middleware(['role:kasir'])->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    
    // Room Monitoring Grid & Quick Actions
    Route::get('/rooms', [KasirController::class, 'roomsGrid'])->name('rooms');
    Route::post('/rooms/checkin', [KasirController::class, 'checkInWalkIn'])->name('rooms.checkin');
    Route::post('/rooms/order', [KasirController::class, 'orderToRoom'])->name('rooms.order');
    Route::post('/rooms/assign-lc', [KasirController::class, 'assignCompanionToRoom'])->name('rooms.assignLc');
    Route::post('/rooms/extend', [KasirController::class, 'extendRoom'])->name('rooms.extend');
    Route::post('/rooms/{id}/status', [KasirController::class, 'updateRoomStatus'])->name('rooms.status');

    // Live Companion Tracker
    Route::get('/companions', [KasirController::class, 'companionTracker'])->name('companions');
    Route::post('/companions/{id}/status', [KasirController::class, 'updateCompanionStatus'])->name('companions.status');

    // Reservation Management
    Route::get('/reservations', [KasirController::class, 'reservations'])->name('reservations');
    Route::post('/reservations/{id}/confirm', [KasirController::class, 'confirmReservation'])->name('reservations.confirm');
    Route::post('/reservations/{id}/cancel', [KasirController::class, 'cancelReservation'])->name('reservations.cancel');

    // POS & Checkout
    Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
    Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');
    Route::get('/receipt/{id}', [KasirController::class, 'thermalReceipt'])->name('thermalReceipt');

    // Shift Management
    Route::get('/shifts', [KasirController::class, 'shifts'])->name('shifts');
    Route::post('/shifts/start', [KasirController::class, 'startShift'])->name('shifts.start');
    Route::post('/shifts/movement', [KasirController::class, 'recordCashMovement'])->name('shifts.movement');
    Route::post('/shifts/{id}/close', [KasirController::class, 'closeShift'])->name('shifts.close');
});

// Owner / Executive & Override Area (Protected by role:owner)
Route::prefix('owner')->name('owner.')->middleware(['role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

    // Master Data F&B & Miras (Stock Control & Alert)
    Route::get('/products', [OwnerController::class, 'products'])->name('products');
    Route::post('/products', [OwnerController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [OwnerController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [OwnerController::class, 'deleteProduct'])->name('products.delete');

    // Master Data Rooms & Override (Hold/Block Room)
    Route::get('/rooms', [OwnerController::class, 'rooms'])->name('rooms');
    Route::post('/rooms', [OwnerController::class, 'storeRoom'])->name('rooms.store');
    Route::put('/rooms/{id}', [OwnerController::class, 'updateRoom'])->name('rooms.update');
    Route::post('/rooms/{id}/block', [OwnerController::class, 'toggleRoomBlock'])->name('rooms.block');

    // Master Data Packages (Bundling)
    Route::get('/packages', [OwnerController::class, 'packages'])->name('packages');
    Route::post('/packages', [OwnerController::class, 'storePackage'])->name('packages.store');
    Route::delete('/packages/{id}', [OwnerController::class, 'deletePackage'])->name('packages.delete');

    // Companions (LC) Master & Performance Report
    Route::get('/companions', [OwnerController::class, 'companions'])->name('companions');
    Route::post('/companions', [OwnerController::class, 'storeCompanion'])->name('companions.store');
    Route::put('/companions/{id}', [OwnerController::class, 'updateCompanion'])->name('companions.update');

    // User Management (Kasir & Staff)
    Route::get('/users', [OwnerController::class, 'users'])->name('users');
    Route::post('/users', [OwnerController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{id}', [OwnerController::class, 'updateUser'])->name('users.update');

    // Financial Reports & Product Margins (Alcohol & F&B)
    Route::get('/reports', [OwnerController::class, 'reports'])->name('reports');
    Route::get('/reports/export-csv', [OwnerController::class, 'exportCsv'])->name('reports.exportCsv');
});
