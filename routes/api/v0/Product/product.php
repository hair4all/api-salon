<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Product\ProductController;

// Route::group([], function () {
//     Route::get('/products', [ProductController::class, 'index']);
//     Route::get('/products/show/{id}', [ProductController::class, 'show']);
//     Route::post('/products', [ProductController::class, 'store']);
//     Route::put('/products/edit/{id}', [ProductController::class, 'update']);
//     Route::delete('/products/delete/{id}', [ProductController::class, 'destroy']);
// });
Route::resource('products', ProductController::class)->names([
    'index' => 'products.index',
    'show' => 'products.show',
    'store' => 'products.store',
    'update' => 'products.update',
    'destroy' => 'products.destroy',
]);