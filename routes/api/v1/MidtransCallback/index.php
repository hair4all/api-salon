<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Extension\Payment\MidtransCallbackController;

Route::prefix('payments')->group(function () {
    Route::post('snap-token', [MidtransCallbackController::class, 'getSnapToken']);
    // Setup the route for handling Midtrans notifications on Midtrans webpage di https://dashboard.sandbox.midtrans.com
    Route::post('notification', [MidtransCallbackController::class, 'handleNotification']);
});