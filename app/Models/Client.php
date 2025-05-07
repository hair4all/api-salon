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
        'address',
        'saldo',
        'points',
        'is_deleted',
    ];
}
