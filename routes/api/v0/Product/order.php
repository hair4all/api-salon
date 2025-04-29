<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\OrderController;
// Route::group([], function () {
//     Route::get('/orders', [OrderController::class, 'index']);
//     Route::get('/orders/show/{id}', [OrderController::class, 'show']);
//     Route::post('/orders', [OrderController::class, 'store']);
//     Route::put('/orders/edit/{id}', [OrderController::class, 'update']);
//     Route::delete('/orders/delete/{id}', [OrderController::class, 'destroy']);
// });
Route::resource('orders', OrderController::class)->names([
    'index' => 'orders.index',
    'show' => 'orders.show',
    'store' => 'orders.store',
    'update' => 'orders.update',
    'destroy' => 'orders.destroy',
]);