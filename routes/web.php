<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authenticated users
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Customer
    Route::get('/menu', [FoodController::class, 'menu'])->name('menu.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin
    Route::middleware(['admin'])->group(function () {
        Route::get('/foods', [FoodController::class, 'index'])->name('foods.index');
        Route::get('/foods/create', [FoodController::class, 'create'])->name('foods.create');
        Route::post('/foods', [FoodController::class, 'store'])->name('foods.store');
        Route::get('/foods/{food}/edit', [FoodController::class, 'edit'])->name('foods.edit');
        Route::put('/foods/{food}', [FoodController::class, 'update'])->name('foods.update');
        Route::delete('/foods/{food}', [FoodController::class, 'destroy'])->name('foods.destroy');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    });

    // Kitchen
    Route::middleware(['kitchen'])->group(function () {
        Route::get('/kitchen/orders', [OrderController::class, 'kitchen'])->name('kitchen.orders');
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

require __DIR__.'/auth.php';