<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //
    protected $table = 'carts';
    protected $fillable = [
        'id',
        'client_id',
        'product_id',
        'quantity',
        'status',
        'is_deleted',
    ];
}
