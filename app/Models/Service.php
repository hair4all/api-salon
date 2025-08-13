<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $table = 'services';
    protected $fillable = [
        'id',
        'branch_id',
        'name',
        'image',
        'description',
        'price',
        'discount',
        'expiry_discount_date',
        'is_deleted',
    ];
}
