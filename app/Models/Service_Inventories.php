<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service_Inventories extends Model
{
    //
    protected $table = 'service__inventories';
    protected $fillable = [
        'id',
        'service_id',
        'inventory_id',
        'quantity',
        'is_deleted',
    ];
}
