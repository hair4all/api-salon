<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Extension\RajaOngkirCallbackController;

Route::prefix('callback/rajaongkir')->group( function () {
    Route::get('/location', [RajaOngkirCallbackController::class, 'getDestination']);
    Route::get('/history-airwaybill', [RajaOngkirCallbackController::class, 'getHistoryAirwayBill']);
    Route::get('/order-detail', [RajaOngkirCallbackController::class, 'getOrderDetail']);
    Route::get('/shipping-cost', [RajaOngkirCallbackController::class, 'getShippingCost']);

    Route::post('/store', [RajaOngkirCallbackController::class, 'storeOrder']);
    Route::post('/order-pickup', [RajaOngkirCallbackController::class, 'OrderPickup']);
    Route::post('/print-resi', [RajaOngkirCallbackController::class, 'LabelOrder']);

    Route::put('/cancel', [RajaOngkirCallbackController::class, 'CancelOrder']);
});