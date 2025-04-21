<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_Tracking extends Model
{
    //
    protected $table = 'order__trackings';
    protected $fillable = [
        'id',
        'order_id',
        'tracking_number',
        'courier',
        'status',
        'shipping_address',
        'estimated_delivery_date',
        'is_deleted',
        'created_at',
        'updated_at',
    ];
}
