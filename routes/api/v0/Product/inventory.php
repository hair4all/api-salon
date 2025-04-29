<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\InventoryController;

// Route::group([], function () {
//     Route::get('/inventories', [InventoryController::class, 'index']);
//     Route::get('/inventories/show/{id}', [InventoryController::class, 'show']);
//     Route::post('/inventories', [InventoryController::class, 'store']);
//     Route::put('/inventories/edit/{id}', [InventoryController::class, 'update']);
//     Route::delete('/inventories/delete/{id}', [InventoryController::class, 'destroy']);
// });

Route::resource('inventories', InventoryController::class)->names([
    'index' => 'inventories.index',
    'show' => 'inventories.show',
    'store' => 'inventories.store',
    'update' => 'inventories.update',
    'destroy' => 'inventories.destroy',
]);