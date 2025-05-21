<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'products';
    protected $fillable = [
        'id',
        'inventory_id',
        'category_id',
        'branch_id',
        'discount',
        'is_promoted',
        'expiry_discount_date',
        'points',
        'limit',
        'status',
        'is_deleted',
    ];
}
