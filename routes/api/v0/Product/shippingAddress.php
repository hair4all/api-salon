<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ShippingAddressController;

Route::group([], function () {
    Route::get('/shipping-addresses', [ShippingAddressController::class, 'index']);
    Route::get('/shipping-addresses/show/{id}', [ShippingAddressController::class, 'show']);
    Route::post('/shipping-addresses', [ShippingAddressController::class, 'store']);
    Route::put('/shipping-addresses/edit/{id}', [ShippingAddressController::class, 'update']);
    Route::delete('/shipping-addresses/delete/{id}', [ShippingAddressController::class, 'destroy']);
});