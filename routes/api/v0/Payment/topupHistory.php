<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\TopupHistoryController;

Route::group([], function () {
    Route::get('/topup-history', [TopupHistoryController::class, 'index']);
    Route::get('/topup-history/show/{id}', [TopupHistoryController::class, 'show']);
    Route::post('/topup-history', [TopupHistoryController::class, 'store']);
    Route::put('/topup-history/edit/{id}', [TopupHistoryController::class, 'update']);
    Route::delete('/topup-history/delete/{id}', [TopupHistoryController::class, 'destroy']);
});