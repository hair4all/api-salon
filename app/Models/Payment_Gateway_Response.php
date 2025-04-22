<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_Gateway_Response extends Model
{
    //
    protected $table = 'payment__gateway__responses';
    protected $fillable = [
        'id',
        'transaction_id',
        'gateway',
        'metadata',
        'status',
        'is_deleted',
    ];
}
