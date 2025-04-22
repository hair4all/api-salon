<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw_Histories extends Model
{
    //
    protected $table = 'withdraw__histories';
    protected $fillable = [
        'id',
        'withdraw_id',
        // 'inventory_id',
        'amount',
        'withdraw_date',
        'payment_method_id',
        'status',
        'is_deleted',
    ];
}
