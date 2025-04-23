<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ItemSoldController;

Route::group([], function () {
    Route::get('/itemsold', [ItemSoldController::class, 'index']);
    Route::get('/itemsold/show/{id}', [ItemSoldController::class, 'show']);
    Route::post('/itemsold', [ItemSoldController::class, 'store']);
    Route::put('/itemsold/edit/{id}', [ItemSoldController::class, 'update']);
    Route::delete('/itemsold/delete/{id}', [ItemSoldController::class, 'destroy']);
});