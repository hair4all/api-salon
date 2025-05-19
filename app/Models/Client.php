<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $table = 'clients';
    protected $fillable = [
        // 'member_id',
        'name',
        'email',
        'phone',
        'password',
        'address',
        'address_id',
        'saldo',
        'points',
        'is_deleted',
    ];
}
