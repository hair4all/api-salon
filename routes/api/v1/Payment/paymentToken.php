<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentTokensController;


Route::group([], function () {
    Route::get('/payment-tokens', [PaymentTokensController::class, 'index']);
    Route::post('/payment-tokens/store', [PaymentTokensController::class, 'store']);
    Route::post('/payment-tokens/generate', [PaymentTokensController::class, 'generate']);
    Route::post('/payment-tokens/edit/{id}', [PaymentTokensController::class, 'update']);
    Route::delete('/payment-tokens/delete/{id}', [PaymentTokensController::class, 'destroy']);
});

