<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProvinceController;

Route::group([], function () {
    Route::get('/provinces', [ProvinceController::class, 'index']);
    Route::get('/provinces/show/{id}', [ProvinceController::class, 'show']);
    Route::post('/provinces', [ProvinceController::class, 'store']);
    Route::put('/provinces/edit/{id}', [ProvinceController::class, 'update']);
    Route::delete('/provinces/delete/{id}', [ProvinceController::class, 'destroy']);
});