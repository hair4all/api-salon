<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //
    protected $table = 'districts';
    protected $fillable = [
        'id',
        'name',
        'code',
        'city_id',
        'is_deleted',
    ];
}
