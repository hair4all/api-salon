<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_Tokens extends Model
{
    //
    protected $table = 'payment_tokens';

    protected $fillable = [
        'token',
        'user_id',
        'expiry',
    ];

    protected $casts = [
        'expiry' => 'datetime',
    ];
}
