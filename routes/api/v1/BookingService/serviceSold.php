<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\ServiceSoldController;

Route::group([], function () {
    Route::get('/service-sold', [ServiceSoldController::class, 'index']);
    Route::get('/service-sold/show/{id}', [ServiceSoldController::class, 'show']);
    Route::post('/service-sold', [ServiceSoldController::class, 'store']);
    Route::post('/service-sold/edit/{id}', [ServiceSoldController::class, 'update']);
    Route::delete('/service-sold/delete/{id}', [ServiceSoldController::class, 'destroy']);
});

// Route::resource('service-sold', ServiceSoldController::class)->names([
//     'index' => 'service-sold.index',
//     'show' => 'service-sold.show',
//     'store' => 'service-sold.store',
//     'update' => 'service-sold.update',
//     'destroy' => 'service-sold.destroy',
// ]);