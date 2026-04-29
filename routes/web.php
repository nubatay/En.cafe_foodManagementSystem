<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Table start URL (sets table in session, no auth required yet)
Route::get('/table/{id}/start', [OrderController::class, 'startTableSession'])->name('table.start');

// Table reset URL (clears table from session, no auth required)
Route::get('/table/reset', [OrderController::class, 'resetTableSession'])->name('table.reset');

// Authenticated users
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $role = trim(auth()->user()->role);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'kitchen') {
            return redirect()->route('kitchen.orders');
        }

        return redirect()->route('customer.home');
    })->middleware(['auth'])->name('dashboard');

    // Customer
    Route::get('/menu', [FoodController::class, 'menu'])->name('menu.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.my');

    Route::post('/proceed-to-counter', [OrderController::class, 'proceedToCounter'])->name('orders.proceedToCounter');

    Route::get('/customer/home', function () {
    return view('customer.home');
})->name('customer.home');

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

        Route::get('/admin/billing', [OrderController::class, 'billing'])->name('admin.billing');
        Route::get('/admin/billing/{table}/{session}', [OrderController::class, 'billingShow'])->name('admin.billing.show');
        Route::post('/admin/billing/{table}/{session}/pay', [OrderController::class, 'confirmPayment'])->name('admin.billing.pay');

        Route::get('/admin/dashboard', [OrderController::class, 'adminDashboard'])->name('admin.dashboard');
    });

    // Kitchen
    Route::middleware(['kitchen'])->group(function () {
        Route::get('/kitchen/orders', [OrderController::class, 'kitchen'])->name('kitchen.orders');
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

        Route::get('/kitchen/orders/data', [OrderController::class, 'kitchenData'])
    ->middleware(['auth', 'kitchen'])
    ->name('kitchen.data');
    });
});

require __DIR__.'/auth.php';