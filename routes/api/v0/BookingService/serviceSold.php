<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\ServiceSoldController;

Route::group([], function () {
    Route::get('/service-sold', [ServiceSoldController::class, 'index']);
    Route::get('/service-sold/show/{id}', [ServiceSoldController::class, 'show']);
    Route::post('/service-sold', [ServiceSoldController::class, 'store']);
    Route::put('/service-sold/edit/{id}', [ServiceSoldController::class, 'update']);
    Route::delete('/service-sold/delete/{id}', [ServiceSoldController::class, 'destroy']);
});