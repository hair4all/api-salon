<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\BookingController;

Route::group([], function () {
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/show/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::post('/bookings/edit/{id}', [BookingController::class, 'update']);
    Route::delete('/bookings/delete/{id}', [BookingController::class, 'destroy']);
});

// Route::resource('bookings', BookingController::class)->names([
//     'index' => 'bookings.index',
//     'show' => 'bookings.show',
//     'store' => 'bookings.store',
//     'update' => 'bookings.update',
//     'destroy' => 'bookings.destroy',
// ]);