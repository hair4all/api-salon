<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ClientController;
Route::group([], function () {
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/show/{id}', [ClientController::class, 'show']);
    Route::post('/clients', [ClientController::class, 'store']);
    Route::post('/clients/edit/{id}', [ClientController::class, 'update']);
    Route::delete('/clients/delete/{id}', [ClientController::class, 'destroy']);
});