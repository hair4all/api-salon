<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ItemSoldController;

Route::group([], function () {
    Route::get('/item-sold', [ItemSoldController::class, 'index']);
    Route::get('/item-sold/show/{id}', [ItemSoldController::class, 'show']);
    Route::post('/item-sold', [ItemSoldController::class, 'store']);
    Route::post('/item-sold/edit/{id}', [ItemSoldController::class, 'update']);
    Route::delete('/item-sold/delete/{id}', [ItemSoldController::class, 'destroy']);
});
// Route::resource('item-sold', ItemSoldController::class)->names([
//     'index' => 'itemsold.index',
//     'show' => 'itemsold.show',
//     'store' => 'itemsold.store',
//     'update' => 'itemsold.update',
//     'destroy' => 'itemsold.destroy',
// ]);