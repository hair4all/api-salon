<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\BookingServiceController;

Route::group([], function () {
    Route::get('/booking-service', [BookingServiceController::class, 'index']);
    Route::get('/booking-service/show/{id}', [BookingServiceController::class, 'show']);
    Route::post('/booking-service', [BookingServiceController::class, 'store']);
    Route::put('/booking-service/edit/{id}', [BookingServiceController::class, 'update']);
    Route::delete('/booking-service/delete/{id}', [BookingServiceController::class, 'destroy']);
});