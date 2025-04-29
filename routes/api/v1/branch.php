<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;

Route::group([], function () {
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/show/{id}', [BranchController::class, 'show']);
    Route::post('/branches', [BranchController::class, 'store']);
    Route::post('/branches/edit/{id}', [BranchController::class, 'update']);
    Route::delete('/branches/delete/{id}', [BranchController::class, 'destroy']);
});