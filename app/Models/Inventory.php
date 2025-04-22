<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    //
    protected $table = 'inventories';
    protected $fillable = [
        'id',
        'name',
        'image',
        'branch_id',
        'category_id',
        'price',
        'stock',
        'is_deleted',
    ];
}
