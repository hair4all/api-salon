<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Product\OrderTrackingController;
Route::group([], function () {
    Route::get('/order-tracking', [OrderTrackingController::class, 'index']);
    Route::get('/order-tracking/show/{id}', [OrderTrackingController::class, 'show']);
    Route::post('/order-tracking', [OrderTrackingController::class, 'store']);
    Route::post('/order-tracking/edit/{id}', [OrderTrackingController::class, 'update']);
    Route::delete('/order-tracking/delete/{id}', [OrderTrackingController::class, 'destroy']);
});
// Route::resource('order-tracking', OrderTrackingController::class)->names([
//     'index' => 'order-tracking.index',
//     'show' => 'order-tracking.show',
//     'store' => 'order-tracking.store',
//     'update' => 'order-tracking.update',
//     'destroy' => 'order-tracking.destroy',
// ]);