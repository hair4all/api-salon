<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;

Route::group([], function () {
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/show/{id}', [CityController::class, 'show']);
    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/edit/{id}', [CityController::class, 'update']);
    Route::delete('/cities/delete/{id}', [CityController::class, 'destroy']);
});