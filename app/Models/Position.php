<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    //
    protected $table = 'positions';
    protected $fillable = [
        'id',
        'name',
        'is_deleted',
    ];
}
