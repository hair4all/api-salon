<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\BookingServiceController;

Route::group([], function () {
    Route::get('/booking-service', [BookingServiceController::class, 'index']);
    Route::get('/booking-service/show/{id}', [BookingServiceController::class, 'show']);
    Route::post('/booking-service', [BookingServiceController::class, 'store']);
    Route::post('/booking-service/edit/{id}', [BookingServiceController::class, 'update']);
    Route::delete('/booking-service/delete/{id}', [BookingServiceController::class, 'destroy']);

    Route::post('/booking-service/checkout', [BookingServiceController::class, 'checkout']);
});

// Route::resource('booking-service', BookingServiceController::class)->names([
//     'index' => 'booking-service.index',
//     'show' => 'booking-service.show',
//     'store' => 'booking-service.store',
//     'update' => 'booking-service.update',
//     'destroy' => 'booking-service.destroy',
// ]);