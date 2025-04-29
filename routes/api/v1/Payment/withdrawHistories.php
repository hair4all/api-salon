<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Payment\WithdrawHistoriesController;

Route::group([], function () {
    Route::get('/withdraw-history', [WithdrawHistoriesController::class, 'index']);
    Route::get('/withdraw-history/show/{id}', [WithdrawHistoriesController::class, 'show']);
    Route::post('/withdraw-history', [WithdrawHistoriesController::class, 'store']);
    Route::post('/withdraw-history/edit/{id}', [WithdrawHistoriesController::class, 'update']);
    Route::delete('/withdraw-history/delete/{id}', [WithdrawHistoriesController::class, 'destroy']);
});