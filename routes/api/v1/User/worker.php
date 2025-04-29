<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\WorkerController;
Route::group([], function () {
    Route::get('/workers', [WorkerController::class, 'index']);
    Route::get('/workers/show/{id}', [WorkerController::class, 'show']);
    Route::post('/workers', [WorkerController::class, 'store']);
    Route::post('/workers/edit/{id}', [WorkerController::class, 'update']);
    Route::delete('/workers/delete/{id}', [WorkerController::class, 'destroy']);
});