<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topup_History extends Model
{
    //
    protected $table = 'topup__histories';
    protected $fillable = [
        'id',
        'client_id',
        'amount',
        'topup_date',
        'payment_method_id',
        'status',
        'is_deleted',
    ];
}
