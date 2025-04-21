<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';
    protected $fillable = [
        'id',
        'member_id',
        'position_id',
        'district_id',
        'city_id',
        'order_number',
        'cart_id',
        'shipping_address_id',
        'total_price',
        'courier',
        'shipping_cost',
        'status',
        'is_deleted',
    ];
}
