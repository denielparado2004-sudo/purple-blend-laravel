<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoreController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureCustomer;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product}', [StoreController::class, 'show'])->name('products.show');
Route::get('/cart', [StoreController::class, 'cart'])->name('cart');
Route::post('/cart/items', [StoreController::class, 'addToCart'])->name('cart.add');
Route::patch('/cart/items/{product}', [StoreController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/items/{product}', [StoreController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/login', [StoreController::class, 'login'])->name('login');
Route::post('/login', [StoreController::class, 'authenticate'])->name('login.submit');
Route::get('/register', [StoreController::class, 'register'])->name('register');
Route::post('/register', [StoreController::class, 'storeCustomer'])->name('register.submit');
Route::get('/forgot-password', [StoreController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [StoreController::class, 'emailPasswordReset'])->middleware('throttle:6,1')->name('password.email');
Route::get('/reset-password/{token}', [StoreController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [StoreController::class, 'resetPassword'])->middleware('throttle:6,1')->name('password.update');
Route::post('/logout', [StoreController::class, 'logout'])->name('logout');

Route::middleware(EnsureCustomer::class)->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('home');    Route::get('/checkout', [StoreController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [StoreController::class, 'placeOrder'])->name('orders.store');
    Route::get('/orders', [StoreController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [StoreController::class, 'order'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [StoreController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/account', [StoreController::class, 'account'])->name('account');
    Route::patch('/account', [StoreController::class, 'updateAccount'])->name('account.update');
    Route::patch('/account/password', [StoreController::class, 'updatePassword'])->name('account.password');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::post('/login', [AdminController::class, 'authenticate'])->name('login.submit');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [AdminController::class, 'products'])->name('products.index');
        Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::patch('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.destroy');
        Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory.index');
        Route::patch('/inventory/{product}', [AdminController::class, 'updateInventory'])->name('inventory.update');
        Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
        Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
        Route::get('/customers', [AdminController::class, 'customers'])->name('customers.index');
        Route::get('/sales', [AdminController::class, 'sales'])->name('sales');
        Route::get('/accounts', [AdminController::class, 'accounts'])->name('accounts.index');
        Route::post('/accounts', [AdminController::class, 'storeAdmin'])->name('accounts.store');
        Route::patch('/accounts/{admin}', [AdminController::class, 'updateAdmin'])->name('accounts.update');
        Route::get('/reports', fn () => redirect()->route('admin.sales'))->name('reports');
    });
});
