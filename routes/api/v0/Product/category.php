<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\CategoryController;

// Route::group([], function () {
//     Route::get('/categories', [CategoryController::class, 'index']);
//     Route::get('/categories/show/{id}', [CategoryController::class, 'show']);
//     Route::post('/categories', [CategoryController::class, 'store']);
//     Route::put('/categories/edit/{id}', [CategoryController::class, 'update']);
//     Route::delete('/categories/delete/{id}', [CategoryController::class, 'destroy']);
// });
Route::resource('categories', CategoryController::class)->names([
    'index' => 'categories.index',
    'show' => 'categories.show',
    'store' => 'categories.store',
    'update' => 'categories.update',
    'destroy' => 'categories.destroy',
]);