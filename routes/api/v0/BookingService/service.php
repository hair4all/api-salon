<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\ServiceController;

// Route::group([], function () {
//     Route::get('/services', [ServiceController::class, 'index']);
//     Route::get('/services/show/{id}', [ServiceController::class, 'show']);
//     Route::post('/services', [ServiceController::class, 'store']);
//     Route::put('/services/edit/{id}', [ServiceController::class, 'update']);
//     Route::delete('/services/delete/{id}', [ServiceController::class, 'destroy']);
// });

Route::resource('services', ServiceController::class)->names([
    'index' => 'services.index',
    'show' => 'services.show',
    'store' => 'services.store',
    'update' => 'services.update',
    'destroy' => 'services.destroy',
]);