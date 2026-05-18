<?php

use App\Http\Controllers\Admin\PurchaseOrderController as AdminPOController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Vendor\PurchaseOrderController as VendorPOController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/home', [HomeController::class, 'index'])->middleware('auth')->name('home');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.purchase-orders.index');
    })->name('dashboard');

    Route::resource('purchase-orders', AdminPOController::class)->only([
        'index', 'create', 'store', 'show',
    ]);

    Route::resource('vendors', AdminVendorController::class)->except([
        'show',
    ]);

    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('notifications/{notification}/read', [AdminNotificationController::class, 'read'])->name('notifications.read');
});

Route::middleware(['auth'])->prefix('vendor')->name('vendor.')->group(function () {
    Route::get('dashboard', [VendorPOController::class, 'index'])->name('dashboard');
    Route::get('purchase-orders/{purchaseOrder}', [VendorPOController::class, 'show'])->name('purchase-orders.show');
    Route::put('purchase-orders/{purchaseOrder}', [VendorPOController::class, 'update'])->name('purchase-orders.update');
});
