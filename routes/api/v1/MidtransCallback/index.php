<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Extension\Payment\MidtransCallbackController;

Route::prefix('payments')->group(function () {
    Route::post('snap-token', [MidtransCallbackController::class, 'getSnapToken']);
    Route::post('notification', [MidtransCallbackController::class, 'handleNotification']);
});