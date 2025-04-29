<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\TransactionController;

Route::group([], function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/show/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::post('/transactions/edit/{id}', [TransactionController::class, 'update']);
    Route::delete('/transactions/delete/{id}', [TransactionController::class, 'destroy']);
});