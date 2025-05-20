<?php

return [
    // Set your Midtrans Server Key
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    // Set your Midtrans Client Key
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    // Production or sandbox (development)
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // 3DSecure
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
];