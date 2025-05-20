<?php

namespace App\Service\Payment;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create a midtrans Snap transaction
     */
    public function createTransaction(array $params)
    {
        // $params should include:
        // transaction_details: ['order_id' => ..., 'gross_amount' => ...]
        // item_details: [[ 'id'=>..., 'price'=>..., 'quantity'=>..., 'name'=>... ]]
        // customer_details: ['first_name' => ..., 'email' => ..., 'phone' => ...]
        return Snap::createTransaction($params);
    }

    /**
     * Handle Midtrans webhook notifications
     */
    public function handleNotification(): Notification
    {
        return new Notification();
    }
}