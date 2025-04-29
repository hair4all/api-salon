<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\PositionController;

Route::group([], function () {
    Route::get('/positions', [PositionController::class, 'index']);
    Route::get('/positions/show/{id}', [PositionController::class, 'show']);
    Route::post('/positions', [PositionController::class, 'store']);
    Route::post('/positions/edit/{id}', [PositionController::class, 'update']);
    Route::delete('/positions/delete/{id}', [PositionController::class, 'destroy']);
});