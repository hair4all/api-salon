<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v0')->group(function () {
    Route::get('health', function () {
        return response()->json(['status' => 'ok']);
    });
    require __DIR__ . '/api/v1/index.php';

});