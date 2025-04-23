<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Product\OrderTrackingController;
Route::group([], function () {
    Route::get('/order-tracking', [OrderTrackingController::class, 'index']);
    Route::get('/order-tracking/show/{id}', [OrderTrackingController::class, 'show']);
    Route::post('/order-tracking', [OrderTrackingController::class, 'store']);
    Route::put('/order-tracking/edit/{id}', [OrderTrackingController::class, 'update']);
    Route::delete('/order-tracking/delete/{id}', [OrderTrackingController::class, 'destroy']);
});