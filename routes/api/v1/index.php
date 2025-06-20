<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Include the routes for authentication

Route::prefix('auth')->group(function () {
    require __DIR__ . '/Auth/index.php';
    
});

// Include the routes for cities, districts, and provinces
// require __DIR__ . '/city.php';
// require __DIR__ . '/district.php';
// require __DIR__ . '/province.php';
Route::prefix('client')->group(function () {
    require __DIR__ . '/Product/index.php';
    require __DIR__ . '/Payment/index.php';
    require __DIR__ . '/BookingService/index.php';
    require __DIR__ . '/User/index.php';
    require __DIR__ . '/branch.php';
    
    // Include the routes for Midtrans Callback
    require __DIR__ . '/MidtransCallback/index.php';

});

Route::prefix('admin')->group(function () {
    require __DIR__ . '/Product/index.php';
    require __DIR__ . '/Payment/index.php';
    require __DIR__ . '/BookingService/index.php';
    require __DIR__ . '/User/index.php';
    require __DIR__ . '/branch.php';
});


// Include the routes for products, payments, bookings, and users

// Include the routes for RajaOngkir Callback
require __DIR__ . '/RajaOngkirCallback/index.php';

// Include the routes for Midtrans Callback
require __DIR__ . '/MidtransCallback/index.php';

// Include the routes for everything
require __DIR__ . '/Product/index.php';
require __DIR__ . '/Payment/index.php';
require __DIR__ . '/BookingService/index.php';
require __DIR__ . '/User/index.php';
require __DIR__ . '/branch.php';