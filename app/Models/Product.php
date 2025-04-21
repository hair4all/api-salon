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
        'expiry_discount_date',
        'status',
        'is_deleted',
    ];
}
