<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\MemberController;
Route::group([], function () {
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/show/{id}', [MemberController::class, 'show']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::post('/members/edit/{id}', [MemberController::class, 'update']);
    Route::delete('/members/delete/{id}', [MemberController::class, 'destroy']);
});