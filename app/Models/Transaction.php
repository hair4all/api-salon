<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $table = 'transactions';
    protected $fillable = [
        'id',
        'client_id',
        'worker_id',
        'transaction_date',
        'total_price',
        'payment_method_id',
        'is_deleted',
    ];
}
