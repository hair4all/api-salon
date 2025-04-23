<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DistrictController;

Route::group([], function () {
    Route::get('/districts', [DistrictController::class, 'index']);
    Route::get('/districts/show/{id}', [DistrictController::class, 'show']);
    Route::post('/districts', [DistrictController::class, 'store']);
    Route::put('/districts/edit/{id}', [DistrictController::class, 'update']);
    Route::delete('/districts/delete/{id}', [DistrictController::class, 'destroy']);
});