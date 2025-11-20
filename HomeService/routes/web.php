<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Services Routes
Route::resource('services', ServiceController::class);
Route::post('/services/{service}/toggle-availability', [ServiceController::class, 'toggleAvailability'])
    ->name('services.toggle-availability');

// Orders Routes
Route::resource('orders', OrderController::class);
Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])
    ->name('orders.update-status');

// Reviews Routes
Route::get('/reviews/create/{order_id}', [ReviewController::class, 'create'])
    ->name('reviews.create');
Route::resource('reviews', ReviewController::class)->except(['create']);