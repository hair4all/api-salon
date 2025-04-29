<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingService\ServiceInventoriesController;

Route::group([], function () {
    Route::get('/service-inventories', [ServiceInventoriesController::class, 'index']);
    Route::get('/service-inventories/show/{id}', [ServiceInventoriesController::class, 'show']);
    Route::post('/service-inventories', [ServiceInventoriesController::class, 'store']);
    Route::post('/service-inventories/edit/{id}', [ServiceInventoriesController::class, 'update']);
    Route::delete('/service-inventories/delete/{id}', [ServiceInventoriesController::class, 'destroy']);
});

// Route::resource('service-inventories', ServiceInventoriesController::class)->names([
//     'index' => 'service-inventories.index',
//     'show' => 'service-inventories.show',
//     'store' => 'service-inventories.store',
//     'update' => 'service-inventories.update',
//     'destroy' => 'service-inventories.destroy',
// ]);