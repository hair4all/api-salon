<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment_Method extends Model
{
    //
    protected $table = 'payment__methods';
    protected $fillable = [
        'id',
        'name',
        'type',
        'description',
        'is_deleted',
    ];
}
