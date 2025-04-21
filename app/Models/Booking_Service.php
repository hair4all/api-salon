<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking_Service extends Model
{
    //
    protected $table = 'booking__services';
    protected $fillable = [
        'id',
        'client_id',
        'service_id',
        'branch_id',
        'price',
        'discount',
        'expiry_discount_date',
        'status',
        'is_deleted',
    ];
}
