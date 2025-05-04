<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::group(['prefix' => 'client'], function () {
    Route::post('/login', [AuthController::class, 'clientLogin']);
    Route::post('/register', [AuthController::class, 'clientRegister']);
    // Route::get('/user', [AuthController::class, 'user']);

    Route::post('/google/login', [AuthController::class, 'loginClientWithGoogle']);
});

Route::group(['prefix' => 'worker'], function () {
    Route::post('/login', [AuthController::class, 'workerLogin']);
    Route::post('/register', [AuthController::class, 'workerRegister']);
    // Route::get('/user', [AuthController::class, 'user']);

    // Route::post('/google/login', [AuthController::class, 'loginWorkerWithGoogle']);
});