<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping_Address extends Model
{
    //
    protected $table = 'shipping__addresses';
    protected $fillable = [
        'id',
        'member_id',
        'recipient_name',
        'phone',
        'province_id',
        'city_id',
        'district_id',
        'address',
        'is_deleted',
    ];

}
