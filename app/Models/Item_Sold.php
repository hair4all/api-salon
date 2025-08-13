<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item_Sold extends Model
{
    //
    protected $table = 'item__solds';
    protected $fillable = [
        'id',
        'order_id',
        'inventory_id',
        'quantity',
        'sold_date',
        'is_deleted',
    ];
}
