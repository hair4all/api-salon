<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Include the routes for cities, districts, and provinces
require __DIR__ . '/v0/city.php';
require __DIR__ . '/v0/district.php';
require __DIR__ . '/v0/province.php';

// Include the routes for products, payments, bookings, and users
require __DIR__ . '/v0/Product/index.php';
require __DIR__ . '/v0/Payment/index.php';
require __DIR__ . '/v0/BookingService/index.php';
require __DIR__ . '/v0/User/index.php';

