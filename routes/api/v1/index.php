<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Include the routes for authentication
require __DIR__ . '/Auth/index.php';

// Include the routes for cities, districts, and provinces
require __DIR__ . '/city.php';
require __DIR__ . '/district.php';
require __DIR__ . '/province.php';
require __DIR__ . '/branch.php';

// Include the routes for products, payments, bookings, and users
require __DIR__ . '/Product/index.php';
require __DIR__ . '/Payment/index.php';
require __DIR__ . '/BookingService/index.php';
require __DIR__ . '/User/index.php';

