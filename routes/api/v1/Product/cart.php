<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\CartController;

Route::group([], function () {
    Route::get('/carts', [CartController::class, 'index']);
    Route::get('/carts/show/{id}', [CartController::class, 'show']);
    Route::post('/carts', [CartController::class, 'store']);
    Route::post('/carts/edit/{id}', [CartController::class, 'update']);
    Route::delete('/carts/delete/{id}', [CartController::class, 'destroy']);
});
// Route::resource('carts', CartController::class)->names([
//     'index' => 'carts.index',
//     'show' => 'carts.show',
//     'store' => 'carts.store',
//     'update' => 'carts.update',
//     'destroy' => 'carts.destroy',
// ]);