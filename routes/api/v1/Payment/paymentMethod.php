<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentMethodController;

Route::group([], function () {
    Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('/payment-methods/show/{id}', [PaymentMethodController::class, 'show']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
    Route::post('/payment-methods/edit/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('/payment-methods/delete/{id}', [PaymentMethodController::class, 'destroy']);
});