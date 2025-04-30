<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MemberController;
Route::group([], function () {
    Route::get('/users', [MemberController::class, 'index']);
    Route::get('/users/show/{id}', [MemberController::class, 'show']);
    Route::post('/users', [MemberController::class, 'store']);
    Route::post('/users/edit/{id}', [MemberController::class, 'update']);
    Route::delete('/users/delete/{id}', [MemberController::class, 'destroy']);
});