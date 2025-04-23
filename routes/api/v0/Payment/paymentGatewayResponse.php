<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Payment\PaymentGatewayResponseController;

Route::group([], function () {
    Route::get('/payment-gateway-response', [PaymentGatewayResponseController::class, 'index']);
    Route::get('/payment-gateway-response/show/{id}', [PaymentGatewayResponseController::class, 'show']);
    Route::post('/payment-gateway-response', [PaymentGatewayResponseController::class, 'store']);
    Route::put('/payment-gateway-response/edit/{id}', [PaymentGatewayResponseController::class, 'update']);
    Route::delete('/payment-gateway-response/delete/{id}', [PaymentGatewayResponseController::class, 'destroy']);
});