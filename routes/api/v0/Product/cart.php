<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\CartController;

Route::group([], function () {
    Route::get('/carts', [CartController::class, 'index']);
    Route::get('/carts/show/{id}', [CartController::class, 'show']);
    Route::post('/carts', [CartController::class, 'store']);
    Route::put('/carts/edit/{id}', [CartController::class, 'update']);
    Route::delete('/carts/delete/{id}', [CartController::class, 'destroy']);
});